<?php

namespace App\Entity;

use App\Repository\SuggestionRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SuggestionRepository::class)]
class Suggestion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $type = null;

    #[ORM\Column(type: 'text')]
    private ?string $topic = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeInterface $date = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $isPoll = false;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $votes = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getTopic(): ?string
    {
        return $this->topic;
    }

    public function setTopic(string $topic): self
    {
        $this->topic = $topic;

        return $this;
    }

    public function getDate(): ?DateTimeInterface
    {
        return $this->date ?: new DateTime();
    }

    public function setDate(DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function isPoll(): ?bool
    {
        return $this->isPoll;
    }

    public function setIsPoll(?bool $isPoll): self
    {
        $this->isPoll = $isPoll;

        return $this;
    }

    public function getVotes(): ?int
    {
        return $this->votes;
    }

    public function setVotes(?int $votes): self
    {
        $this->votes = $votes;

        return $this;
    }
}
