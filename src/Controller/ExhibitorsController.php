<?php

namespace App\Controller;

use App\Entity\Exhibitor;
use App\Form\EmailVerificationType;

use App\Form\ExhibitorType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/exhibitors')]
class ExhibitorsController extends AbstractController
{
    #[Route('/', name:'app_exhibitors_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $exhibitors = $entityManager->getRepository(Exhibitor::class)->findBy(
            [
                'roles' => 'ROLE_EXHIBITOR',
                'archived' => false
            ],
            [
                'lastName' => 'DESC'
            ]
        );

        return $this->render('exhibitor/index.html.twig', [
            'exhibitors' => $exhibitors
        ]);
    }

    #[Route('pre_creation', name:'app_exhibitors_pre_create')]
    public function preCreate(Request $request, SessionInterface $session): Response
    {
        $form = $this->createForm(EmailVerificationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $exhibitor = new Exhibitor();
            $exhibitor->setEmail($form->get('email')->getData());

            $session->set('exhibitor', $exhibitor);

            return $this->redirectToRoute('app_exhibitors_create');
        }
        return $this->render('exhibitor/pre-create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/creation', name:'app_exhibitors_create')]
    public function create(Request $request, EntityManagerInterface $entityManager,
                           SessionInterface $session, UserPasswordHasherInterface $passwordHasher): Response
    {
        $exhibitor = $session->get('exhibitor');
        $form = $this->createForm(ExhibitorType::class, $exhibitor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();

            if ($plainPassword) {
                // Si un nouveau mot de passe a été fourni, on le hash et on le met à jour
                $hashedPassword = $passwordHasher->hashPassword($exhibitor, $plainPassword);
                $exhibitor->setPassword($hashedPassword);
            }

            $entityManager->persist($exhibitor);
            $entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur ' . $exhibitor->getFullName() . ' a bien été créé');
            return $this->redirectToRoute('app_exhibitors_index');
        }

        return $this->render('exhibitor/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/modification', name:'app_exhibitors_edit')]
    public function edit($id, Request $request, EntityManagerInterface $entityManager,
                         UserPasswordHasherInterface $passwordHasher): Response
    {
        $exhibitor = $entityManager->getRepository(Exhibitor::class)->find($id);

        $form = $this->createForm(ExhibitorType::class, $exhibitor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $plainPassword = $form->get('password')->getData();

            if ($plainPassword) {
                // Si un nouveau mot de passe a été fourni, on le hash et on le met à jour
                $hashedPassword = $passwordHasher->hashPassword($exhibitor, $plainPassword);
                $exhibitor->setPassword($hashedPassword);
            }

            $entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur ' . $exhibitor->getFullName() . ' a bien été modifié');
            return $this->redirectToRoute('app_exhibitors_index');
        }
        return $this->render('exhibitor/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/suppression', name:'app_exhibitors_delete')]
    public function delete($id, EntityManagerInterface $entityManager): Response
    {
        $exhibitor = $entityManager->getRepository(Exhibitor::class)->find($id);

        $exhibitor->setArchived(true);

        $entityManager->flush();

        $this->addFlash('warning', 'L\'utilisateur ' . $exhibitor->getFullName() . ' a bien été archivé');

        return $this->redirectToRoute('app_exhibitors_index');

    }

}