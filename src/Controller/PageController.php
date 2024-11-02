<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Exhibitor;
use App\Entity\ExhibitorGroup;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/concept', name: 'app_page_concept')]
    public function concept(): Response
    {
        return $this->render('pages/concept.html.twig');
    }

    #[Route('/presentation_exposants', name: 'app_page_exhibitor_group')]
    public function exhibitorGroup(): Response
    {
        $exhibitorsGroup = $this->entityManager->getRepository(ExhibitorGroup::class)->findAll();

        return $this->render('pages/exhibitors.html.twig',[
            'exhibitorsGroup' => $exhibitorsGroup
        ]);
    }

    #[Route('/exposant/{group_name}', name: 'group_show', methods: ['GET'])]
    public function showExhibitorGroup(int $groupName): Response
    {
        $exhibitorGroup = $this->entityManager->getRepository(ExhibitorGroup::class)->findOneBy(
            [
                'groupName' => $groupName
            ]
        );

        if (!$exhibitorGroup) {
            throw $this->createNotFoundException("Le groupe d'exposants n'existe pas.");
        }

        // Séparation des images et des vidéos pour l'affichage
        $images = [];
        $video = null;

        foreach ($exhibitorGroup->getAttachments() as $attachment) {
            if ($attachment->getType() === 'image') {
                $images[] = $attachment;
            } elseif ($attachment->getType() === 'video') {
                $video = $attachment;
            }
        }

        return $this->render('pages/exhibitor_details.html.twig', [
            'exhibitorGroup' => $exhibitorGroup,
            'images' => $images,
            'video' => $video,
        ]);
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

    #[Route('/billetterie', name: 'app_page_ticket_office')]
    public function ticketOffice(): Response
    {
        $events = $this->entityManager->getRepository(Event::class)->findBy(
            [
                'archived' => false
            ]
        );

        return $this->render('pages/ticket_office.html.twig',[
            'events' => $events
        ]);
    }
}