<?php

namespace App\Controller;

use App\Entity\Attachments;
use App\Entity\Competition;
use App\Entity\Event;
use App\Entity\Exercise;
use App\Entity\Exhibitor;
use App\Entity\ExhibitorGroup;
use App\Entity\Member;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageController extends AbstractController
{
    #[Route('/concept', name: 'app_page_concept')]
    public function concept(): Response
    {
        return $this->render('pages/concept.html.twig');
    }

    #[Route('/presentation_exposants', name: 'app_page_exhibitor_group')]
    public function exhibitorGroup(EntityManagerInterface $entityManager): Response
    {
        $exhibitorsGroup = $entityManager->getRepository(ExhibitorGroup::class)->findAll();

        return $this->render('pages/exhibitors.html.twig',[
            'exhibitorsGroup' => $exhibitorsGroup
        ]);
    }

    #[Route('/exposant/{group_name}', name: 'group_show', methods: ['GET'])]
    public function showExhibitorGroup(int $groupName, EntityManagerInterface $entityManager): Response
    {
        $exhibitorGroup = $entityManager->getRepository(ExhibitorGroup::class)->findOneBy(
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
    public function exercise(EntityManagerInterface $entityManager): Response
    {
        $exercises = $entityManager->getRepository(Exercise::class)->findAll();

        return $this->render('pages/exercise.html.twig',[
            'exercises' => $exercises
        ]);
    }

    #[Route('/concours_ludiques', name: 'app_page_competition')]
    public function competition(EntityManagerInterface $entityManager): Response
    {
        $competitions = $entityManager->getRepository(Competition::class)->findAll();

        return $this->render('pages/competition.html.twig',[
            'competitions' => $competitions
        ]);
    }

    #[Route('/presentation_equipe', name: 'app_page_team')]
    public function team( EntityManagerInterface $entityManager): Response
    {
        $members = $entityManager->getRepository(Member::class)->findBy(
            [
                'archived' => false
            ]
        );
        return $this->render('pages/team.html.twig', [
            'members' => $members
        ]);
    }

    #[Route('/billetterie', name: 'app_page_ticket_office')]
    public function ticketOffice(EntityManagerInterface $entityManager): Response
    {
        $events = $entityManager->getRepository(Event::class)->findBy(
            [
                'archived' => false
            ]
        );

        return $this->render('pages/ticket_office.html.twig',[
            'events' => $events
        ]);
    }

    #[Route('/groupe_exposants/{id}', name: 'app_exhibitor_group_details', methods: ['GET'])]
    public function groupDetails(int $id, EntityManagerInterface $entityManager): Response
    {
        $exhibitorGroup = $entityManager->getRepository(ExhibitorGroup::class)->findOneBy([
            'id' => $id
        ]);

        $images = [];
        $video = null;

        $attachments = $entityManager->getRepository(Attachments::class)->findBy([
            'exhibitorGroup' => $exhibitorGroup
        ]);

        foreach ($attachments as $attachment) {
            if ($attachment->getType() === Attachments::IMAGES) {
                $images[] = $attachment->getUrl();
                //$images[] = $this->getParameter('uploads_directory') . '/' . $attachment->getFilePath();
            } elseif ($attachment->getType() === Attachments::VIDEOS) {
                $video = $attachment->getUrl();
            }
        }



        return $this->render('pages/exhibitor_details.html.twig',[
            'exhibitorGroup' => $exhibitorGroup,
            'images' => $images,
            'video' => $video,
        ]);
    }
}