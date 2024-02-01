<?php

namespace App\Controller\Api;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * Retrieves the categories for the agenda.
     *
     * @Route("/api/v1/categories", name="api_v1_categories", methods={"GET"})
     *
     * @param CategoryRepository $categoryRepository The category repository.
     * @return JsonResponse The JSON response containing the categories array or an error message.
     */
    public function list(CategoryRepository $categoryRepository): JsonResponse
    {
        // Retrieve all categories from the category repository
        $categories = $categoryRepository->findAll();

        // If no categories are found, return an error response
        if (empty($categories)) {
            return $this->json(['error' => 'Categories not found'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Return a JSON response containing the categories array
        // Use the "categorieslist" serialization group to include only necessary fields
        return $this->json(['categories' => $categories], Response::HTTP_OK, [], ['groups' => 'categorieslist']);
    }
}
