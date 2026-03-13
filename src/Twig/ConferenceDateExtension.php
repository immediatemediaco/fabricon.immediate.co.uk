<?php

namespace App\Twig;

use App\Entity\Conference;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ConferenceDateExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('conference_date_range', [$this, 'formatConferenceDateRange']),
        ];
    }

    public function formatConferenceDateRange(Conference $conference): string
    {
        $startDate = $conference->getStartDate();
        $endDate = $conference->getEndDate();

        if (!$startDate) {
            return '';
        }

        // Single day conference or no end date
        if (!$endDate || $startDate->format('Y-m-d') === $endDate->format('Y-m-d')) {
            return $startDate->format('d F Y');
        }

        // Multi-day conference
        $startFormatted = $startDate->format('d F Y');
        $endFormatted = $endDate->format('d F Y');

        // Same month and year
        if ($startDate->format('F Y') === $endDate->format('F Y')) {
            return $startDate->format('d') . ' - ' . $endFormatted;
        }

        // Same year, different months
        if ($startDate->format('Y') === $endDate->format('Y')) {
            return $startDate->format('d F') . ' - ' . $endFormatted;
        }

        // Different years
        return $startFormatted . ' - ' . $endFormatted;
    }
}
