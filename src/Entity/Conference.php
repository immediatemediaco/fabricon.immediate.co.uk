<?php

namespace App\Entity;

use App\Repository\ConferenceRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank(message: 'Start date is required')]
    private ?DateTimeInterface $startDate = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTimeInterface $endDate = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $slackChannel = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $slackChannelUrl = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $feedbackFormUrl = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $track1Description = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $track2Description = null;

    #[ORM\ManyToOne(inversedBy: 'conferences')]
    private ?Theme $theme = null;

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

    public function getStartDate(): ?DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function isMultiDay(): bool
    {
        return $this->endDate !== null && $this->startDate->format('Y-m-d') !== $this->endDate->format('Y-m-d');
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

    /**
     * Returns an array of DateTimeInterface objects for each day from startDate to endDate (inclusive).
     * For single-day conferences (endDate is null or equals startDate), returns [$this->startDate].
     *
     * @return \DateTimeInterface[]
     */
    public function getDays(): array
    {
        if (!$this->startDate) {
            return [];
        }

        if (!$this->endDate || $this->startDate->format('Y-m-d') === $this->endDate->format('Y-m-d')) {
            return [$this->startDate];
        }

        $days = [];
        $current = \DateTime::createFromFormat('Y-m-d', $this->startDate->format('Y-m-d'));
        $end = \DateTime::createFromFormat('Y-m-d', $this->endDate->format('Y-m-d'));

        while ($current <= $end) {
            $days[] = \DateTimeImmutable::createFromMutable($current);
            $current->modify('+1 day');
        }

        return $days;
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getTheme(): ?Theme
    {
        return $this->theme;
    }

    public function setTheme(?Theme $theme): static
    {
        $this->theme = $theme;

        return $this;
    }

    #[Assert\Callback]
    public function validateDateRange(ExecutionContextInterface $context): void
    {
        if ($this->endDate && $this->startDate && $this->endDate < $this->startDate) {
            $context->buildViolation('End date must be on or after the start date')
                ->atPath('endDate')
                ->addViolation();
        }
    }
}
