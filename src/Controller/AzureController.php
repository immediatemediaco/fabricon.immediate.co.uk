<?php

declare(strict_types=1);

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class AzureController extends AbstractController
{
    #[Route('/login', name: 'app_login', priority: 1)]
    public function connect(ClientRegistry $clientRegistry): RedirectResponse
    {
        return $clientRegistry->getClient('azure')->redirect(['openid', 'profile'], []);
    }

    #[Route('/connect/azure/check', name: 'connect_azure_check')]
    public function connectCheck(): void
    {
        throw new LogicException('This statement should never be reached');
    }

    #[Route('/logout', name:'app_logout', priority: 1)]
    public function logout(): void
    {
        throw new LogicException('This statement should never be reached');
    }
}
