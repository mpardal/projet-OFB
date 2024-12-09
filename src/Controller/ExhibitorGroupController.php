<?php

namespace App\Controller;

use App\Entity\ExhibitorGroup;
use App\Form\ExhibitorGroupType;
use App\Form\GroupNameVerificationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/groupe_exposition')]
class ExhibitorGroupController extends AbstractController
{
    #[Route('/', name:'app_exhibitor_group_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $exhibitorsGroup = $entityManager->getRepository(ExhibitorGroup::class)->findBy(
            [
                'archived' => false
            ],
            [
                'groupName' => 'DESC'
            ]
        );

        $exhibitorsGroupArchived = $entityManager->getRepository(ExhibitorGroup::class)->findBy(
            [
                'archived' => true
            ],
            [
                'groupName' => 'DESC'
            ]
        );

        return $this->render('exhibitorGroup/index.html.twig', [
            'exhibitorsGroup' => $exhibitorsGroup,
            'exhibitorsGroupArchived' => $exhibitorsGroupArchived
        ]);
    }

    #[Route('/pre_creation', name:'app_exhibitor_group_pre_create')]
    public function preCreate(Request $request, EntityManagerInterface $entityManager,
                              SessionInterface $session): Response
    {
        $form = $this->createForm(GroupNameVerificationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $exhibitorGroupExist = $entityManager->getRepository(ExhibitorGroup::class)->findOneBy(
                ['groupName' => $form->get('groupName')->getData()]
            );

            if (null !== $exhibitorGroupExist) {
                $this->addFlash('danger', 'Le groupe ' . $exhibitorGroupExist->getGroupName() . ' existe deja');
                return $this->redirectToRoute('app_exhibitor_group_pre_create');
            }

            $exhibitorGroup = new ExhibitorGroup();
            $exhibitorGroup->setGroupName($form->get('groupName')->getData());

            $session->set('exhibitorGroup', $exhibitorGroup);

            return $this->redirectToRoute('app_exhibitor_group_create');
        }
        return $this->render('exhibitorGroup/pre-create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/creation', name:'app_exhibitor_group_create')]
    public function create(Request $request, EntityManagerInterface $entityManager,
                           SessionInterface $session): Response
    {
        $exhibitorGroup = $session->get('exhibitorGroup');
        $form = $this->createForm(ExhibitorGroupType::class, $exhibitorGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le groupe ' . $exhibitorGroup->getGroupName() . ' a bien été créé');
            return $this->redirectToRoute('app_exhibitor_group_index');
        }

        return $this->render('exhibitorGroup/create.html.twig', [
            'form' => $form->createView(),
            'exhibitorGroup' => $exhibitorGroup
        ]);
    }

    #[Route('/modification/{id}', name:'app_exhibitor_group_edit')]
    public function edit($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $exhibitorGroup = $entityManager->getRepository(ExhibitorGroup::class)->find($id);

        $form = $this->createForm(ExhibitorGroupType::class, $exhibitorGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le groupe ' . $exhibitorGroup->getGroupName() . ' a bien été modifié');
            return $this->redirectToRoute('app_exhibitor_group_index');
        }
        return $this->render('exhibitorGroup/edit.html.twig', [
            'form' => $form->createView(),
            'exhibitorGroup' => $exhibitorGroup
        ]);
    }

    #[Route('/suppression/{id}', name:'app_exhibitor_group_delete')]
    public function delete($id, EntityManagerInterface $entityManager): Response
    {
        $exhibitorGroup = $entityManager->getRepository(ExhibitorGroup::class)->find($id);

        $exhibitorGroup->setArchived(true);

        $entityManager->flush();

        $this->addFlash('warning', 'Le groupe ' . $exhibitorGroup->getGroupName() . ' a bien été archivé');

        return $this->redirectToRoute('app_exhibitor_group_index');
    }

    #[Route('/{id}/reactivation', name:'app_exhibitor_group_reactivate')]
    public function reActivate($id, EntityManagerInterface $entityManager): Response
    {
        $exhibitorGroup = $entityManager->getRepository(ExhibitorGroup::class)->find($id);

        $exhibitorGroup->setArchived(false);

        $entityManager->flush();

        $this->addFlash('success', 'Le groupe ' . $exhibitorGroup->getGroupName() . ' a bien été réactivé');

        return $this->redirectToRoute('app_exhibitor_group_index');
    }
}