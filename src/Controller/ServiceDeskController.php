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

    
    /**
     * @Route("/servicedesk/registros")
     */
    public function importarRegistros(): ?Response 
    {
        $client = new Client(HttpClient::create(['verify_peer' => false, 'verify_host' => false]));
                
        //Login
        if(!$this->loadCookies($client)){
            echo "Hacemos login de nuevo \r\n";
            $this->login($client);
            $this->saveCookies($client);
        }
        
        $em = $this->getDoctrine()->getManager();
        
        $ticketsInfo = $this->getTickets($client, $this->getParameter('servicedesk.vistas.registros'));
        //Si no tenemos tickets -> Tal vez no estemos logueados... 
        if(empty($ticketsInfo)){
            $this->resetCookies(); 
            $this->login($client);
            $this->saveCookies($client);
            $ticketsInfo = $this->getTickets($client, $this->getParameter('servicedesk.vistas.registros'));
        }

        foreach($ticketsInfo as $ticketInfo){
            $ticketDB = $em->getRepository(SDTicket::class)->findOneBy(['sdId' => $ticketInfo['ticketId']]);
            $importaRegistrosHora = false;
            if(empty($ticketDB)){
                $ticketDB = new SDTicket();
                $ticketDB->setFechaImportacion(new \DateTime());
                $importaRegistrosHora = true;
            }
            if(!$importaRegistrosHora && $ticketDB->getSdEstado() !== $ticketInfo['estado']){
                $importaRegistrosHora = true;
                $ticketDB->setSdEstado($ticketInfo['estado']);
            }

            if(!$importaRegistrosHora && ($ticketDB->getFechaRevision() < new \DateTime() && !in_array($ticketDB->getSdEstado(), ['Pendiente de Cierre', 'Cerrado']))){
                $importaRegistrosHora = true;
            }
            $ticketDB->loadFromArray($ticketInfo);

            //Ojo con la categorización
            if($importaRegistrosHora){
                echo date('Ymdhis')." Revisamos ticket [".$ticketDB->getSdId().' - '.$ticketDB->getNombre()."]\r\n";
                $fechaRevision = new \DateTime(); 
                $fechaRevision->add(new \DateInterval('PT2H'));

                $ticketDB->setFechaRevision($fechaRevision);
                
                $registrosHoras =  $this->getRegistrosHoras($client, $ticketDB);
                foreach($registrosHoras as $registroHoras){
                    $registroHorasDB = $em->getRepository(SDRegistroTiempo::class)->findOneBy([
                        'ticket' => $ticketDB,
                        'inicio' => $registroHoras['inicio'],
                        'tecnico' => $registroHoras['tecnico']
                    ]);
                    if(empty($registroHorasDB)){
                        $registroHorasDB = new SDRegistroTiempo(); 
                        $registroHorasDB->setTicket($ticketDB);
                    }
                    $registroHorasDB->loadFromArray($registroHoras);
                    $em->persist($registroHorasDB);
                }
            }

            $em->persist($ticketDB);
            $em->flush(); 
        }
        $em->flush(); 
        die('Importados');
        return null;
    }

    /**
     * @Route("/servicedesk/categorizacion")
     */
    public function categorizacion(): ?Response
    {
        $client = new Client(HttpClient::create(['verify_peer' => false, 'verify_host' => false]));
                
        //Login
        if(!$this->loadCookies($client)){
            echo "Hacemos login de nuevo \r\n";
            $this->login($client);
            $this->saveCookies($client);
        }
        
        $em = $this->getDoctrine()->getManager();
        
        $ticketsInfo = $this->getTickets($client, $this->getParameter('servicedesk.vistas.categorizacion'));
        //Si no tenemos tickets -> Tal vez no estemos logueados... 
        if(empty($ticketsInfo)){
            $this->resetCookies(); 
            $this->login($client);
            $this->saveCookies($client);
            $ticketsInfo = $this->getTickets($client, $this->getParameter('servicedesk.vistas.categorizacion'));
        }

        foreach($ticketsInfo as $ticketInfo){
            $ticketDB = $em->getRepository(SDTicket::class)->findOneBy(['sdId' => $ticketInfo['ticketId']]);
            $revisaRegistrosHoras = empty($ticketDB) || empty($ticket->getRegistrosHoras()); //Si no tenemos ticket o si el ticket está en estado Abierto o Asignado (tanto en la DB como en el SD)
            if(empty($ticketDB)){
                $ticketDB = new SDTicket();
                $ticketDB->setFechaImportacion(new \DateTime());
                $revisaRegistrosHoras = true;
            }

            $ticketDB->loadFromArray($ticketInfo);
            
            //Vamos a revisar los registros de horas del ticket... 
            if($revisaRegistrosHoras){
                $registroHoras = $this->getRegistrosHoras($client, $ticketDB);
                if(empty($registroHoras)){
                    echo "Insertamos tiempo en ticket ".$ticketDB->getSdId()."[".$ticketDB->getNombre()."] \r\n";
                    $this->setRegistrosHora($client, $ticketDB);
                }
            }
            $em->persist($ticketDB);
        }
        $em->flush(); 
        
        return null;
    }

    /**
     * @Route("/servicedesk/test")
     */
    public function test(): ?Response
    {
        $client = new Client(HttpClient::create(['verify_peer' => false, 'verify_host' => false]));
                
        //Login
        if(!$this->loadCookies($client)){
            echo "Hacemos login de nuevo \r\n";
            $this->login($client);
            $this->saveCookies($client);
        }
        
        $em = $this->getDoctrine()->getManager();
        
        $ticketsInfo = $this->getTickets($client, $this->getParameter('servicedesk.vistas.categorizacion'));
        //Si no tenemos tickets -> Tal vez no estemos logueados... 
        if(empty($ticketsInfo)){
            $this->resetCookies(); 
            $this->login($client);
            $this->saveCookies($client);
            $ticketsInfo = $this->getTickets($client, $this->getParameter('servicedesk.vistas.categorizacion'));
        }

        foreach($ticketsInfo as $ticketInfo){
            $test = $this->getTicketDetail($client, $ticketInfo['ticketId']);
            dump($test);
            die();

        }
        $em->flush(); 
        
        return null;
    }



    public function login(Client $client){
        $crawler = $client->request('GET', $this->getParameter('servicedesk.domain'));
        $form = $crawler->selectButton('Login')->form();
        
        $crawler = $client->submit($form, ['j_username' => $this->getParameter('servicedesk.user'), 'j_password' => $this->getParameter('servicedesk.passwd')]);
        return $client;
    }
    
    public function getTickets(Client $client, $vistaId){
        $crawler = $client->request('GET', $this->getParameter('servicedesk.domain').'WOListView.do?viewName='.$vistaId);
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

    public function getTicketDetail(Client $client, $ticketId){
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
                $ticketInfo[$ticketArray[0]] = substr($ticketArray[1], 0, strlen($ticketArray[1]) - strlen($ticketArray[0]) -1);
            }
        }
        return $ticketInfo;
    }

    public function getRegistrosHoras(Client $client, SDTicket $ticket){
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

    public function setRegistrosHora(Client $client, SDTicket $ticket){
        $crawler = $client->request('GET', $this->getParameter('servicedesk.domain').'WorkLogAction.do?1614587285016&&createnew=worklog&SUBREQUEST=true&module=request&associatedEntity=request&scopeid=2&associatedEntityID='.$ticket->getSdId());
        $form = $crawler->selectButton('Guardar')->form();
        
        
        $tecnicos = [50132, 50131, 50132, 50131, 18319, 26402, 16293, 18621];
        $tecnicosNombre = ['david.lozano','jorge.cuesta','david.lozano', 'jorge.cuesta', 'david.rubio', 'juan.martinez', 'jesus.mata', 'daniel.vazquez'];
        $tecnicoId = rand(0, sizeof($tecnicos) -1);
        $descripciones = ['Categorización', 'Categorizamos ticket', 'ticket', 'Imputación.', 'categorizamos ticket', 'imputamos ticket.'];

        echo date('YmdHis')." - El ticket [".$ticket->getSdId()." - ".$ticket->getNombre()."] lo categoriza $tecnicosNombre[$tecnicoId] \r\n";

        $crawler = $client->submit($form, [
            'technicianid' => $tecnicos[$tecnicoId], 
            'timespenthrs' => 0, 
            'timespentmins' => 0,
            'description' => $descripciones[rand(0,sizeof($descripciones)-1)],
        ]);
    }

    public function resetCookies(){
        $cookieFilePath = $this->getParameter('proejct.path').$this->getParameter('proejct.path.cookies');
        file_put_contents($cookieFilePath, '');
    }

    public function saveCookies($client){
        $cookieFilePath = $this->getParameter('proejct.path').$this->getParameter('proejct.path.cookies');
        $cookieJar = $client->getCookieJar();
        $cookies = $cookieJar->all();
        if ($cookies) {
            file_put_contents($cookieFilePath, serialize($cookies));
        }
    }

    public function loadCookies($client){
        $cookieFilePath = $this->getParameter('proejct.path').$this->getParameter('proejct.path.cookies');
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


}

