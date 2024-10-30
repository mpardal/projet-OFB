<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageController extends AbstractController
{
    #[Route('/concept', name: 'app_page_concept')]
    public function concept(): Response
    {
        return $this->render('pages/concept.html.twig');
    }

    #[Route('/presentation_exposants', name: 'app_page_exhibitor')]
    public function exhibitor(): Response
    {
        return $this->render('pages/exhibitors.html.twig');
    }

    #[Route('/cours_et_seances', name: 'app_page_exercise')]
    public function exercise(): Response
    {
        return $this->render('pages/exercise.html.twig');
    }

    #[Route('/concours_ludiques', name: 'app_page_competition')]
    public function competition(): Response
    {
        return $this->render('pages/competition.html.twig');
    }

    #[Route('/presentation_equipe', name: 'app_page_team')]
    public function team(): Response
    {
        return $this->render('pages/team.html.twig');
    }
}