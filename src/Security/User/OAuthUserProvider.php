<?php

namespace App\Security\User;

use App\Repository\AdminRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class OAuthUserProvider implements UserProviderInterface
{
    private static array $defaultAdmins = [
        'rob.meijer@immediate.co.uk',
    ];

    public function __construct(
        private AdminRepository $admins,
    ) {
    }

    public function loadUserByIdentifier($identifier): UserInterface
    {
        $oAuthUser = new OAuthUser($identifier);

        if ($this->isAdmin($identifier)) {
            $oAuthUser->addRole('ROLE_ADMIN');
        }

        return $oAuthUser;
    }

    public function loadUserByUsername($username): UserInterface
    {
        return $this->loadUserByIdentifier($username);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (! $user instanceof OAuthUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass($class): bool
    {
        return OAuthUser::class === $class;
    }

    private function isAdmin(string $email): bool
    {
        return in_array($email, self::$defaultAdmins, true) || $this->admins->findAdminByEmail($email);
    }
}
