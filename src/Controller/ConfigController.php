<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;

class ConfigController extends AbstractController
{
    public static $project_path = 'D:/__proyectos/sothis.local/';
    public static $cookie_path = 'D:/__proyectos/sothis.local/files/cookies/galletas.txt';
}

