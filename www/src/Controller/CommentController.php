<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Service\SendEmail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Workflow\WorkflowInterface;

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
        if ((!$security->getUser() || $security->getUser()->getId() !== $comment->getUser()->getId()) && !$this->isGranted('ROLE_ADMIN')) {
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
     * @Route("/validate/comment", name="validate_comment")
     * @param Request $request
     * @param CommentRepository $commentRepository
     * @param SendEmail $sendEmail
     * @return Response
     */
    public function validateComment(Request $request, EntityManagerInterface $manager, CommentRepository $commentRepository, SendEmail $sendEmail, WorkflowInterface $articlePublishingStateMachine): Response
    {
        $values = json_decode($request->getContent(), true);

        if (!$this->isCsrfTokenValid('validate-comment', $values['_token'])) {
            throw $this->createAccessDeniedException('Access Denied.');
        }

        $comment = $commentRepository->find($values['id']);

        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Access Denied.');
        }

        try {
            $articlePublishingStateMachine->apply($comment, 'publish');
        } catch (\Exception $exception) {
            return new Response(false);
        }
        $manager->persist($comment);
        $manager->flush();
        $sendEmail->sendCommentConfirmMessage($comment, $comment->getArticle());

        return new Response(true);
    }


    /**
     * @Route("/refuse/comment", name="refuse_comment")
     * @param Request $request
     * @param CommentRepository $commentRepository
     * @param SendEmail $sendEmail
     * @return Response
     */
    public function refuseComment(Request $request, EntityManagerInterface $manager, CommentRepository $commentRepository, SendEmail $sendEmail, WorkflowInterface $articlePublishingStateMachine): Response
    {
        $values = json_decode($request->getContent(), true);

        if (!$this->isCsrfTokenValid('refuse-comment', $values['_token'])) {
            throw $this->createAccessDeniedException('Access Denied.');
        }

        $comment = $commentRepository->find($values['id']);

        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Access Denied.');
        }

        try {
            $articlePublishingStateMachine->apply($comment, 'reject');
        } catch (\Exception $exception) {
            return new Response(false);
        }
        $manager->persist($comment);
        $manager->flush();
        $sendEmail->sendCommentRefusedMessage($comment, $comment->getArticle());

        return new Response(true);
    }


    /**
     * @Route("/delete/comment", name="delete_comment")
     * @param Request $request
     * @param Security $security
     * @return Response
     */
    public function deleteComment(Request $request, Security $security, CommentRepository $commentRepository): Response
    {

        $values = json_decode($request->getContent(), true);

        if (!$this->isCsrfTokenValid('delete-comment', $values['_token'])) {
            throw $this->createAccessDeniedException('Access Denied.');
        }

        $comment = $commentRepository->find($values['id']);

        if ((!$security->getUser() || $security->getUser()->getId() !== $comment->getUser()->getId()) && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Access Denied.');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($comment);
        $entityManager->flush();

        return new Response(true);
    }
}
