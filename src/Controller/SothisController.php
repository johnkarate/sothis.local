<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;

class SothisController extends AbstractController {

    /**
     * @Route("/")
     */
    public function public(): ?Response 
    {
        return $this->redirect('https://www.sothis.tech', 301);
    }

}

