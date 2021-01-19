<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
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
        if (!$category) {
            $category = new Category();
        }

        $category_form = $this->createForm(CategoryType::class, $category);

        $category_form->handleRequest($request);

        if ($category_form->isSubmitted() && $category_form->isValid()) {
            $manager->persist($category);
            $manager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('category/create.html.twig', [
            'form_article' => $category_form->createView()
        ]);
    }

  /**
   * @Route("/show/categories", name="show_categories")
   * @param CategoryRepository $categoryRepo
   * @return Response
   */
    public function showCategories(CategoryRepository  $categoryRepo): Response
    {
        $categories = $categoryRepo->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/delete/category/{id}", name="delete_category")
     * @param Category $category
     * @return Response
     */
    public function deleteCategory(Category $category): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($category);
        $entityManager->flush();

        return $this->redirectToRoute('home');
    }
}
