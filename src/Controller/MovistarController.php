<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;

use App\Controller\ConfigController;
use App\Entity\SDTicket;
use App\Entity\SDRegistroTiempo;

class MovistarController extends AbstractController {

    /**
     * @Route("/movistar/")
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
        
        $facturaInfo = $this->getLineas($client);
        //Si no tenemos tickets -> Tal vez no estemos logueados... 
        if(empty($facturaInfo)){
            $this->resetCookies(); 
            $this->login($client);
            $this->saveCookies($client);
            $facturaInfo = $this->getLineas($client);
        }
        
        dump($facturaInfo);
        die();



        return null;
    }

    public function getLineas(Client $client){
        $crawler = $client->request('GET', 'https://www.movistar.es/mimovistar-cliente/es-es/c_medianas-empresas/cclivr/consumos.html');
        $test = $crawler->filter('ccli-valor-positivo')->html();
        
//  https://www.movistar.es/estaticos/Metrics/AreaPrivadaMetrics.html?pagina=cclivr%2Fconsumos.html#?ccliToken=178231ca62dc29

        return $test;
    }

    public function login(Client $client){
        $crawler = $client->request('GET', $this->getParameter('movistar.url').$this->getParameter('movistar.url.login'));
        $form = $crawler->selectButton('ENTRAR')->form();
        
        $crawler = $client->submit($form, ['concontrasena_usuario' => $this->getParameter('movistar.user'), 'concontrasena_clave' => $this->getParameter('movistar.passwd')]);
        return $client;
    }

    public function resetCookies(){
        $cookieFilePath = ConfigController::$cookie_path;
        file_put_contents($cookieFilePath, '');
    }

    public function saveCookies($client){
        $cookieFilePath = ConfigController::$cookie_path;
        $cookieJar = $client->getCookieJar();
        $cookies = $cookieJar->all();
        if ($cookies) {
            file_put_contents($cookieFilePath, serialize($cookies));
        }
    }

    public function loadCookies($client){
        $cookieFilePath = ConfigController::$cookie_path;
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

