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
use Symfony\Component\Security\Core\Security;

class CommentController extends AbstractController
{
  /**
   * @Route("comments", name="show_comments")
   * @param CommentRepository $commentRepo
   * @return Response
   */
    public function showComments(CommentRepository  $commentRepo): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('home');
        }

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
     * @param Security $security
     * @return Response
     */
    public function updateComment(Comment $comment, Request $request, EntityManagerInterface $manager, Security $security): Response
    {
        if ((!$security->getUser() || $security->getUser()->getUsername() !== $comment->getAuthor()) && !$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('home');
        }

        $submittedToken = $request->request->get('comment')['_token'];

        $form_comment = $this->createForm(CommentType::class, $comment);

        $form_comment->handleRequest($request);

        if ($this->isCsrfTokenValid('comment_form', $submittedToken)) {
            if ($form_comment->isSubmitted() && $form_comment->isValid()) {
                $manager->persist($comment);
                $manager->flush();

                return $this->redirectToRoute('show_articles');
            }
        }

        return $this->render('comment/update.html.twig', [
            'form_comment' => $form_comment->createView()
        ]);
    }


    /**
     * @Route("/delete/comment/{id}", name="delete_comment")
     * @param Comment $comment
     * @param Request $request
     * @param Security $security
     * @return Response
     */
    public function deleteComment(Comment $comment, Request $request, Security $security): Response
    {
        if ((!$security->getUser() || $security->getUser()->getUsername() !== $comment->getAuthor()) && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Access Denied.');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($comment);
        $entityManager->flush();

        return $this->redirectToRoute('articles');
    }
}
