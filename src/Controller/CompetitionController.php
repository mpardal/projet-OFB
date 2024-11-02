<?php

namespace App\Controller;

use App\Entity\Competition;
use App\Entity\Event;
use App\Entity\ExhibitorGroup;
use App\Form\CompetitionType;
use App\Form\EventType;
use App\Form\ExhibitorGroupType;
use App\Form\GroupNameVerificationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/event')]
class CompetitionController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name:'app_competition_index')]
    public function index(): Response
    {
        $competitions = $this->entityManager->getRepository(Competition::class)->findAll();

        return $this->render('competition/index.html.twig', [
            'competitions' => $competitions
        ]);
    }

    #[Route('/creation', name:'app_competition_create')]
    public function create(Request $request): Response
    {
        $entityManager = $this->entityManager;
        $form = $this->createForm(CompetitionType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $competition = new Competition();
            $entityManager->persist($competition);
            $entityManager->flush();

            $this->addFlash('success', 'Le concours ' . $competition->getTitle() . ' a bien été créé');
            return $this->redirectToRoute('app_competition_index');
        }

        return $this->render('competition/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/modification', name:'app_competition_edit')]
    public function edit($id, Request $request): Response
    {
        $entityManager = $this->entityManager;
        $competition = $entityManager->getRepository(Competition::class)->findOneBy($id);
        $form = $this->createForm(CompetitionType::class, $competition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Le concours ' . $competition->getTitle() . ' a bien été modifié');
            return $this->redirectToRoute('app_competition_index');
        }
        return $this->render('competition/edit.html.twig', [
            'form' => $form->createView(),
            'competition' => $competition
        ]);
    }

    #[Route('/{id}/suppression', name:'app_competition_delete')]
    public function delete($id): Response
    {
        $entityManager = $this->entityManager;
        $competition = $entityManager->getRepository(Competition::class)->findOneBy($id);

        $competition->setArchived(true);

        $entityManager->flush();

        $this->addFlash('warning', 'Le concours ' . $competition->getTitle() . ' a bien été archivé');

        return $this->redirectToRoute('app_competition_index');
    }

}