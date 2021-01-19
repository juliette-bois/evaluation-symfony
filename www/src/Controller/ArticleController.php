<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\ArticleType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use App\Service\FileUploader;

class ArticleController extends AbstractController
{
    /**
     * @Route("/new/article", name="new_article")
     * @Route("/article/{id}/edit", name="edit_article")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param Security $security
     * @param FileUploader $fileUploader
     * @param Article|null $article
     * @return Response
     */
    public function index(Request $request, EntityManagerInterface $manager, Security $security, FileUploader $fileUploader, Article $article = null): Response
    {
        if (!$article) {
            $article = new Article();
        }

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile);
                $article->setImage($imageFileName);
            }

            if (!$article->getId()) {
                $article->setCreatedAt(new \DateTime());
                $article->setCreatedBy($security->getUser()->getUsername());
            }

            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('article_show', [
              'id' => $article->getId()
            ]);
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
        $articles = $articleRepo->findBy(["createdBy" => $security->getUser()->getUsername()]);
        $comments = $commentRepo->findBy(["author" => $security->getUser()->getUsername()]);

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
            'comments' => $comments
        ]);
    }


    /**
     * @Route("/delete/article/{id}", name="delete_article")
     * @param Article $article
     * @return Response
     */
    public function deleteArticle(Article $article): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($article);
        $entityManager->flush();

        return $this->redirectToRoute('home');
    }
}
