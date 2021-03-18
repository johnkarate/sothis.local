<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;

use App\Entity\SDTicket;
use App\Entity\SDRegistroTiempo;

class ServiceDeskController extends AbstractController
{


    /***
     * 1) IMPORTACIÓN
     * 
     * - GET TICKETS VISTA (CO5)
     * -> Si DB -> No hacemos nada... 
     * -> No DB -> Persistimos y obtenemos registro de horas
     *      -> Obtenemos registroHoras y persistimos
     *      -> Si Cliente == MdE 
     *          -> Si registroHoras == 0 -> registramos registroHoras + persistimos
     *          -> Si !Categorizado -> Categorizamos + persistimos
     * 
     */

    public function importarTickets() {
        $em = $this->getDoctrine()->getManager();
        $client = $this->getClient();
                
        //Login
        if(!$this->loadCookies($client)){
            echo "Hacemos login de nuevo \r\n";
            $this->login($client);
            $this->saveCookies($client);
        }
        
        $ticketsInfo = $this->getTicketsVista($client, $this->getParameter('servicedesk.vistas.registros'));
        //Si no tenemos tickets -> Tal vez no estemos logueados... 
        if(empty($ticketsInfo)){
            $this->resetCookies(); 
            $this->login($client);
            $this->saveCookies($client);
            $ticketsInfo = $this->getTicketsVista($client, $this->getParameter('servicedesk.vistas.registros'));
        }
        if(empty($ticketsInfo)){
            //OJO: algo no funciona no funciona bien -> avisamos de que algo no funciona
            echo date('YmdHis').' - error - algo no funciona bien: 0 tickets en la vista '.$this->getParameter('servicedesk.vistas.registros')."\r\n";
            return null;
        }

        foreach($ticketsInfo as $ticketInfo){
            $ticketDB = $em->getRepository(SDTicket::class)->findOneBy(['sdId' => $ticketInfo['ticketId']]);
            $ticketCreado = false;
            if(empty($ticketDB)){
                $ticketDB = new SDTicket();
                $ticketDB->setFechaImportacion(new \DateTime());
                $ticketCreado = true;
            }
            $ticketDB->loadFromArray($ticketInfo);

            //Calcumamos la fecha de revisión del ticket
            $ticketDB->setFechaRevision($ticketDB->calcFechaRevision());

            //Obtenemos los detalles del ticket... 
            // -> PTE

            //Guardamos el ticket
            $em->persist($ticketDB);

            //Obtenemos y persistimos los registros de horas que tenemos... 
            if($ticketCreado){
                echo date('YmdHis')." - Creamos ticket ".$ticketDB->getNombre(). ' ['.$ticketDB->getSdId().' - '.$ticketDB->getSdSitio()."] \r\n";
                $registroHoras = $this->getRegistrosHoras($client, $ticketDB);
                foreach($registroHoras as $registroHora){
                    //Importamos registroHoras... (si hay alguno, es nuevo, porque no tenemos ticket...)
                    $registroTiempo = new SDRegistroTiempo(); 
                    $registroTiempo->loadFromArray($registroHora);
                    
                    $ticketDB->addRegistrosTiempo($registroTiempo);
    
                    $em->persist($registroTiempo);
                }
    
                //Si el cliente es MdE -> creamos registro tiempo y categorizamos (si procede)
                if($ticketDB->isClienteMdE()){
                    //Revisaremos el ticket cada hora...
                    $fechaRevision = new \DateTime(); 
                    $fechaRevision->add(new \DateInterval("PT1H"));
                    $ticketDB->setFechaRevision($fechaRevision);
    
                    //Si no tenemos registros de horas, creamos uno... 
                    if(empty($registroHoras)){
                        $this->insertaRegistroTrabajoPrimeraRespuesta($client, $ticketDB);
                    }
    
                    //Categorizamos (si procede)
                    if((empty($ticketDB->getSdCategoria()) || $ticketDB->getSdCategoria() == 'No Asignado')){
                        echo date('YmdHis')." - Categorizamos ticket ".$ticketDB->getNombre(). ' ['.$ticketDB->getSdId().' - '.$ticketDB->getSdSitio()."] \r\n";
                        $this->categorizaTicket($client, $ticketDB);
                    }
    
                }    
            }
            $em->flush(); 
        }
        return null;
    }

