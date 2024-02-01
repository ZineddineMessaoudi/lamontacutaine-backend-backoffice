<?php

namespace App\Controller\Api;

use App\Entity\Member;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LoginController extends AbstractController
{
    /**
     * This function checks the login credentials sent via POST request to the "/api/v2/checklogin" endpoint.
     * If the credentials are valid, it returns the user's information in JSON format.
     * If the credentials are invalid, it returns an error message in JSON format.
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     *
     * @Route("/api/v2/checklogin", name="app_api_checklogin", methods={"POST"})
     */
    public function checkLogin(Request $request, EntityManagerInterface $em): Response
    {
        // Get the request jsonData as an associative array
        $jsonData = json_decode($request->getContent(), true);

        // Check if the 'username' or 'password' fields are empty
        if (empty($jsonData['username']) || empty($jsonData['password'])) {
            // Return an error response with a message and HTTP status code 400
            return new JsonResponse('Champs vides', RESPONSE::HTTP_BAD_REQUEST);
        }

        // Get the user repository
        $userRepository = $em->getRepository(User::class);

        // Find the user by their email
        $user = $userRepository->findOneBy(['email' => $jsonData['username']]);

        // Check if the user is not a member
        if (!$user->getMember()) {
            // Return an error response with a message and HTTP status code 401
            return new JsonResponse("L'utilisateur n'est pas membre", RESPONSE::HTTP_UNAUTHORIZED);
        }
        // Check if the password is correct
        else if ($user instanceof User && password_verify($jsonData['password'], $user->getMember()->getPassword())) {
            // Convert the user object to JSON format
            $member = json_encode($user);
            // Return the user information as a response with HTTP status code 200
            return new JsonResponse($member, RESPONSE::HTTP_OK, [], true);
        }

        // Return an error response for invalid credentials with a message and HTTP status code 400
        return new JsonResponse('Identifiants invalides', RESPONSE::HTTP_UNAUTHORIZED);
    }
}
