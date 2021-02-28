<?php

namespace App\Entity;

use App\Repository\TalkRepository;
use DateInterval;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TalkRepository::class)
 */
class Talk
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\Column(type="dateinterval")
     */
    private ?DateInterval $duration;

    /**
     * @ORM\Column(type="dateinterval", nullable=true)
     */
    private ?DateInterval $qAndADuration;

    /**
     * @ORM\ManyToOne(targetEntity=Person::class)
     */
    private ?Person $organiser;

    /**
     * @ORM\ManyToOne(targetEntity=Person::class)
     */
    private ?Person $moderator;

    /**
     * @ORM\ManyToMany(targetEntity=Person::class)
     */
    private Collection $speakers;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $slackChannel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $slackChannelUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $teamsUrl;

    public function __construct()
    {
        $this->speakers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDuration(): ?DateInterval
    {
        return $this->duration;
    }

    public function setDuration(DateInterval $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getQAndADuration(): ?DateInterval
    {
        return $this->qAndADuration;
    }

    public function setQAndADuration(?DateInterval $qAndADuration): self
    {
        $this->qAndADuration = $qAndADuration;

        return $this;
    }

    public function getOrganiser(): ?Person
    {
        return $this->organiser;
    }

    public function setOrganiser(?Person $organiser): self
    {
        $this->organiser = $organiser;

        return $this;
    }

    public function getModerator(): ?Person
    {
        return $this->moderator;
    }

    public function setModerator(?Person $moderator): self
    {
        $this->moderator = $moderator;

        return $this;
    }

    /**
     * @return Collection|Person[]
     */
    public function getSpeakers(): Collection
    {
        return $this->speakers;
    }

    public function addSpeaker(Person $speaker): self
    {
        if (!$this->speakers->contains($speaker)) {
            $this->speakers[] = $speaker;
        }

        return $this;
    }

    public function removeSpeaker(Person $speaker): self
    {
        $this->speakers->removeElement($speaker);

        return $this;
    }

    public function getSlackChannel(): ?string
    {
        return $this->slackChannel;
    }

    public function setSlackChannel(?string $slackChannel): self
    {
        $this->slackChannel = $slackChannel;

        return $this;
    }

    public function getSlackChannelUrl(): ?string
    {
        return $this->slackChannelUrl;
    }

    public function setSlackChannelUrl(?string $slackChannelUrl): self
    {
        $this->slackChannelUrl = $slackChannelUrl;

        return $this;
    }

    public function getTeamsUrl(): ?string
    {
        return $this->teamsUrl;
    }

    public function setTeamsUrl(?string $teamsUrl): self
    {
        $this->teamsUrl = $teamsUrl;

        return $this;
    }
}
