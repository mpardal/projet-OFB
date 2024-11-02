<?php

namespace App\Controller;

use App\Entity\Competition;
use App\Entity\Event;
use App\Entity\ExhibitorGroup;
use App\Entity\Member;
use App\Form\CompetitionType;
use App\Form\EventType;
use App\Form\ExhibitorGroupType;
use App\Form\GroupNameVerificationType;
use App\Form\MemberType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/membres_equipe')]
class MemberController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name:'app_member_index')]
    public function index(): Response
    {
        $members = $this->entityManager->getRepository(Member::class)->findAll();

        return $this->render('member/index.html.twig', [
            '$member' => $members
        ]);
    }

    #[Route('/creation', name:'app_member_create')]
    public function create(Request $request): Response
    {
        $entityManager = $this->entityManager;
        $form = $this->createForm(MemberType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $member = new Member();
            $entityManager->persist($member);
            $entityManager->flush();

            $this->addFlash('success', 'Le membre ' . $member->getFullName() . ' a bien été créé');
            return $this->redirectToRoute('app_member_index');
        }

        return $this->render('member/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/modification', name:'app_member_edit')]
    public function edit($id, Request $request): Response
    {
        $entityManager = $this->entityManager;
        $member = $entityManager->getRepository(Member::class)->findOneBy($id);
        $form = $this->createForm(MemberType::class, $member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Le membre ' . $member->getFullName() . ' a bien été modifié');
            return $this->redirectToRoute('app_member_index');
        }
        return $this->render('member/edit.html.twig', [
            'form' => $form->createView(),
            'member' => $member
        ]);
    }

    #[Route('/{id}/suppression', name:'app_member_delete')]
    public function delete($id): Response
    {
        $entityManager = $this->entityManager;
        $member = $entityManager->getRepository(Member::class)->findOneBy($id);

        $member->setArchived(true);

        $entityManager->flush();

        $this->addFlash('warning', 'Le membre ' . $member->getFullName() . ' a bien été archivé');

        return $this->redirectToRoute('app_member_index');
    }

}