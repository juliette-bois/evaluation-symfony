<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Service\SendEmail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param ArticleRepository $repo
     * @param CategoryRepository $repo_categories
     * @param Request $request
     * @return Response
     */
    public function index(ArticleRepository $repo, CategoryRepository $repo_categories, Request $request): Response
    {
        $choice = $request->query->get('category');

        $categories = [];
        foreach ($repo_categories->findAll() as $category) {
            $categories[$category->getTitle()] = $category->getId();
        }

        $form = $this->createFormBuilder()
            ->add('choice', ChoiceType::class, [
                'placeholder' => 'Choisir une catégorie',
                'choices'  => $categories,
                'data' => $choice,
            ])
            ->getForm();

        $form->handleRequest($request);

        if (in_array($choice, $categories)) {
            $articles = $repo->findOneByCategory($choice);
        } else {
            $articles = $repo->findAll();
        }

        return $this->render('home/index.html.twig', [
            'articles' => $articles,
            'categories' => $categories,
            'categories_select' => $form->createView(),
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

        $submittedToken = $request->request->get('comment')['_token'];

        if ($this->isCsrfTokenValid('comment_form', $submittedToken)) {
            if ($form_comment->isSubmitted() && $form_comment->isValid()) {
                $comment->setCreatedAt(new \DateTime())
                    ->setArticle($article);

                $mailer->sendConfirmMessage($comment, $article);

                $manager->persist($comment);
                $manager->flush();

                return $this->redirectToRoute('article_show', ['id' => $article->getId()]);
            }
        }

        return $this->render('home/article.html.twig', [
            'article' => $article,
            'form_comment' => $form_comment->createView(),
            'edit_mode' => $article->getId() !== null
        ]);
    }
}
