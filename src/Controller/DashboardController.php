<?php

namespace App\Controller;

use App\Entity\DashboardArticle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'home_public')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $articles = $entityManager->getRepository(DashboardArticle::class)->findBy(
            ['archived' => false],
            ['id' => 'DESC']
        );

        return $this->render('Pages/dashboard.html.twig', [
            'articles' => $articles
        ]);
    }

    #[Route('/home-private', name: 'home_private')]
    public function homePrivate(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('home_public');
        }

        return $this->render('pages/dashboard_private.html.twig');
    }

}