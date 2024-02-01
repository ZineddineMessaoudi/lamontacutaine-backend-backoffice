<?php

namespace App\Controller\Api;

use App\Entity\Member;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

class MemberController extends AbstractController
{

   private $jwtEncoder;

   /**
    * Constructs a new instance of the class.
    *
    * @param JWTEncoderInterface $jwtManager The JWT encoder interface.
    */
   public function __construct(JWTEncoderInterface $jwtManager)
   {
      $this->jwtEncoder = $jwtManager;
   }

   /**
    * Update a member via the API.
    *
    * @Route("/api/v1/member/edit", name="api_v1_member_update", methods="PATCH")
    * 
    * @param EntityManagerInterface $em - The entity manager used to interact with the database.
    * @param Request $request - The HTTP request object.
    * @param SerializerInterface $serializer - The serializer used to deserialize JSON.
    * @param ValidatorInterface $validator - The validator used to validate the member object.
    * 
    * @return JsonResponse - The JSON response containing the updated member object.
    */
   public function update(EntityManagerInterface $em, Request $request, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
   {
      // Extract the token from the request headers
      $token = $request->headers->get('Authorization');
      $token = str_replace('Bearer ', '', $token);

      try {
         // Decode the token
         $payload = $this->jwtEncoder->decode($token);
      } catch (\Exception $e) {
         // Return an error response if the token is invalid
         return $this->json(['message' => 'Invalid token'], Response::HTTP_UNAUTHORIZED);
      }

      // Find the user by email
      $user = $em->getRepository(User::class)->findOneBy(['email' => $payload['username']]);

      // Get the member associated with the user
      $member = $user->getMember();

      if ($member === null) {
         // Return an error response if the member is not found
         $errorMessage = [
            'message' => "Member not found",
         ];
         return $this->json($errorMessage, Response::HTTP_NOT_FOUND);
      }

      // Get the JSON data from the request
      $json = $request->getContent();

      // Deserialize the JSON and populate the member object
      $serializer->deserialize($json, Member::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $member]);

      // Validate the member object
      $errorList = $validator->validate($member);

      if (count($errorList) > 0) {
         // Return the validation errors if there are any
         return $this->json($errorList, Response::HTTP_UNPROCESSABLE_ENTITY);
      }

      // Decode the JSON data as an associative array
      $jsonData = json_decode($json, true);

      // Check if the password or confirmPassword fields are set
      if (isset($jsonData['password']) || isset($jsonData['confirmPassword'])) {
         // Check if the password field is set
         if (isset($jsonData['password'])) {
            // Check if the confirmPassword field is set
            if (isset($jsonData['confirmPassword'])) {
               // Check if the passwords match
               if ($jsonData['password'] !== $jsonData['confirmPassword']) {
                  // Return an error response if the passwords don't match
                  return $this->json(['message' => 'Les mots de passe ne correspondent pas'], Response::HTTP_UNPROCESSABLE_ENTITY);
               }
            } else {
               // Return an error response if the confirmPassword field is not set
               return $this->json(['message' => 'Veuillez confirmer le mot de passe'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Hash the password and set it on the member object
            $hashedPassword = password_hash($jsonData['password'], PASSWORD_BCRYPT);
            $member->setPassword($hashedPassword);
         } else if (isset($jsonData['confirmPassword'])) {
            // Return an error response if the password field is not set
            return $this->json(['message' => 'Veuillez renseigner le mot de passe'], Response::HTTP_UNPROCESSABLE_ENTITY);
         } else {
            // Return an error response if the confirmPassword field is not set
            return $this->json(['message' => 'Veuillez renseigner le mot de passe'], Response::HTTP_UNPROCESSABLE_ENTITY);
         }
      }
      // Update the member in the database
      $em->flush();

      // Return the updated member object
      return $this->json($member, Response::HTTP_OK, [], ["groups" => 'member']);
   }
}
