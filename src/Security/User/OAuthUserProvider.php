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
        private AdminRepository $admins
    ) {}

    public function loadUserByUsername($username): UserInterface
    {
        $oAuthUser = new OAuthUser($username);

        if ($this->isAdmin($username)) {
            $oAuthUser->addRole('ROLE_ADMIN');
        }

        return $oAuthUser;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (! $user instanceof OAuthUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class): bool
    {
        return OAuthUser::class === $class;
    }

    private function isAdmin(string $email): bool
    {
        return in_array($email, self::$defaultAdmins, true) || $this->admins->findOneBy(compact('email'));
    }
}
