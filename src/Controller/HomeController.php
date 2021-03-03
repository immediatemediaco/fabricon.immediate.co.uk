<?php

namespace App\Controller;

use App\Repository\SettingsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private SettingsRepository $settings,
    ) {}

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $settings = $this->settings->find(1);

        if ($settings && $conference = $settings->getCurrentConference()) {
            return $this->redirectToRoute('app_conference', ['slug' => $conference->getSlug()]);
        }

        return $this->render('home/index.html.twig');
    }
}
