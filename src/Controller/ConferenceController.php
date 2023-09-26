<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Repository\ConferenceRepository;
use App\Repository\SettingsRepository;
use App\Repository\SlotRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class ConferenceController extends AbstractController
{
    public function __construct(
        private ConferenceRepository $conferences,
        private SettingsRepository $settings,
        private SlotRepository $slots,
        private Environment $twig,
    ) {}

    #[Route('/{slug}', name: 'app_conference')]
    public function show(Conference $conference): Response
    {
        if (! $settings = $this->settings->find(1)) {
            throw $this->createNotFoundException();
        }

        $nextConference = $this->conferences->findNext($conference->getDate());
        $previousConference = $this->conferences->findPrevious($conference->getDate());

        $loader = $this->twig->getLoader();

        if ($conference->getHoldingPageEnabled() && (! $this->isGranted('ROLE_ADMIN'))) {
            $holdingTemplate = sprintf('conference/%s/holding.html.twig', $conference->getTheme()?->getSlug());
            if ($loader->exists($holdingTemplate)) {
                return $this->render(
                    $holdingTemplate,
                    compact('conference', 'nextConference', 'previousConference', 'settings')
                );
            }

            return $this->render(
                'conference/holding.html.twig',
                compact('conference', 'nextConference', 'previousConference', 'settings')
            );
        }

        $schedule = $this->slots->findByConference($conference, ['startTime' => 'ASC']);

        $conferenceTemplate = sprintf('conference/%s/index.html.twig', $conference->getTheme()?->getSlug());

        if ($loader->exists($conferenceTemplate)) {
            return $this->render($conferenceTemplate, compact('conference', 'schedule', 'settings'));
        }

        return $this->render(
            'conference/show.html.twig',
            compact('conference', 'schedule', 'settings', 'nextConference', 'previousConference')
        );
    }
}
