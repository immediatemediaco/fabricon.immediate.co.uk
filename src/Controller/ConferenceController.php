<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Repository\SettingsRepository;
use App\Repository\SlotRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class ConferenceController extends AbstractController
{
    public function __construct(
        private SettingsRepository $settings,
        private SlotRepository $slots,
        private Environment $twig,
    ) {}

    #[Route('/{slug}', name: 'app_conference')]
    public function index(Conference $conference): Response
    {
        if (! $settings = $this->settings->find(1)) {
            throw $this->createNotFoundException();
        }

        if ($conference->getHoldingPageEnabled() && (! $this->isGranted('ROLE_ADMIN'))) {
            return $this->render('conference/holding.html.twig', compact('conference'));
        }

        $schedule = $this->slots->findByConference($conference, ['startTime' => 'ASC']);

        $loader = $this->twig->getLoader();

        $conferenceTemplate = sprintf('conference/%s/index.html.twig', $conference->getSlug());

        if ($loader->exists($conferenceTemplate)) {
            return $this->render($conferenceTemplate, compact('conference', 'schedule', 'settings'));
        }

        return $this->render('conference/index.html.twig', compact('conference', 'schedule', 'settings'));
    }
}
