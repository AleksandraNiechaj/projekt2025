<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login', methods: ['GET','POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Jeżeli użytkownik już zalogowany, przekieruj go na listę przepisów
        if ($this->getUser()) {
            return $this->redirectToRoute('app_recipe_index');
        }

        // Pobierz ewentualny błąd logowania i ostatnio wpisany login
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): void
    {
        // Kontroler pozostaje pusty — Symfony przejmie tu kontrolę
        throw new \LogicException('Ta metoda nie powinna być wywoływana bezpośrednio.');
    }
}
