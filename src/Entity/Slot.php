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
    private ?Talk $talk = null;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private ?int $track = null;

    #[ORM\Column(type: 'date')]
    private ?DateTimeInterface $date = null;

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

    public function getTalk(): ?Talk
    {
        return $this->talk;
    }

    public function setTalk(?Talk $talk): self
    {
        $this->talk = $talk;

        return $this;
    }

    public function getTrack(): ?int
    {
        return $this->track;
    }

    public function setTrack(?int $track): self
    {
        $this->track = $track;

        return $this;
    }

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function isBreak(): bool
    {
        return !empty($this->breakDetails);
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
