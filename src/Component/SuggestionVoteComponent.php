<?php

declare(strict_types=1);

namespace App\Component;

use App\Entity\Suggestion;
use App\Repository\SuggestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('suggestion_vote')]
class SuggestionVoteComponent extends AbstractController
{
    use DefaultActionTrait;

    #[LiveProp]
    public Suggestion $suggestion;

    #[LiveProp]
    public bool $hasVoted = false;

    public function __construct(
        private SuggestionRepository $suggestions,
    ) {
    }

    #[LiveAction]
    public function vote(): void
    {
        $this->suggestion->upVote();
        $this->suggestions->add($this->suggestion, true);
        $this->hasVoted = true;
    }
}
