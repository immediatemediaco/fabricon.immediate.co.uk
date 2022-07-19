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
    private ?string $track1Description = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $track2Description = null;

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

    public function getTrack1Description(): ?string
    {
        return $this->track1Description;
    }

    public function setTrack1Description(?string $track1Description): self
    {
        $this->track1Description = $track1Description;

        return $this;
    }

    public function getTrack2Description(): ?string
    {
        return $this->track2Description;
    }

    public function setTrack2Description(?string $track2Description): self
    {
        $this->track2Description = $track2Description;

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
