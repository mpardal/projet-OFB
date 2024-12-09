<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Form\EmailVerificationType;
use App\Form\AdminType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/', name:'app_admin_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $admins = $entityManager->getRepository(Admin::class)->findBy(
            [
                'archived' => false
            ],
            [
                'lastName' => 'DESC'
            ]
        );

        $adminsArchived = $entityManager->getRepository(Admin::class)->findBy(
            [
                'archived' => true
            ],
            [
                'lastName' => 'DESC'
            ]
        );

        return $this->render('admin/index.html.twig', [
            'admins' => $admins,
            'adminsArchived' => $adminsArchived
        ]);
    }

    #[Route('pre_creation', name:'app_admin_pre_create')]
    public function preCreate(Request $request, SessionInterface $session): Response
    {
        $form = $this->createForm(EmailVerificationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $admin = new Admin();
            $admin->setEmail($form->get('email')->getData());

            $session->set('admin', $admin);

            return $this->redirectToRoute('app_admin_create');

        }
        return $this->render('admin/pre-create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/creation', name:'app_admin_create')]
    public function create(Request $request, SessionInterface $session,
                           EntityManagerInterface $entityManager,
                           UserPasswordHasherInterface $passwordHasher): Response
    {
        $admin = $session->get('admin');

        $form = $this->createForm(AdminType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $plainPassword = $form->get('password')->getData();

            if ($plainPassword) {
                // Si un nouveau mot de passe a été fourni, on le hash et on le met à jour
                $hashedPassword = $passwordHasher->hashPassword($admin, $plainPassword);
                $admin->setPassword($hashedPassword);
            }

            $entityManager->persist($admin);
            $entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur ' . $admin->getFullName() . ' a bien été créé');
            return $this->redirectToRoute('app_admin_index');
        }

        return $this->render('admin/create.html.twig', [
            'form' => $form->createView(),
            'admin' => $admin
        ]);
    }

    #[Route('/{id}/modification', name:'app_admin_edit')]
    public function edit($id, Request $request, EntityManagerInterface $entityManager,
                         UserPasswordHasherInterface $passwordHasher): Response
    {
        $admin = $entityManager->getRepository(Admin::class)->find($id);

        $form = $this->createForm(AdminType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $plainPassword = $form->get('password')->getData();

            if ($plainPassword) {
                // Si un nouveau mot de passe a été fourni, on le hash et on le met à jour
                $hashedPassword = $passwordHasher->hashPassword($admin, $plainPassword);
                $admin->setPassword($hashedPassword);
            }
            // Enregistrer les changements
            $entityManager->persist($admin);
            $entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur ' . $admin->getFullName() . ' a bien été modifié');
            return $this->redirectToRoute('app_admin_index');
        }

        return $this->render('admin/edit.html.twig', [
            'form' => $form->createView(),
            'admin' => $admin
        ]);
    }

    #[Route('/{id}/suppression', name:'app_admin_delete')]
    public function delete($id, EntityManagerInterface $entityManager): Response
    {
        $admin = $entityManager->getRepository(Admin::class)->find($id);

        $admin->setArchived(true);

        $entityManager->flush();

        $this->addFlash('warning', 'L\'utilisateur ' . $admin->getFullName() . ' a bien été archivé');

        return $this->redirectToRoute('app_admin_index');
    }

    #[Route('/{id}/reactivation', name:'app_admin_reactivate')]
    public function reActivate($id, EntityManagerInterface $entityManager): Response
    {
        $admin = $entityManager->getRepository(Admin::class)->find($id);

        $admin->setArchived(false);

        $entityManager->flush();

        $this->addFlash('success', 'L\'utilisateur ' . $admin->getFullName() . ' a bien été réactivé');

        return $this->redirectToRoute('app_admin_index');
    }
}