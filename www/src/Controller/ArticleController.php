<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Service\SendEmail;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\ArticleType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use App\Service\FileUploader;
use Symfony\Component\Workflow\WorkflowInterface;

class ArticleController extends AbstractController
{

    /**
     * @Route("/new/article", name="new_article")
     * @Route("/article/{id}/edit", name="edit_article")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param Security $security
     * @param FileUploader $fileUploader
     * @param SendEmail $sendEmailService
     * @param Article|null $article
     * @return Response
     */
    public function index(Request $request, EntityManagerInterface $manager, Security $security, FileUploader $fileUploader, SendEmail $sendEmailService, Article $article = null): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('security_login');
        }

        if (!$article) {
            $article = new Article();
        } else {
            if ((!$security->getUser() || $security->getUser()->getId() !== $article->getUser()->getId()) && !$this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('security_login');
            }
        }


        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $submittedToken = $request->request->get('article')['_token'];
            if ($this->isCsrfTokenValid('article_form', $submittedToken)) {
                $imageFile = $form->get('image')->getData();
                if ($imageFile) {
                    $imageFileName = $fileUploader->upload($imageFile);
                    $article->setImage($imageFileName);
                }

                if (!$article->getId()) {
                    $article->setCreatedAt(new \DateTime());
                    $article->setUser($security->getUser());
                }

                $manager->persist($article);
                $manager->flush();

                $sendEmailService->sendArticleCreateMessage($article);

                return $this->redirectToRoute('article_show', [
                    'id' => $article->getId()
                ]);
            }
        }

        return $this->render('article/create.html.twig', [
            'form_article' => $form->createView(),
            'edit_mode' => $article->getId() !== null,
            'id' => $article->getId()
        ]);
    }


    /**
     * @Route("/articles", name="show_articles")
     * @param ArticleRepository $articleRepo
     * @param CommentRepository $commentRepo
     * @param Security $security
     * @return Response
     */
    public function showArticles(ArticleRepository  $articleRepo, CommentRepository $commentRepo, Security $security): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('home');
        }

        $articles = $articleRepo->findBy(["user" => $security->getUser()->getId()]);
        $comments = $commentRepo->findBy(["user" => $security->getUser()->getId()]);

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
            'comments' => $comments
        ]);
    }


    /**
     * @Route("/validate/article", name="validate_article")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param ArticleRepository $articleRepository
     * @param SendEmail $sendEmail
     * @param WorkflowInterface $articlePublishingStateMachine
     * @return Response
     */
    public function validateArticle(Request $request, EntityManagerInterface $manager, ArticleRepository $articleRepository, SendEmail $sendEmail, WorkflowInterface $articlePublishingStateMachine): Response
    {
        $values = json_decode($request->getContent(), true);

        if (!$this->isCsrfTokenValid('validate-article', $values['_token'])) {
            throw $this->createAccessDeniedException('Access Denied.');
        }

        $article = $articleRepository->find($values['id']);

        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Access Denied.');
        }


        try {
            $articlePublishingStateMachine->apply($article, 'publish');
        } catch (LogicException $exception) {
            return new Response(false);
        }
        $manager->persist($article);
        $manager->flush();
        $sendEmail->sendArticleConfirmMessage($article);

        return new Response(true);
    }


    /**
     * @Route("/refuse/article", name="refuse_article")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param ArticleRepository $articleRepository
     * @param SendEmail $sendEmail
     * @return Response
     */
    public function refuseArticle(Request $request, EntityManagerInterface $manager, ArticleRepository $articleRepository, SendEmail $sendEmail, WorkflowInterface $articlePublishingStateMachine): Response
    {
        $values = json_decode($request->getContent(), true);

        if (!$this->isCsrfTokenValid('refuse-article', $values['_token'])) {
            throw $this->createAccessDeniedException('Access Denied.');
        }

        $article = $articleRepository->find($values['id']);

        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Access Denied.');
        }

        try {
            $articlePublishingStateMachine->apply($article, 'reject');
        } catch (\Exception $exception) {
            return new Response(false);
        }
        $manager->persist($article);
        $manager->flush();
        $sendEmail->sendArticleRefusedMessage($article);

        return new Response(true);
    }


    /**
     * @Route("/delete/article", name="delete_article")
     * @param Request $request
     * @param Security $security
     * @param ArticleRepository $articleRepository
     * @return Response
     */
    public function deleteArticle(Request $request, Security $security, ArticleRepository $articleRepository): Response
    {
        $values = json_decode($request->getContent(), true);

        if (!$this->isCsrfTokenValid('delete-article', $values['_token'])) {
            throw $this->createAccessDeniedException('Access Denied.');
        }

        $article = $articleRepository->find($values['id']);

        if ((!$security->getUser() || $security->getUser()->getId() !== $article->getUser()->getId()) && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Access Denied.');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($article);
        $entityManager->flush();

        return $this->redirectToRoute('home');
    }
}
