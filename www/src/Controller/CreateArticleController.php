<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ArticleType;

class CreateArticleController extends AbstractController
{
    /**
     * @Route("/new/article", name="new_article")
     * @Route("/article/{id}/edit", name="edit_article")
     * @param Article|null $article
     * @param Request $request
     * @param ObjectManager $manager
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
