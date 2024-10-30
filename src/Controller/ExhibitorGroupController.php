<?php

namespace App\Controller;

use App\Entity\ExhibitorGroup;
use App\Entity\Admin;
use App\Form\EmailVerificationType;
use App\Form\AdminType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/exhibitors_group')]
class ExhibitorGroupController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name:'app_exhibitor_group_index')]
    public function index(): Response
    {
        $exhibitors = $this->entityManager->getRepository(ExhibitorGroup::class)->findBy(
            ['roles' => 'ROLE_ADMIN']
        );

        return $this->render('exhibitorGroup/index.html.twig', [
            'exhibitors' => $exhibitors
        ]);
    }

    #[Route('/{id}/pre_creation', name:'app_exhibitor_group_pre_create')]
    public function preCreate(Request $request): Response
    {
        $form = $this->createForm(EmailVerificationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->entityManager;
            $exhibitor = new Admin();
            $exhibitor->setEmail($form->get('email')->getData());
            $exhibitor->setRoles(['ROLE_ADMIN']);

            $entityManager->persist($exhibitor);
            $entityManager->flush();

            //$session->set('user', $exhibitor);

            return $this->redirectToRoute('app_exhibitor_group_create', [
                'id' => $exhibitor->getId()
            ]);
        }
        return $this->render('exhibitorGroup/pre-create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/creation', name:'app_exhibitor_group_create')]
    public function create($id, Request $request): Response
    {
        $entityManager = $this->entityManager;
        $exhibitor = $entityManager->getRepository(Admin::class)->find($id);
        $form = $this->createForm(AdminType::class, $exhibitor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur ' . $exhibitor->getFullName() . ' a bien été créé');
            return $this->redirectToRoute('app_exhibitor_group_index');
        }

        return $this->render('exhibitorGroup/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/modification', name:'app_exhibitor_group_edit')]
    public function edit($id, Request $request): Response
    {
        $entityManager = $this->entityManager;
        $exhibitor = $entityManager->getRepository(Admin::class)->find($id);

        $form = $this->createForm(AdminType::class, $exhibitor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur ' . $exhibitor->getFullName() . ' a bien été modifié');
            return $this->redirectToRoute('app_exhibitor_group_index');
        }
        return $this->render('exhibitorGroup/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/suppression', name:'app_exhibitor_group_delete')]
    public function delete($id): Response
    {
        $entityManager = $this->entityManager;
        $exhibitor = $entityManager->getRepository(Admin::class)->find($id);

        $exhibitor->setArchived(true);

        $entityManager->flush();

        $this->addFlash('warning', 'L\'utilisateur ' . $exhibitor->getFullName() . ' a bien été archivé');

        return $this->redirectToRoute('app_exhibitor_group_index');
    }

}