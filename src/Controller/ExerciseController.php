<?php

namespace App\Controller;


use App\Entity\Exercise;
use App\Form\ExerciseType;
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
        $exercises = $entityManager->getRepository(Exercise::class)->findBy(
            [
                'archived' => false
            ],
            [
                'lastName' => 'DESC'
            ]
        );

        $exercisesArchived = $entityManager->getRepository(Exercise::class)->findBy(
            [
                'archived' => true
            ],
            [
                'lastName' => 'DESC'
            ]
        );

        return $this->render('exercise/index.html.twig', [
            'exercises' => $exercises,
            'exercisesArchived' => $exercisesArchived
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

        $this->addFlash('warning', 'Le cours ou la séance ' . $exercise->getTitle() . ' a bien été archivé');

        return $this->redirectToRoute('app_exercise_index');
    }

    #[Route('/{id}/reactivation', name:'app_exercise_reactivate')]
    public function reActivate($id, EntityManagerInterface $entityManager): Response
    {
        $exercise = $entityManager->getRepository(Exercise::class)->findOneBy($id);

        $exercise->setArchived(false);

        $entityManager->flush();

        $this->addFlash('success', 'Le cours ou la séance ' . $exercise->getTitle() . ' a bien été réactivé');

        return $this->redirectToRoute('app_exercise_index');
    }
}