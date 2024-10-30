<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{

    #[Route('/', name: 'home_public')]
    public function index(): Response
    {
        return $this->render('Pages/dashboard.html.twig');
    }

    #[Route('/home-private', name: 'home_private')]
    //#[IsGranted('ROLE_ADMIN or ROLE_EXHIBITOR')] // Autorise les utilisateurs ayant l'un des deux rôles
    public function homePrivate(): Response
    {
        //$this->denyAccessUnlessGranted(['ROLE_ADMIN', 'ROLE_EXHIBITOR']);

        return $this->render('pages/dashboard_private.html.twig');
    }

}