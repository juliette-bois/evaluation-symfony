<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreateArticleController extends AbstractController
{
    /**
     * @Route("/create/article", name="create_article")
     */
    public function index(): Response
    {
        return $this->render('create_article/index.html.twig', [
            'controller_name' => 'CreateArticleController',
        ]);
    }
}
