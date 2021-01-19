<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Service\SendEmail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param ArticleRepository $repo
     * @return Response
     */
    public function index(ArticleRepository $repo): Response
    {
        $articles = $repo->findAll();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'articles' => $articles
        ]);
    }

  /**
   * @Route("/article/{id}", name="article_show")
   * @param Article $article
   * @param Request $request
   * @param EntityManagerInterface $manager
   * @param SendEmail $mailer
   * @return Response
   */
    public function showArticle(Article $article, Request $request, EntityManagerInterface $manager, SendEmail $mailer): Response
    {
        $comment = new Comment();
        $form_comment = $this->createForm(CommentType::class, $comment);
        $form_comment->handleRequest($request);
        if ($form_comment->isSubmitted() && $form_comment->isValid()) {
            $comment->setCreatedAt(new \DateTime())
                    ->setArticle($article);

            $mailer->sendConfirmMessage($comment, $article);

            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('article_show', ['id' => $article->getId()]);
        }

        return $this->render('home/article.html.twig', [
            'article' => $article,
            'form_comment' => $form_comment->createView(),
            'edit_mode' => $article->getId() !== null
        ]);
    }
}
