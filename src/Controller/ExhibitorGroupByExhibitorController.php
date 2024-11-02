<?php

namespace App\Controller;

use App\Entity\ExhibitorGroup;
use App\Form\ExhibitorGroupByExhibitorType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/expositions')]
class ExhibitorGroupByExhibitorController extends AbstractController
{
    #[Route('/{id}/modification', name:'app_exhibitor_group_by_exhibitor_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $exhibitorGroup = $entityManager->getRepository(ExhibitorGroup::class)->findOneBy(
            ['id' => $this->getUser()->getExhibitorGroup()]
        );

        $form = $this->createForm(ExhibitorGroupByExhibitorType::class, $exhibitorGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le stand ' . $exhibitorGroup->getGroupName() . ' a bien été modifié');
            return $this->redirectToRoute('app_exhibitor_group_index');
        }
        return $this->render('exhibitorGroup/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

}