    public function comprobarTickets(){
        $em = $this->getDoctrine()->getManager();
        $client = $this->getClient();

                        
        //Login
        if(!$this->loadCookies($client)){
            echo "Hacemos login de nuevo \r\n";
            $this->login($client);
            $this->saveCookies($client);
        }
        
        $ticketsInfo = $this->getTicketsVista($client, $this->getParameter('servicedesk.vistas.registros'));
        //Si no tenemos tickets -> Tal vez no estemos logueados... 
        if(empty($ticketsInfo)){
            $this->resetCookies(); 
            $this->login($client);
            $this->saveCookies($client);
            $ticketsInfo = $this->getTicketsVista($client, $this->getParameter('servicedesk.vistas.registros'));
        }

        $ticketsRevision = $em->getRepository(SDTicket::class)->findPtesRevisar(); 
        $checkMaxNumTickets = 100;
        $numTickets = 0;
        foreach($ticketsRevision as $ticket){
            if($numTickets < $checkMaxNumTickets){
                $ticketInfo = $this->getTicketDetail($client, $ticket->getSdId());

                echo "Comprobamos ".$ticket->getNombre().'['.$ticket->getSdId().']'."\r\n";
                //Actualizamos datos del ticket
                if(empty($ticketInfo['Categoría'])){
                    echo "Este ticket no tiene información disponible.";
                    $ticket->setSdEstado('Error')
                        ->setFechaRevision(new \DateTime('2030-01-01 00:00:00'));
                } else {
                    $ticket->loadFromDetailsArray($ticketInfo);
                }
                

                //Actualizamos registros de horas
                $registros = $this->getRegistrosHoras($client, $ticket);

                foreach($registros as $registroInfo){
                    $registroDB = $em->getRepository(SDRegistroTiempo::class)->findOneBy([
                        'ticket' => $ticket,
                        'tecnico' => $registroInfo['tecnico'],
                        'inicio' => $registroInfo['inicio']
                    ]);
                    if(empty($registroDB)){
                        $registroDB = new SDRegistroTiempo(); 
                        $ticket->addRegistrosTiempo($registroDB);
                    }
                    $registroDB->loadFromArray($registroInfo);

                    $em->persist($registroDB);
                }

                

                //Seteamos nueva revisión
                $ticket->setFechaRevision($ticket->calcFechaRevision());
                $em->persist($ticket);
                $em->flush();
                $numTickets++;
            }
        }
        
        
    }

    /**
     * OBTENEMOS LOS TICKETS QUE TENEMOS EN UNA VISTA... 
     */
    private function getTicketsVista(Client $client, $vistaId){
        $url = $this->getParameter('servicedesk.domain').'WOListView.do?viewName='.$vistaId;
        $crawler = $client->request('GET', $url);
        $tickets = [];
        
        $ticketsTable = $crawler->filter('table#RequestsView_TABLE')->filter('tr')->each(function ($tr, $i) {
            return $tr->filter('td')->each(function ($td, $i) {
                return trim($td->text());
            });
        });
        
        foreach($ticketsTable as $ticketsRow){
            if(sizeof($ticketsRow) == 20){
                $tickets[] = [
                    'ticketId' => $ticketsRow[5],
                    'asunto' => $ticketsRow[6],
                    'solicitante' => $ticketsRow[7],
                    'asignadoA' => $ticketsRow[8],
                    'vencimiento' => $ticketsRow[9],
                    'estado' => $ticketsRow[10],
                    'creacion' => $ticketsRow[11],
                    'site' => $ticketsRow[12],
                    'grupo' => $ticketsRow[16],
                    'cuenta' => $ticketsRow[19],
                ];
            }
        }
        return $tickets;
    }

