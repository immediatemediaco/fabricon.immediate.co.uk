<?php

declare(strict_types=1);

namespace App\Security;

use App\Security\User\OAuthUser;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use TheNetworg\OAuth2\Client\Provider\AzureResourceOwner;

class AzureAuthenticator extends OAuth2Authenticator implements AuthenticationEntryPointInterface
{
    private const CLAIM_GROUPS = 'groups';
    private const GROUP_APP_ADMIN = '1e54b4d7-50fa-4303-a6fc-52325ae4e0f2';

    public function __construct(
        private ClientRegistry $clientRegistry,
        private RouterInterface $router,
        private UserProviderInterface $userProvider,
    ) {}

    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') === 'connect_azure_check';
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        $loginUrl = $this->router->generate('app_login');

        return new RedirectResponse($loginUrl, Response::HTTP_TEMPORARY_REDIRECT);
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('azure');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client) {
                /** @var AzureResourceOwner $azureUser */
                $azureUser = $client->fetchUserFromToken($accessToken);

                /** @var OAuthUser $user */
                $user = $this->userProvider->loadUserByIdentifier($azureUser->getUpn());

                if ($this->isAdmin($azureUser)) {
                    $user->addRole('ROLE_ADMIN');
                }

                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $targetUrl = $this->router->generate('app_admin');

        return new RedirectResponse($targetUrl);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    private function isAdmin(AzureResourceOwner $azureUser): bool
    {
        $groups = $azureUser->claim(self::CLAIM_GROUPS);

        return $groups !== null && in_array(self::GROUP_APP_ADMIN, $groups, true);
    }
}
