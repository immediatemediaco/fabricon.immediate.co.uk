<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', host: '{year<202[0-1]>?2021}.%app.base_host%')]
    public function index(int $year): Response
    {
        return $this->render('home/index.html.twig', compact('year'));
    }
}
