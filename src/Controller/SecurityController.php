<?php

namespace App\Controller;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login', priority: 1)]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_admin');
        }

        return $this->render('@EasyAdmin/page/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'last_username' => $authenticationUtils->getLastUsername(),
            'translation_domain' => 'admin',
            'page_title' => 'Sign in to Fabric Conference',
            'csrf_token_intention' => 'authenticate',
            'username_label' => 'Username',
            'username_parameter' => 'username',
            'password_parameter' => 'password',
        ]);
    }

    #[Route('/logout', name: 'app_logout', priority: 1)]
    public function logout(): void
    {
        throw new LogicException('This statement should never be reached');
    }
}
