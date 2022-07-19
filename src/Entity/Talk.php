<?php

namespace App\Entity;

use App\Repository\TalkRepository;
use DateInterval;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TalkRepository::class)]
class Talk
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title = '';

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'dateinterval')]
    private ?DateInterval $duration = null;

    #[ORM\Column(type: 'dateinterval', nullable: true)]
    private ?DateInterval $qAndADuration = null;

    #[ORM\ManyToOne(targetEntity: Person::class)]
    private ?Person $organiser = null;

    #[ORM\ManyToOne(targetEntity: Person::class)]
    private ?Person $moderator = null;

    #[ORM\ManyToMany(targetEntity: Person::class)]
    private Collection $speakers;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $slackChannel = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $slackChannelUrl = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $teamsUrl = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $slidoText = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $slidoUrl = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $isArchived = false;

    public function __construct()
    {
        $this->speakers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
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

    public function getSlidoText(): ?string
    {
        return $this->slidoText;
    }

    public function setSlidoText(?string $slidoText): self
    {
        $this->slidoText = $slidoText;

        return $this;
    }

    public function getSlidoUrl(): ?string
    {
        return $this->slidoUrl;
    }

    public function setSlidoUrl(?string $slidoUrl): self
    {
        $this->slidoUrl = $slidoUrl;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getTitle();
    }

    public function getIsArchived(): ?bool
    {
        return $this->isArchived;
    }

    public function setIsArchived(?bool $isArchived): self
    {
        $this->isArchived = $isArchived;

        return $this;
    }
}
