<?php

namespace App\Entity;

use App\Repository\ConferenceRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConferenceRepository::class)]
class Conference
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name = '';

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $slug = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $siteTitle = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $about = null;

    #[ORM\Column(type: 'boolean')]
    private bool $holdingPageEnabled = true;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTimeInterface $date = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $slackChannel = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $slackChannelUrl = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $feedbackFormUrl = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSiteTitle(): ?string
    {
        return $this->siteTitle;
    }

    public function setSiteTitle(?string $siteTitle): self
    {
        $this->siteTitle = $siteTitle;

        return $this;
    }

    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function setAbout(?string $about): self
    {
        $this->about = $about;

        return $this;
    }

    public function getHoldingPageEnabled(): bool
    {
        return $this->holdingPageEnabled;
    }

    public function setHoldingPageEnabled(bool $holdingPageEnabled): self
    {
        $this->holdingPageEnabled = $holdingPageEnabled;

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

    public function getFeedbackFormUrl(): ?string
    {
        return $this->feedbackFormUrl;
    }

    public function setFeedbackFormUrl(?string $feedbackFormUrl): self
    {
        $this->feedbackFormUrl = $feedbackFormUrl;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName();
    }
}
