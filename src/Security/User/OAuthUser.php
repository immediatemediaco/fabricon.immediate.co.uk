<?php

namespace App\Security\User;

use Symfony\Component\Security\Core\User\UserInterface;

class OAuthUser implements UserInterface
{
    public function __construct(
        private string $username,
        private array $roles = ['ROLE_USER', 'ROLE_OAUTH_USER']
    ) {}

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function addRole(string $role): void
    {
        $this->roles[] = $role;
    }

    public function getPassword(): ?string
    {
        return '';
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function eraseCredentials(): void {}
}
