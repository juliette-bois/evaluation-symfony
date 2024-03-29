<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class CategoryController extends AbstractController
{
    /**
     * @Route("/new/category", name="new_category")
     * @Route("/category/{id}/edit", name="edit_category")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param Security $security
     * @param FileUploader $fileUploader
     * @param Category|null $category
     * @return Response
     */
    public function index(Request $request, EntityManagerInterface $manager, Security $security, FileUploader $fileUploader, Category $category = null): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('home');
        }

        if (!$category) {
            $category = new Category();
        }

        $submittedToken = $request->request->get('category')['_token'];

        $category_form = $this->createForm(CategoryType::class, $category);

        $category_form->handleRequest($request);

        if ($this->isCsrfTokenValid('category_form', $submittedToken)) {
            if ($category_form->isSubmitted() && $category_form->isValid()) {
                $manager->persist($category);
                $manager->flush();

                return $this->redirectToRoute('show_categories');
            }
        }

        return $this->render('category/create.html.twig', [
            'form_article' => $category_form->createView(),
            'edit_mode' => $category->getId() !== null
        ]);
    }


    /**
     * @Route("categories", name="show_categories")
     * @param CategoryRepository $categoryRepo
     * @return Response
     */
    public function showCategories(CategoryRepository  $categoryRepo): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('home');
        }

        $categories = $categoryRepo->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $categories
        ]);
    }


  /**
   * @Route("/delete/category", name="delete_category")
   * @param CategoryRepository $categoryRepository
   * @param Request $request
   * @return Response
   */
    public function deleteCategory(CategoryRepository $categoryRepository, Request $request): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Access Denied.');
        }

        $values = json_decode($request->getContent(), true);

        if (!$this->isCsrfTokenValid('delete-category', $values['_token'])) {
            throw $this->createAccessDeniedException('Access Denied.');
        }

        $category = $categoryRepository->find($values['id']);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($category);
        $entityManager->flush();

        return new Response(true);
    }
}
