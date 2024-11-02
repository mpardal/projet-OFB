<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\ExhibitorGroup;
use App\Form\EventType;
use App\Form\ExhibitorGroupType;
use App\Form\GroupNameVerificationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/evenements')]
class EventController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name:'app_event_index')]
    public function index(): Response
    {
        $events = $this->entityManager->getRepository(Event::class)->findAll();

        return $this->render('event/index.html.twig', [
            'events' => $events
        ]);
    }

    #[Route('/creation', name:'app_event_create')]
    public function create(Request $request): Response
    {
        $entityManager = $this->entityManager;
        $form = $this->createForm(EventType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = new Event();

            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', 'L\'événement ' . $event->getTitle() . ' a bien été créé');
            return $this->redirectToRoute('app_event_index');
        }

        return $this->render('event/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/modification', name:'app_event_edit')]
    public function edit($id, Request $request): Response
    {
        $entityManager = $this->entityManager;
        $event = $entityManager->getRepository(Event::class)->find($id);
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'L\'événement ' . $event->getTitle() . ' a bien été modifié');
            return $this->redirectToRoute('app_event_index');
        }
        return $this->render('event/edit.html.twig', [
            'form' => $form->createView(),
            'event' => $event
        ]);
    }

    #[Route('/{id}/suppression', name:'app_event_delete')]
    public function delete($id): Response
    {
        $entityManager = $this->entityManager;
        $event = $entityManager->getRepository(Event::class)->find($id);

        $event->setArchived(true);

        $entityManager->flush();

        $this->addFlash('warning', 'L\'événement ' . $event->getTitle() . ' a bien été archivé');

        return $this->redirectToRoute('app_event_index');
    }

}