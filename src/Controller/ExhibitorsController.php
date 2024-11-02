<?php

namespace App\Controller;

use App\Entity\Exhibitor;
use App\Form\EmailVerificationType;

use App\Form\ExhibitorType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/exhibitors')]
class ExhibitorsController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name:'app_exhibitors_index')]
    public function index(): Response
    {
        $exhibitors = $this->entityManager->getRepository(Exhibitor::class)->findBy(
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

    #[Route('/{id}/pre_creation', name:'app_exhibitors_pre_create')]
    public function preCreate(Request $request): Response
    {
        $form = $this->createForm(EmailVerificationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->entityManager;
            $exhibitor = new Exhibitor();
            $exhibitor->setEmail($form->get('email')->getData());
            $exhibitor->setRoles(['ROLE_EXHIBITOR']);

            $entityManager->persist($exhibitor);
            $entityManager->flush();

            return $this->redirectToRoute('app_exhibitors_create', [
                'id' => $exhibitor->getId()
            ]);
        }
        return $this->render('exhibitor/pre-create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/creation', name:'app_exhibitors_create')]
    public function create($id, Request $request): Response
    {
        $entityManager = $this->entityManager;
        $exhibitor = $entityManager->getRepository(Exhibitor::class)->find($id);
        $form = $this->createForm(ExhibitorType::class, $exhibitor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur ' . $exhibitor->getFullName() . ' a bien été créé');
            return $this->redirectToRoute('app_exhibitors_index');
        }

        return $this->render('exhibitor/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/modification', name:'app_exhibitors_edit')]
    public function edit($id, Request $request): Response
    {
        $entityManager = $this->entityManager;
        $exhibitor = $entityManager->getRepository(Exhibitor::class)->find($id);

        $form = $this->createForm(ExhibitorType::class, $exhibitor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur ' . $exhibitor->getFullName() . ' a bien été modifié');
            return $this->redirectToRoute('app_exhibitors_index');
        }
        return $this->render('exhibitor/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/suppression', name:'app_exhibitors_delete')]
    public function delete($id): Response
    {
        $entityManager = $this->entityManager;
        $exhibitor = $entityManager->getRepository(Exhibitor::class)->find($id);

        $exhibitor->setArchived(true);

        $entityManager->flush();

        $this->addFlash('warning', 'L\'utilisateur ' . $exhibitor->getFullName() . ' a bien été archivé');

        return $this->redirectToRoute('app_exhibitors_index');

    }

}