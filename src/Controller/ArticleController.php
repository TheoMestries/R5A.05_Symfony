<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Form\ResponseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ArticleController extends AbstractController
{
    #[Route('/create', name: 'create_article')]
    public function create(Request $request, Security $security, EntityManagerInterface $entityManager)
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $article->setPublicationDate(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
            $user = $security->getUser();
            $article->setAuthor($user);
            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', 'Article créé avec succès!');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('article/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/article/{id}', name: 'article_show')]
    public function show(Article $article, Request $request, EntityManagerInterface $em): HttpResponse
    {
        $responses = $em->getRepository(\App\Entity\Response::class)->findBy(
            ['article' => $article],
            ['publicationDate' => 'DESC']
        );

        $newResponse = new \App\Entity\Response();
        $form = $this->createForm(ResponseType::class, $newResponse);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->isGranted('ROLE_SUPERVISEUR')) {
                throw $this->createAccessDeniedException('Vous n\'avez pas la permission d\'ajouter une réponse.');
            }

            $newResponse->setPublicationDate(new \DateTime());
            $newResponse->setAuthor($this->getUser());
            $newResponse->setArticle($article);

            $em->persist($newResponse);
            $em->flush();

            return $this->redirectToRoute('article_show', ['id' => $article->getId()]);
        }

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'responses' => $responses,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/article/{id}/edit', name: 'article_edit')]
    public function edit(Article $article, Request $request, EntityManagerInterface $entityManager)
    {
        if ($this->getUser() !== $article->getAuthor()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas l\'auteur de cet article.');
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('article_show', ['id' => $article->getId()]);
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/article/{id}/delete', name: 'article_delete', methods: ['POST'])]
    public function delete(Article $article, Request $request, EntityManagerInterface $entityManager)
    {
        if ($this->getUser() !== $article->getAuthor()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas l\'auteur de cet article.');
        }

        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->request->get('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
            $this->addFlash('success', 'Article supprimé avec succès!');
        }

        return $this->redirectToRoute('app_home');
    }

    #[Route('/response/{id}/edit', name: 'response_edit', methods: ['POST'])]
    public function editResponse(\App\Entity\Response $response, Request $request, EntityManagerInterface $em): JsonResponse
    {
        if ($this->getUser() !== $response->getAuthor()) {
            return new JsonResponse(['error' => 'Vous n\'êtes pas l\'auteur de cette réponse.'], 403);
        }

        $content = $request->request->get('content');
        if (!$content) {
            return new JsonResponse(['error' => 'Le contenu ne peut pas être vide.'], 400);
        }

        $response->setContent($content);
        $em->flush();

        return new JsonResponse(['message' => 'Réponse mise à jour avec succès.']);
    }

    #[Route('/response/{id}/delete', name: 'delete_response', methods: ['POST'])]
    public function deleteResponse(\App\Entity\Response $response, EntityManagerInterface $em): JsonResponse
    {
        if ($this->getUser() !== $response->getAuthor()) {
            return new JsonResponse(['error' => 'Vous n\'êtes pas l\'auteur de cette réponse.'], 403);
        }

        $em->remove($response);
        $em->flush();

        return new JsonResponse(['message' => 'Réponse supprimée avec succès!']);
    }


}


