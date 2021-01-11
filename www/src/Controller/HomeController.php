<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/article/{id}", name="article_show")
     * @param Article $article
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function showArticle(Article $article, Request $request, EntityManagerInterface $manager): Response
    {
        $comment = new Comment();
        $form_comment = $this->createForm(CommentType::class, $comment);
        $form_comment->handleRequest($request);
        if ($form_comment->isSubmitted() && $form_comment->isValid()) {
            $comment->setCreatedAt(new \DateTime())
                    ->setArticle($article);

            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('article_show', ['id' => $article->getId()]);
        }

        return $this->render('home/article.html.twig', [
            'article' => $article,
            'form_comment' => $form_comment->createView(),
        ]);
    }

  /**
   * @Route("/articles", name="articles")
   * @param ArticleRepository $repo
   * @return Response
   */
    public function showArticles(ArticleRepository  $repo): Response
    {
      //dd($user);
      $articles = $repo->findAll();
      //dd($articles);

      return $this->render('home/articles.html.twig', [
        'articles' => $articles
      ]);
    }
}
