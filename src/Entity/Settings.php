<?php

namespace App\Entity;

use App\Repository\SettingsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SettingsRepository::class)]
class Settings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: Conference::class, cascade: ['persist', 'remove'])]
    private ?Conference $currentConference = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $conferenceDetails = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $introduction = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCurrentConference(): ?Conference
    {
        return $this->currentConference;
    }

    public function setCurrentConference(?Conference $currentConference): self
    {
        $this->currentConference = $currentConference;

        return $this;
    }

    public function getConferenceDetails(): ?string
    {
        return $this->conferenceDetails;
    }

    public function setConferenceDetails(?string $conferenceDetails): self
    {
        $this->conferenceDetails = $conferenceDetails;

        return $this;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(?string $introduction): self
    {
        $this->introduction = $introduction;

        return $this;
    }
}
