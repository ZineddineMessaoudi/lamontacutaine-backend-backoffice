<?php

namespace App\Controller\Back;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 *
 * @Route("/categories", name="back_category_")
 */
class CategoryController extends AbstractController
{
    /**
     * Displays a list of categories.
     * 
     * @Route("/liste", name="list")
     * 
     * @param CategoryRepository $categoryRepository The category repository to retrieve categories from the database.
     * 
     * @return Response The response containing the home page template and data of categories.
     */
    public function list(CategoryRepository $categoryRepository): Response
    {
        // Retrieve all categories from the database
        $categories = $categoryRepository->findAll();

        // Render the 'back/category/list.html.twig' template with the controller name and categories data
        return $this->render('back/category/list.html.twig', [
            'controller_name' => 'CategoryController',
            'categories' => $categories
        ]);
    }

    /**
     * Creates a new category
     * 
     * @Route("/creer", name="create", methods={"GET","POST"})
     * 
     * @param Request $request The request containing the category data.
     * @param EntityManagerInterface $entityManager The entity manager to persist the category in the database.
     * 
     * @return Response The response containing the category creation form.
     */
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Create a new instance of the Category entity
        $category = new Category();

        // Create a form using the CategoryType form type and bind it to the Category entity
        $form = $this->createForm(CategoryType::class, $category);

        // Handle the form submission and validation
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the Category entity to the database
            $entityManager->persist($category);
            $entityManager->flush();

            // Add a flash message to indicate the successful creation of the category
            $this->addFlash('success', 'La catégorie ' . $category->getName() . ' a bien été créée');

            // Redirect to the category list page
            return $this->redirectToRoute('back_category_list');
        }

        // Render the category creation form template with the form
        return $this->renderForm('back/category/add_edit.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * Updates a category
     * 
     * @Route("/{id<\d+>}/mise-a-jour", name="update", methods={"GET","POST"})
     * 
     * @param Request $request The request containing the category data.
     * @param EntityManagerInterface $entityManager The entity manager to persist the category in the database.
     * 
     * @return Response The response containing the category update form.
     */
    public function update(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Retrieve the category from the database based on the provided ID
        $category = $entityManager->getRepository(Category::class)->find($request->attributes->get('id'));

        // If the category does not exist, throw an exception
        if (null === $category) {
            throw $this->createNotFoundException("La catégorie n'existe pas");
        }

        // Create a form instance for updating the category, passing the category object
        $form = $this->createForm(CategoryType::class, $category);

        // Handle the form submission and validation
        $form->handleRequest($request);

        // If the form is submitted and valid, update the category in the database
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();
            $this->addFlash('success', 'La catégorie ' . $category->getName() . ' a bien été mise à jour');
            return $this->redirectToRoute('back_category_list');
        }
        // If the form is submitted but not valid, display an error message
        else if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'La catégorie n\'a pas pu être mise à jour');
        }

        // Render the form for updating the category
        return $this->renderForm('back/category/add_edit.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * Deletes a category.
     * 
     * @Route("/{id<\d+>}/supprimer", name="delete", methods={"POST"})
     * 
     * @param Request $request The request containing the category id.
     * @param CategoryRepository $categoryRepository The category repository to retrieve categories from the database.
     * 
     * @return Response The response redirecting to the category list.
     */
    public function delete(Request $request, CategoryRepository $categoryRepository): Response
    {
        // Find the category with the specified id
        $category = $categoryRepository->find($request->attributes->get('id'));

        // If the category is not found, throw an exception
        if (null === $category) {
            throw $this->createNotFoundException("La catégorie n'existe pas");
        }
        // If the CSRF token is valid
        else if ($this->isCsrfTokenValid('delete-category-' . $category->getId(), $request->request->get('_token'))) {
            // Remove the category from the database
            $categoryRepository->remove($category, true);
            // Add a success flash message
            $this->addFlash('success', 'La catégorie ' . $category->getName() . ' a bien été supprimée');
        }

        // Redirect to the category list page
        return $this->redirectToRoute('back_category_list', [], Response::HTTP_SEE_OTHER);
    }
}
