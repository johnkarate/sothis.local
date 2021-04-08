<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;

use App\Controller\ConfigController;

use App\Entity\AdobeCategoria; 
use App\Entity\AdobeGrabacion; 
use App\Entity\AdobeReunion; 

class AdobeConnectController extends AbstractController {

    /**
     * @Route("/adobe/")
     */
    public function getUrlAdobe(){
        $em = $this->getDoctrine()->getManager();

        $grabacion = $em->getRepository(AdobeGrabacion::class)->findBy(['estado' => 'insertado'], ['prioridad' => 'ASC'], 1);
        $grabacion = $grabacion[0];
        $grabacion->setEstado('descargando');
        $grabacion->setFechaDescarga(new \DateTime());

        $em->persist($grabacion);
        $em->flush(); 


        $finGrabacion = new \DateTime(); 
        $finGrabacion->add(new \DateInterval('PT'.$grabacion->getDuracionSegundos().'S'));

        echo '<p>'.$grabacion->getNombre().'</p>';
        echo '<p><a href="'.$grabacion->getLinkDesconectado().'"> Hacer desconectado </a></p>';
        echo '<p>Fin descarga: '.$finGrabacion->format('H:i').'</p>';
        echo '<br /><br /><br />';
        echo '<script type="text/javascript">
        window.open("'.$grabacion->getLinkDesconectado().'", \'_blank\');
        </script>';

        dump($grabacion);
        die();
    }


    public function infoReuniones() 
    {
        $client = new Client(HttpClient::create(['verify_peer' => false, 'verify_host' => false]));
                
        //Login
        if(!$this->loadCookies($client)){
            echo "Hacemos login de nuevo \r\n";
            $this->login($client);
            $this->saveCookies($client);
        }
        
        $em = $this->getDoctrine()->getManager();
        
        $scoR = 2689963968;
        $info = $this->procesaSCOID($client, $scoR);
        if(empty($info['reuniones']) && empty($info['categorias'])){
            $this->resetCookies(); 
            $this->login($client);
            $this->saveCookies($client);
            $info = $this->procesaSCOID($client, $scoR);
        }
        
        dump($info);
        
        return $info;
    }

    public function procesaSCOID(Client $client, $scoId='2689963968', $prefix='/', AdobeCategoria $cat=null){
        $em = $this->getDoctrine()->getManager();

        $infoScoId = $this->getInfoReuniones($client, $scoId);
        //Procesamos las reuniones
        foreach($infoScoId['reuniones'] as $reunionScoId => $reunion){
            echo "Revisando reunion $reunionScoId \r\n";
            $grabacionesArray = $this->getInfoGrabaciones($client, $reunion['scoId']);
            $infoScoId['reuniones'][$reunionScoId]['grabaciones'] = $grabacionesArray;

            $reunionObj = $em->getRepository(AdobeReunion::class)->findOneBy(['scoId' => $reunion['scoId']]);
            if(empty($reunionObj)){
                $reunionObj = new AdobeReunion(); 
                $reunionObj->loadFromArray($reunion);
                if(!empty($cat)){
                    $reunionObj->setCategoria($cat);
                }
            }
            foreach($grabacionesArray as $grabacionArray){
                $grabacion = $em->getRepository(AdobeGrabacion::class)->findOneBy(['scoId' => $grabacionArray['scoId']]);
                if(empty($grabacion)){
                    $grabacion = new AdobeGrabacion(); 
                    $grabacion->loadFromArray($grabacionArray);
                    $grabacion->setReunion($reunionObj);
                    $em->persist($grabacion);
                }
                $reunionObj->addGrabacione($grabacion);
            }
            $em->persist($reunionObj);
        }

        //Procesamos las categorías (recursivo)
        
        foreach($infoScoId['categorias'] as $categoriaScoId => $categoria){
            $categoria['prefix'] = $prefix.$categoria['scoId'].'/';
            $infoScoId['categorias'][$categoriaScoId]['prefix'] = $categoria['prefix'];
            
            $categoriaObj = $em->getRepository(AdobeCategoria::class)->findOneBy(['scoId' => $categoria['scoId']]);
            if(empty($categoriaObj)){
                $categoriaObj = new AdobeCategoria(); 
                $categoriaObj->loadFromArray($categoria);
                if(!empty($cat)){
                    $categoriaObj->setCategoriaPadre($cat);
                }
            }
            $em->persist($categoriaObj);

            echo "Revisando categoria ".$categoria['prefix']."\r\n";
            $infoScoId['categorias'][$categoriaScoId]['datosCategoria'] = $this->procesaSCOID($client, $categoria['scoId'], $categoria['prefix'], $categoriaObj);

        }

        $em->flush(); 

        return $infoScoId;
    }

    public function getInfoReuniones(Client $client, $scoId='2689963968'){
        
        $url = 'https://edem.adobeconnect.com/admin/meeting/folder/list?filter-rows=1000&filter-start=0&sco-id='.$scoId.'&tab-id='.$scoId;
        $crawler = $client->request('GET', $url);

        $infoEnlaces = $crawler->filter('td.data>a');
        $infoEnlacesLimpios = [];
        $infoCategorias = [];
        $infoReuniones = [];
        foreach($infoEnlaces as $enlaceDOM){
            $enlace = trim($enlaceDOM->getAttribute('href'));
            $scoIdEnlace = substr($enlace, stripos($enlace, 'sco-id')+7);
            $scoId = substr($scoIdEnlace, 0, stripos($scoIdEnlace, '&'));
            
            if(empty($infoCategorias[$scoId]) && empty($infoReuniones[$scoId]) && !empty($enlaceDOM->nodeValue)){
                $isReunion = stripos($enlace, '/meeting?') === false;
                if($isReunion){
                    $infoReuniones[$scoId] = [
                        'enlace' => $enlace,
                        'scoId' => $scoId,
                        'nombre' => trim($enlaceDOM->nodeValue),
                        'grabaciones' => []
                    ];
                } else {
                    $infoCategorias[$scoId] = [
                        'enlace' => $enlace,
                        'scoId' => $scoId,
                        'nombre' => trim($enlaceDOM->nodeValue),
                        'datosCategoria' => [],
                        'prefix' => ''
                    ];
                }
            }   
        }
        return [
            'reuniones' => $infoReuniones,
            'categorias' => $infoCategorias
        ];
    }

    public function getInfoGrabaciones(Client $client, $reunionSCOid){
        $url = 'https://edem.adobeconnect.com/admin/meeting/sco/recordings?account-id=2689963964&filter-rows=120&sco-id='.$reunionSCOid.'&select-all=true&sort-date-modified=desc';
        $crawler = $client->request('GET', $url);

        $infoGrabacion = $crawler->filter('tr');
        $infoGrabacionesLimpio = [];

        foreach($infoGrabacion as $enlaceDOM){
            if(empty($enlaceDOM->getAttribute('class'))){ 
                $infoGrabacion = $enlaceDOM->childNodes;
                if($infoGrabacion->length == 9){
                    $nombreR = trim($infoGrabacion[2]->nodeValue);

                    $linkRDetails = $infoGrabacion[2]->childNodes[1]->childNodes[1]->childNodes[1]->getAttribute('href');
                    $scoIdExt = substr($linkRDetails, stripos($linkRDetails, 'sco-id') + 7 );
                    $scoId = substr($scoIdExt, 0,  stripos($scoIdExt, '&'));
                    
                    $accesoExt = $infoGrabacion[4]->childNodes[2]->nodeValue;
                    $acceso = substr($accesoExt, 0, stripos($accesoExt, "\r\n"));

                    $duracion = $infoGrabacion[7]->nodeValue;
                    $duracionArray = explode(':', $duracion);
                    $duracionSec = (sizeof($duracionArray) == 3)? intval($duracionArray[0]) * 3600 + intval($duracionArray[1]) * 60 + intval($duracionArray[2]) : 0;
                    
                    $linkDesconectadoJS = $infoGrabacion[3]->childNodes[1]->childNodes[3]->childNodes[1]->childNodes[1]->childNodes[3]->childNodes[1]->getAttribute('href');
                    $linkDesconectadoTxt = $infoGrabacion[3]->childNodes[1]->childNodes[3]->childNodes[1]->childNodes[1]->childNodes[3]->childNodes[1]->nodeValue;
                    $linkDesconectado = explode("'", $linkDesconectadoJS)[1];

                    $infoGrabacionesLimpio[] = [
                        'nombre' => $nombreR,
                        'linkDetalles' => $linkRDetails,
                        'scoId' => $scoId, 
                        'acceso' => $acceso,
                        'duracion' => $duracionSec,
                        'linkDesconectado' => $linkDesconectado
                    ];
                }
            }
        }
        
        return $infoGrabacionesLimpio;
    }

    public function login(Client $client){
        echo "Hacemos login \r\n";
        $crawler = $client->request('GET', 'https://edem.adobeconnect.com/system/login?next=%2Fadmin&set-lang=es');
        $form = $crawler->selectButton('Iniciar sesión')->form();
        
        $crawler = $client->submit($form, ['login' => $this->getParameter('adobe.user'), 'password' => $this->getParameter('adobe.passwd')]);
        return $client;
    }

    public function resetCookies(){
        $cookieFilePath = $this->getParameter('project.path').$this->getParameter('adobe.path.cookies');
        file_put_contents($cookieFilePath, '');
    }

    public function saveCookies($client){
        $cookieFilePath = $this->getParameter('project.path').$this->getParameter('adobe.path.cookies');
        $cookieJar = $client->getCookieJar();
        $cookies = $cookieJar->all();
        if ($cookies) {
            file_put_contents($cookieFilePath, serialize($cookies));
        }
    }

    public function loadCookies($client){
        $cookieFilePath = $this->getParameter('project.path').$this->getParameter('adobe.path.cookies');
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

