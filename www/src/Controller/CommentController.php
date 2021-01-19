<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
  /**
   * @Route("comments", name="show_comments")
   * @param CommentRepository $commentRepo
   * @return Response
   */
    public function showComments(CommentRepository  $commentRepo): Response
    {
        $comments = $commentRepo->findAll();

        return $this->render('comment/index.html.twig', [
            'comments' => $comments
        ]);
    }


    /**
     * @Route("/edit/comment/{id}", name="edit_comment")
     * @param Comment $comment
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function updateComment(Comment $comment, Request $request, EntityManagerInterface $manager): Response
    {
        $form_comment = $this->createForm(CommentType::class, $comment);
        $form_comment->handleRequest($request);
        if ($form_comment->isSubmitted() && $form_comment->isValid()) {
            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('articles');
        }

        return $this->render('comment/update.html.twig', [
            'form_comment' => $form_comment->createView()
        ]);
    }


    /**
     * @Route("/delete/comment/{id}", name="delete_comment")
     * @param Comment $comment
     * @param Request $request
     * @return Response
     */
    public function deleteComment(Comment $comment, Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($comment);
        $entityManager->flush();

        return $this->redirectToRoute('articles');
    }
}