    private function getTicketDetail(Client $client, $ticketId){
        $crawler = $client->request('GET', $this->getParameter('servicedesk.domain').'WorkOrder.do?woMode=viewWO&fromListView=true&woID='.$ticketId);
       
        //Detalles de la solicitud
        $ticketTableInfo = $crawler->filter('div#ProDetails')->filter('div.rows')->each(function ($tr, $i) {
            return $tr->filter('td')->each(function ($td, $i) {
                return trim($td->text());
            });
        });
        $ticketInfo = [];
        foreach($ticketTableInfo as $ticketArray){
            if(sizeof($ticketArray) == 2){
                $key = $ticketArray[0];

                if($key == 'Estado'){
                    $ticketInfo[$key] = substr($ticketArray[1], 0, strpos($ticketArray[1], 'function') -1);
                } elseif($key == 'Activo(s)'){
                    $ticketInfo[$key] = 'N/A';
                } elseif($key == 'Técnico'){
                    $ticketInfo[$key] = substr($ticketArray[1], 0, strpos($ticketArray[1], 'parent') -1);
                } else {
                    $ticketInfo[$key] = substr($ticketArray[1], 0, strlen($ticketArray[1]) - strlen($ticketArray[0]) -1);
                }
            }
        }
        return $ticketInfo;
    }

    /**
     * Gestión de REGISTROS DE HORAS
     */

    private function insertaRegistroTrabajoPrimeraRespuesta(Client $client, SDTicket $ticket){
        $crawler = $client->request('GET', $this->getParameter('servicedesk.domain').'WorkLogAction.do?createnew=worklog&SUBREQUEST=true&module=request&associatedEntity=request&scopeid=2&associatedEntityID='.$ticket->getSdId());
        $form = $crawler->selectButton('Guardar')->form();
        
        $tecnicos = [50132, 50131, 50132, 50131, 18319, 16293, 18621, 72128];
        $tecnicosNombre = ['david.lozano','jorge.cuesta','david.lozano', 'jorge.cuesta', 'david.rubio', 'jesus.mata', 'daniel.vazquez', 'sebastian.rosado'];
        $tecnicoId = rand(0, sizeof($tecnicos) -1);
        $descripciones = ['Categorización', 'Categorizamos ticket', 'ticket', 'Imputación.', 'categorizamos ticket', 'imputamos ticket.'];
        $descripcionesId = rand(0,sizeof($descripciones) -1);

        echo date('YmdHis')." - El ticket [".$ticket->getSdId()." - ".$ticket->getNombre()." - ".$ticket->getSdSitio()."] lo categoriza $tecnicosNombre[$tecnicoId] ($descripciones[$descripcionesId])\r\n";

        $crawler = $client->submit($form, [
            'technicianid' => $tecnicos[$tecnicoId], 
            'timespenthrs' => 0, 
            'timespentmins' => 0,
            'description' => $descripciones[$descripcionesId],
        ]);
    }

    private function getRegistrosHoras(Client $client, SDTicket $ticket){
        $crawler = $client->request('GET', $this->getParameter('servicedesk.domain').'TaskDefAction.do?SUBREQUEST=true&from=request&module=request&associatedEntityID='.$ticket->getSdId());
        //WorkLogListView_TABLE
        $registrosHoras = $crawler->filter('table#WorkLogListView_TABLE.tableComponent')->filter('tr')->each(function ($tr, $i) {
            return $tr->filter('td')->each(function ($td, $i) {
                return trim($td->text());
            });
        });

        $registrosTiempo = [];
        foreach($registrosHoras as $registroHoras){
            if(sizeof($registroHoras) == 7){
                $tiempo = explode('-', str_replace('Min.', '', str_replace('Hor.', '-', $registroHoras[2])));
                $tiempo = intval($tiempo[0]) * 60 + intval($tiempo[1]);
                
                $registrosTiempo[] = [
                    'tecnico' => $registroHoras[1],
                    'tiempo' => $tiempo, //Procesar!
                    'inicio' => \DateTime::createFromFormat('d/m/Y h:i A', $registroHoras[4]),
                    'inicio_str' => $registroHoras[4],
                    'fin' => \DateTime::createFromFormat('d/m/Y h:i A', $registroHoras[5]),
                    'fin_str' => $registroHoras[5],
                    'descripcion' => $registroHoras[6]
                ];
            }
        }

        return $registrosTiempo;
    }
    

