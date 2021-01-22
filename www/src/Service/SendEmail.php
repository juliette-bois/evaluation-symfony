<?php

namespace App\Service;

use App\Entity\Article;
use App\Entity\Comment;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class SendEmail
{
    private $mailer;
    private $userRepository;

    public function __construct(MailerInterface $mailer, UserRepository $userRepository)
    {
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
    }

    public function sendCommentCreateMessage(Comment $comment, Article $article)
    {
        $user = $comment->getUser();
        $email = (new TemplatedEmail())
            ->from(Address::create('Mon Blog <contact@leblog.com>'))
            ->to(Address::create($user->getUsername() . ' <' . $user->getEmail() . '>'))
            ->subject("Merci d'avoir posté un commentaire " . $user->getUsername() . " !")
            ->htmlTemplate('emails/comment.html.twig')
            ->context([
                'author' => $user->getUsername(),
                'createdAt' => $comment->getCreatedAt(),
                'articleName' => $article->getTitle(),
                'articleId' => $article->getId()
            ]);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }

        $admin = $this->userRepository->findOneBy(['isAdmin' => 1]);
        $email = (new TemplatedEmail())
            ->from(Address::create('Mon Blog <contact@leblog.com>'))
            ->to(Address::create($admin->getUsername() . ' <' . $admin->getEmail() . '>'))
            ->subject("Un commentaire est en attente de review")
            ->htmlTemplate('emails/comment-toreview.html.twig')
            ->context([
                'author' => $user->getUsername(),
                'createdAt' => $comment->getCreatedAt(),
                'articleName' => $article->getTitle(),
                'articleId' => $article->getId()
            ]);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }
    }

    public function sendCommentConfirmMessage(Comment $comment, Article $article)
    {
        $user = $comment->getUser();
        $email = (new TemplatedEmail())
            ->from(Address::create('Mon Blog <contact@leblog.com>'))
            ->to(Address::create($user->getUsername() . ' <' . $user->getEmail() . '>'))
            ->subject("Votre commentaire a été validé !")
            ->htmlTemplate('emails/comment-validated.html.twig')
            ->context([
                'author' => $user->getUsername(),
                'createdAt' => $comment->getCreatedAt(),
                'articleName' => $article->getTitle(),
                'articleId' => $article->getId()
            ]);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }
    }

    public function sendCommentRefusedMessage(Comment $comment, Article $article)
    {
        $user = $comment->getUser();
        $email = (new TemplatedEmail())
            ->from(Address::create('Mon Blog <contact@leblog.com>'))
            ->to(Address::create($user->getUsername() . ' <' . $user->getEmail() . '>'))
            ->subject("Votre commentaire a été refusé.")
            ->htmlTemplate('emails/comment-refused.html.twig')
            ->context([
                'author' => $user->getUsername(),
                'createdAt' => $comment->getCreatedAt(),
                'articleName' => $article->getTitle(),
                'articleId' => $article->getId()
            ]);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }
    }

    public function sendArticleCreateMessage(Article $article)
    {
        $user = $article->getUser();
        $email = (new TemplatedEmail())
            ->from(Address::create('Mon Blog <contact@leblog.com>'))
            ->to(Address::create($user->getUsername() . ' <' . $user->getEmail() . '>'))
            ->subject("Merci d'avoir posté un Article " . $user->getUsername() . " !")
            ->htmlTemplate('emails/article.html.twig')
            ->context([
                'author' => $user->getUsername(),
                'createdAt' => $article->getCreatedAt(),
                'articleName' => $article->getTitle(),
                'articleId' => $article->getId()
            ]);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }

        $admin = $this->userRepository->findOneBy(['isAdmin' => 1]);
        $email = (new TemplatedEmail())
            ->from(Address::create('Mon Blog <contact@leblog.com>'))
            ->to(Address::create($admin->getUsername() . ' <' . $admin->getEmail() . '>'))
            ->subject("Un article est en attente de review")
            ->htmlTemplate('emails/article-toreview.html.twig')
            ->context([
                'author' => $user->getUsername(),
                'createdAt' => $article->getCreatedAt(),
                'articleName' => $article->getTitle(),
                'articleId' => $article->getId()
            ]);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }
    }

    public function sendArticleConfirmMessage(Article $article)
    {
        $user = $article->getUser();
        $email = (new TemplatedEmail())
            ->from(Address::create('Mon Blog <contact@leblog.com>'))
            ->to(Address::create($user->getUsername() . ' <' . $user->getEmail() . '>'))
            ->subject("Votre commentaire a été validé !")
            ->htmlTemplate('emails/comment-validated.html.twig')
            ->context([
                'author' => $user->getUsername(),
                'createdAt' => $article->getCreatedAt(),
                'articleName' => $article->getTitle(),
                'articleId' => $article->getId()
            ]);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }
    }

    public function sendArticleRefusedMessage(Article $article)
    {
        $user = $article->getUser();
        $email = (new TemplatedEmail())
            ->from(Address::create('Mon Blog <contact@leblog.com>'))
            ->to(Address::create($user->getUsername() . ' <' . $user->getEmail() . '>'))
            ->subject("Votre article a été refusé")
            ->htmlTemplate('emails/article-refused.html.twig')
            ->context([
                'author' => $user->getUsername(),
                'createdAt' => $article->getCreatedAt(),
                'articleName' => $article->getTitle(),
                'articleId' => $article->getId()
            ]);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }
    }
}
