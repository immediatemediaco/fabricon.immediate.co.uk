<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Repository\ConferenceRepository;
use App\Repository\SettingsRepository;
use App\Repository\SlotRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
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

        $nextConference = $this->conferences->findNext($conference->getStartDate());
        $previousConference = $this->conferences->findPrevious($conference->getStartDate());

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

        $slotsByDay = $this->slots->findByConferenceGroupedByDay($conference);
        $scheduleData = $this->prepareScheduleData($slotsByDay, $conference);

        $conferenceTemplate = sprintf('conference/%s/index.html.twig', $conference->getTheme()?->getSlug());

        if ($loader->exists($conferenceTemplate)) {
            return $this->render($conferenceTemplate, compact('conference', 'scheduleData', 'settings'));
        }

        return $this->render(
            'conference/show.html.twig',
            compact('conference', 'scheduleData', 'settings', 'nextConference', 'previousConference')
        );
    }

    private function prepareScheduleData(array $slotsByDay, Conference $conference): array
    {
        $days = [];

        foreach ($slotsByDay as $dateKey => $slots) {
            $track1Slots = [];
            $track2Slots = [];
            $breaks = [];

            // Calculate day time range across all slots
            $dayStartMinutes = PHP_INT_MAX;
            $dayEndMinutes = PHP_INT_MIN;

            foreach ($slots as $slot) {
                $startMin = $this->timeToMinutes($slot->getStartTime());
                $endMin   = $this->timeToMinutes($slot->getEndTime());

                if ($startMin !== null && $startMin < $dayStartMinutes) {
                    $dayStartMinutes = $startMin;
                }
                if ($endMin !== null && $endMin > $dayEndMinutes) {
                    $dayEndMinutes = $endMin;
                }
            }

            if ($dayStartMinutes === PHP_INT_MAX) {
                $dayStartMinutes = 0;
                $dayEndMinutes   = 0;
            }

            $totalRows = max(1, (int) round(($dayEndMinutes - $dayStartMinutes) / 5));

            foreach ($slots as $slot) {
                $startMin = $this->timeToMinutes($slot->getStartTime());
                $endMin   = $this->timeToMinutes($slot->getEndTime());

                $gridRowStart = $startMin !== null ? (int) round(($startMin - $dayStartMinutes) / 5) + 1 : 1;
                $gridRowEnd   = $endMin !== null   ? (int) round(($endMin   - $dayStartMinutes) / 5) + 1 : 2;

                $slotData = [
                    'slot'         => $slot,
                    'gridRowStart' => $gridRowStart,
                    'gridRowEnd'   => $gridRowEnd,
                ];

                if ($slot->isBreak()) {
                    $breaks[] = $slotData;
                } elseif ($slot->getTrack() === 1) {
                    $track1Slots[] = $slotData;
                } elseif ($slot->getTrack() === 2) {
                    $track2Slots[] = $slotData;
                }
            }

            // Find the date object from the first slot for this day
            $dateObj = null;
            foreach ($slots as $slot) {
                if ($slot->getDate()) {
                    $dateObj = $slot->getDate();
                    break;
                }
            }

            $days[] = [
                'date'          => $dateObj,
                'dateFormatted' => $dateObj?->format('l j F Y') ?? $dateKey,
                'dateKey'       => $dateKey,
                'track1Slots'   => $track1Slots,
                'track2Slots'   => $track2Slots,
                'breaks'        => $breaks,
                'totalRows'     => $totalRows,
            ];
        }

        return ['days' => $days];
    }

    private function timeToMinutes(?\DateTimeInterface $time): ?int
    {
        if ($time === null) {
            return null;
        }

        return (int) $time->format('H') * 60 + (int) $time->format('i');
    }
}
