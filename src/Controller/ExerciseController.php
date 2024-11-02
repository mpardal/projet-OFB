<?php

namespace App\Controller;

use App\Entity\Competition;
use App\Entity\Event;
use App\Entity\Exercise;
use App\Entity\ExhibitorGroup;
use App\Form\CompetitionType;
use App\Form\EventType;
use App\Form\ExerciseType;
use App\Form\ExhibitorGroupType;
use App\Form\GroupNameVerificationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cours_et_seances_management')]
class ExerciseController extends AbstractController
{
    #[Route('/', name:'app_exercise_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $exercises = $entityManager->getRepository(Exercise::class)->findAll();

        return $this->render('exercise/index.html.twig', [
            'exercises' => $exercises
        ]);
    }

    #[Route('/creation', name:'app_exercise_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $exercise = new Exercise();
        $form = $this->createForm(ExerciseType::class, $exercise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($exercise);
            $entityManager->flush();

            $this->addFlash('success', 'Le cours ou la séance ' . $exercise->getTitle() . ' a bien été créé');
            return $this->redirectToRoute('app_exercise_index');
        }

        return $this->render('exercise/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/modification', name:'app_exercise_edit')]
    public function edit($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $exercise = $entityManager->getRepository(Exercise::class)->findOneBy($id);
        $form = $this->createForm(ExerciseType::class, $exercise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le cours ou la séance ' . $exercise->getTitle() . ' a bien été modifié');
            return $this->redirectToRoute('app_exercise_index');
        }
        return $this->render('exercise/edit.html.twig', [
            'form' => $form->createView(),
            'exercise' => $exercise
        ]);
    }

    #[Route('/{id}/suppression', name:'app_exercise_delete')]
    public function delete($id, EntityManagerInterface $entityManager): Response
    {
        $exercise = $entityManager->getRepository(Exercise::class)->findOneBy($id);

        $exercise->setArchived(true);

        $entityManager->flush();

        $this->addFlash('warning', 'L\'événement ' . $exercise->getTitle() . ' a bien été archivé');

        return $this->redirectToRoute('app_exercise_index');
    }

}