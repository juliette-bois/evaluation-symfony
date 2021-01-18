<?php

namespace App\Service;

use App\Entity\Article;
use App\Entity\Comment;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class SendEmail
{
  private $mailer;

  public function __construct(MailerInterface $mailer)
  {
    $this->mailer = $mailer;
  }

  public function sendConfirmMessage(Comment $comment, Article $article)
  {
    $email = (new TemplatedEmail())
      ->from(Address::create('Mon Blog <contact@leblog.com>'))
      ->to(Address::create($comment->getAuthor().' <'.$comment->getEmail().'>'))
      ->subject("Merci d'avoir posté un commentaire ".$comment->getAuthor(). " !")
      ->htmlTemplate('emails/comment.html.twig')
      ->context([
        'author' => $comment->getAuthor(),
        'createdAt' => $comment->getCreatedAt(),
        'articleName' => $article->getTitle(),
        'articleId' => $article->getId()
      ]);

    try {
      $this->mailer->send($email);
    } catch (TransportExceptionInterface $e) {
    }
  }
}
