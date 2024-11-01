<?php

namespace App\Controller;

use App\Entity\ExhibitorGroup;
use App\Entity\Admin;
use App\Form\EmailVerificationType;
use App\Form\AdminType;
use App\Form\ExhibitorGroupType;
use App\Form\ExhibitorType;
use App\Form\GroupNameVerificationType;
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
        $exhibitorsGroup = $this->entityManager->getRepository(ExhibitorGroup::class)->findAll();

        return $this->render('exhibitorGroup/index.html.twig', [
            'exhibitorsGroup' => $exhibitorsGroup
        ]);
    }

    #[Route('/{id}/pre_creation', name:'app_exhibitor_group_pre_create')]
    public function preCreate(Request $request): Response
    {
        $form = $this->createForm(GroupNameVerificationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->entityManager;

            $exhibitorGroupExist = $entityManager->getRepository(ExhibitorGroup::class)->findOneBy(
                ['groupName' => $form->get('groupName')->getData()]
            );

            if (null !== $exhibitorGroupExist) {
                $this->addFlash('danger', 'Le stand ' . $exhibitorGroupExist->getGroupName() . ' existe deja');
                return $this->redirectToRoute('app_exhibitor_group_pre_create');
            }

            $exhibitorGroup = new ExhibitorGroup();
            $exhibitorGroup->setGroupName($form->get('groupName')->getData());

            $entityManager->persist($exhibitorGroup);
            $entityManager->flush();

            return $this->redirectToRoute('app_exhibitor_group_create', [
                'id' => $exhibitorGroup->getId()
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
        $exhibitorGroup = $entityManager->getRepository(ExhibitorGroup::class)->find($id);
        $form = $this->createForm(ExhibitorGroupType::class, $exhibitorGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le stand ' . $exhibitorGroup->getGroupName() . ' a bien été créé');
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
        $exhibitorGroup = $entityManager->getRepository(ExhibitorGroup::class)->find($id);

        $form = $this->createForm(ExhibitorGroupType::class, $exhibitorGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Le stand ' . $exhibitorGroup->getGroupName() . ' a bien été modifié');
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
        $exhibitorGroup = $entityManager->getRepository(ExhibitorGroup::class)->find($id);

        $exhibitorGroup->setArchived(true);

        $entityManager->flush();

        $this->addFlash('warning', 'Le stand ' . $exhibitorGroup->getGroupName() . ' a bien été archivé');

        return $this->redirectToRoute('app_exhibitor_group_index');
    }

}