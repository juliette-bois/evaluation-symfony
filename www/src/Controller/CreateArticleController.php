<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\ArticleType;
use Symfony\Component\Routing\Annotation\Route;

class CreateArticleController extends AbstractController
{
  /**
   * @Route("/new/article", name="new_article")
   * @Route("/article/{id}/edit", name="edit_article")
   * @param Request $request
   * @param EntityManagerInterface $manager
   * @param Article|null $article
   * @param User $user
   * @return Response
   */
    public function index(Request $request, EntityManagerInterface $manager, Article $article = null): Response
    {
        if (!$article) {
            $article = new Article();
        }

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$article->getId()) {
                $article->setCreatedAt(new \DateTime());
            }

            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('article_show', ['id' => $article->getId()]);
        }

        return $this->render('create_article/index.html.twig', [
            'form_article' => $form->createView(),
            'edit_mode' => $article->getId() !== null
        ]);
    }
}