    /**
     * Gestión de COOKIES Y CONEXIONES
     */

    private function login(Client $client){
        $crawler = $client->request('GET', $this->getParameter('servicedesk.domain'));
        $form = $crawler->selectButton('Login')->form();
        
        $crawler = $client->submit($form, ['j_username' => $this->getParameter('servicedesk.user'), 'j_password' => $this->getParameter('servicedesk.passwd')]);
        return $client;
    }

    private function resetCookies(){
        $cookieFilePath = $this->getParameter('project.path').$this->getParameter('servicedesk.path.cookies');
        file_put_contents($cookieFilePath, '');
    }

    private function saveCookies($client){
        $cookieFilePath = $this->getParameter('project.path').$this->getParameter('servicedesk.path.cookies');
        $cookieJar = $client->getCookieJar();
        $cookies = $cookieJar->all();
        if ($cookies) {
            file_put_contents($cookieFilePath, serialize($cookies));
        }
    }

    private function loadCookies($client){
        $cookieFilePath = $this->getParameter('project.path').$this->getParameter('servicedesk.path.cookies');
        if (is_file($cookieFilePath)) {
            // Load cookies and populate browserkit's cookie jar
            $cookieJar = $client->getCookieJar();
            $cookies = unserialize(file_get_contents($cookieFilePath));
            foreach ($cookies as $cookie) {
                $cookieJar->set($cookie);
            }
            return true;
        }
        return false;
    }

    private function getClient(){
         return new Client(HttpClient::create([
            'verify_peer' => false, 
            'verify_host' => false,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36',
            ]
        ]));
    }


    /**
     * Gestión de CATEGORIZACIÓN DE TICKETS
     */
    public function calcInfoByTicket(SDTicket $ticket){
        $tecnicoOnline = 72128; // Sebastian Rosado
        $tecnicosLanzadera = [18319, 16293]; // Jesús Mata + David Rubio
        $tecnicosEDEM = [50132, 50131]; // David Lozano + Jorge Cuesta

        $ticketInfo = [
            'requestType' => 2, // Tipo de Solicitud = Petición de servicio 
            'status' => 302, // Estado = Asignado
            'modeID' => 1, //Email

            'category' => 8404, // Soporte Usuario - MdE
            'subCategory' => 11130, // Gestión Puesto de Trabajo
            'item' => 10926, // Otros

            'siteID' => 18362, // Lanzadera = 2107 || EDEM = 18362
            'group' => 18150, // CO5-N1-SoporteUsuario
            'technician' => 50132 // David Lozano
        ];

        if(strtolower($ticket->getSdSitio()) == 'edificio lanzadera'){
            $ticketInfo['siteID'] = 2107;
            $ticketInfo['technician'] = $tecnicosLanzadera[rand(0, sizeof($tecnicosLanzadera) -1)];
        } else {
            $ticketInfo['siteID'] = 18362;
            $ticketInfo['technician'] = $tecnicosEDEM[rand(0, sizeof($tecnicosEDEM) -1)];
        }

        //Revisar datos por defecto... 

        //Calculamos si es prestamo
        $isPrestamo = false;
        if($isPrestamo){
            $ticketInfo['category'] = 8404; // Soporte Usuario - MdE
            $ticketInfo['subCategory'] = 11128; //Gestión Activos
            $ticketInfo['item'] = 10897; //Alquiler equipo
            $ticketInfo['technician'] = $tecnicoOnline; //Sebastian Rosado
        }


        return $ticketInfo;
    }

    public function setInfoByTicket(Client $client, SDTicket $ticket, $ticketInfo){
        $crawler = $client->request('GET', $this->getParameter('servicedesk.domain').'WorkOrder.do?woMode=editWO&fromListView=true&fromPage=reqDetails&woID='.$ticket->getSdId());
        $form = $crawler->selectButton('Actualizar solicitud')->form();
        $form->disableValidation(); 

        $crawler = $client->submit($form, $ticketInfo);
    }

    public function categorizaTicket(Client $client, SDTicket $ticket){
        $this->setInfoByTicket($client, $ticket, $this->calcInfoByTicket($ticket));
    }

}

