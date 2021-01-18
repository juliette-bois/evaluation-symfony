<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TranslationsController extends AbstractController
{
    /**
     * @Route("/translations", name="translations")
     */
    public function index(): Response
    {
        return $this->render('translations/index.html.twig', [
            'controller_name' => 'TranslationsController',
        ]);
    }

  /**
   * @Route("/change_locale/{locale}", name="change_locale")
   * @param $locale
   * @param Request $request
   * @return Response
   */
    public function changeLocale($locale, Request $request): Response
    {
        $request->getSession()->set('_locale', $locale);
        return $this->redirect($request->headers->get('referer'));
    }
}
