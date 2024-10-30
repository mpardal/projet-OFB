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
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/', name:'app_admin_index')]
    public function index(): Response
    {
        $admins = $this->entityManager->getRepository(Admin::class)->findBy(
            ['roles' => 'ROLE_ADMIN']
        );

        return $this->render('admin/index.html.twig', [
            'admins' => $admins
        ]);
    }

    #[Route('pre_creation', name:'app_admin_pre_create')]
    public function preCreate(Request $request): Response
    {
        $form = $this->createForm(EmailVerificationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->entityManager;
            $admin = new Admin();
            $admin->setEmail($form->get('email')->getData());
            $admin->setRoles(['ROLE_ADMIN']);

            $entityManager->persist($admin);
            $entityManager->flush();

            //$session->set('user', $admin);

            return $this->redirectToRoute('app_admin_create', [
                'id' => $admin->getId()
            ]);
        }
        return $this->render('admin/pre-create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/creation', name:'app_admin_create')]
    public function create($id, Request $request): Response
    {
        $entityManager = $this->entityManager;
        $admin = $entityManager->getRepository(Admin::class)->find($id);
        $form = $this->createForm(AdminType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur ' . $admin->getFullName() . ' a bien été créé');
            return $this->redirectToRoute('app_admin_index');
        }

        return $this->render('admin/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/modification', name:'app_admin_edit')]
    public function edit($id, Request $request): Response
    {
        $entityManager = $this->entityManager;
        $admin = $entityManager->getRepository(Admin::class)->find($id);

        $form = $this->createForm(AdminType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur ' . $admin->getFullName() . ' a bien été modifié');
            return $this->redirectToRoute('app_admin_index');
        }

        return $this->render('admin/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/suppression', name:'app_admin_delete')]
    public function delete($id): Response
    {
        $entityManager = $this->entityManager;
        $admin = $entityManager->getRepository(Admin::class)->find($id);

        $admin->setArchived(true);

        $entityManager->flush();

        $this->addFlash('warning', 'L\'utilisateur ' . $admin->getFullName() . ' a bien été archivé');

        return $this->redirectToRoute('app_admin_index');
    }
}