<?php

namespace App\Controller;

use App\Entity\DashboardArticle;
use App\Form\DashboardArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/article_accueil')]
class DashboardArticleController extends AbstractController
{
    #[Route('/', name:'app_dashboard_article_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $articles = $entityManager->getRepository(DashboardArticle::class)->findAll();

        return $this->render('dashboardArticle/index.html.twig', [
            'articles' => $articles
        ]);
    }

    #[Route('/creation', name:'app_dashboard_article_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new DashboardArticle();
        $form = $this->createForm(DashboardArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', 'L\'article ' . $article->getTitle() . ' a bien été créé');
            return $this->redirectToRoute('app_dashboard_article_index');
        }

        return $this->render('dashboardArticle/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/modification', name:'app_dashboard_article_edit')]
    public function edit($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = $entityManager->getRepository(DashboardArticle::class)->findOneBy($id);
        $form = $this->createForm(DashboardArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'L\'article ' . $article->getTitle() . ' a bien été modifié');
            return $this->redirectToRoute('app_dashboard_article_index');
        }
        return $this->render('dashboardArticle/edit.html.twig', [
            'form' => $form->createView(),
            'article' => $article
        ]);
    }

    #[Route('/{id}/suppression', name:'app_dashboard_article_delete')]
    public function delete($id, EntityManagerInterface $entityManager): Response
    {
        $article = $entityManager->getRepository(DashboardArticle::class)->findOneBy($id);

        $article->setArchived(true);

        $entityManager->flush();

        $this->addFlash('warning', 'L\'article ' . $article->getTitle() . ' a bien été archivé');

        return $this->redirectToRoute('app_dashboard_article_index');
    }

}