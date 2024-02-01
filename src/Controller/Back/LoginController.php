<?php

namespace App\Controller\Back;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    /**
     * Renders the login page.
     * 
     * The login page is accessible at the route "/connexion".
     * 
     * @Route("/connexion", name="app_back_login")
     * 
     * @param AuthenticationUtils $authenticationUtils - An instance of the AuthenticationUtils class
     * 
     * @return Response - The rendered login page
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Render the login page template
        return $this->render('back/login/index.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }
}
