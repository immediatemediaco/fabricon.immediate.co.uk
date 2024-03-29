<?php

namespace App\Entity;

use App\Repository\SlotRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SlotRepository::class)]
class Slot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'time', nullable: true)]
    private ?DateTimeInterface $startTime = null;

    #[ORM\Column(type: 'time', nullable: true)]
    private ?DateTimeInterface $endTime = null;

    #[ORM\ManyToOne(targetEntity: Talk::class)]
    private ?Talk $track1 = null;

    #[ORM\ManyToOne(targetEntity: Talk::class)]
    private ?Talk $track2 = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $breakDetails = null;

    #[ORM\ManyToOne(targetEntity: Conference::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Conference $conference = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartTime(): ?DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(?DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(?DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getTrack1(): ?Talk
    {
        return $this->track1;
    }

    public function setTrack1(?Talk $track1): self
    {
        $this->track1 = $track1;

        return $this;
    }

    public function getTrack2(): ?Talk
    {
        return $this->track2;
    }

    public function setTrack2(?Talk $track2): self
    {
        $this->track2 = $track2;

        return $this;
    }

    public function getBreakDetails(): ?string
    {
        return $this->breakDetails;
    }

    public function setBreakDetails(?string $breakDetails): self
    {
        $this->breakDetails = $breakDetails;

        return $this;
    }

    public function getBreakStyle(): ?string
    {
        return strtolower(str_replace(' ', '-', rtrim($this->breakDetails, ' break')));
    }

    public function getConference(): ?Conference
    {
        return $this->conference;
    }

    public function setConference(?Conference $conference): self
    {
        $this->conference = $conference;

        return $this;
    }
}
