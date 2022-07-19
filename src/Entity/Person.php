<?php

namespace App\Entity;

use App\Repository\PersonRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
class Person
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name = '';

    #[ORM\Column(type: 'boolean')]
    private bool $isModerator = false;

    #[ORM\Column(type: 'boolean')]
    private bool $isOrganiser = false;

    #[ORM\Column(type: 'boolean')]
    private bool $isSpeaker = false;

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

    public function getRoles(): string
    {
        $roles = [];
        if ($this->isModerator()) {
            $roles[] = 'Moderator';
        }

        if ($this->isOrganiser()) {
            $roles[] = 'Organiser';
        }
        if ($this->isSpeaker()) {
            $roles[] = 'Speaker';
        }

        return implode(', ', $roles);
    }

    public function isModerator(): bool
    {
        return $this->isModerator;
    }

    public function setIsModerator(bool $isModerator): self
    {
        $this->isModerator = $isModerator;

        return $this;
    }

    public function isOrganiser(): bool
    {
        return $this->isOrganiser;
    }

    public function setIsOrganiser(bool $isOrganiser): self
    {
        $this->isOrganiser = $isOrganiser;

        return $this;
    }

    public function isSpeaker(): bool
    {
        return $this->isSpeaker;
    }

    public function setIsSpeaker(bool $isSpeaker): self
    {
        $this->isSpeaker = $isSpeaker;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName();
    }
}
