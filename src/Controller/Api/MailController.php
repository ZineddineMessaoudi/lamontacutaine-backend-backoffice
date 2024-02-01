<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Service\SendMail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MailController extends AbstractController
{
   private $sendMail;

   public function __construct(SendMail $sendMail)
   {
      $this->sendMail = $sendMail;
   }

   /**
    * @Route("/api/v1/emails", name="api_v1_emails", methods="POST")
    *
    * @param EntityManagerInterface $em
    * @param Request $request
    * @param SerializerInterface $serializer
    * @param ValidatorInterface $validator
    */
   public function create(EntityManagerInterface $em, Request $request, ValidatorInterface $validator): JsonResponse
   {
      // Decode the JSON content from the request
      $jsonData = json_decode($request->getContent(), true);

      // Retrieve the user with the given email from the database
      $userRepository = $em->getRepository(User::class);
      $user = $userRepository->findOneBy(['email' => $jsonData['email']]);

      // If the user doesn't exist, create a new user
      if ($user === null) {
         $user = new User();
         $user->setEmail($jsonData['email'])
            ->setFirstname($jsonData['firstname'])
            ->setLastname($jsonData['lastname'])
            ->setNewsletterSubscriber(false);

         // Validate the user entity
         $errors = $validator->validate($user);
         if (count($errors) > 0) {
            // Return the validation errors if there are any
            return $this->json($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
         }

         // Persist the new user to the database
         $em->persist($user);
         $em->flush();
      }

      // Check if the subject and body fields are empty
      if (empty($jsonData['subject']) || empty($jsonData['body'])) {
         // Return an error message if any of the fields are empty
         return $this->json([
            'message' => 'Veuillez renseigner tous les champs'
         ], Response::HTTP_UNPROCESSABLE_ENTITY);
      }

      // Send an email from the user
      $this->sendMail->sendEmailFromUser($user, $jsonData['subject'], $jsonData['body']);

      // Return a success message
      return $this->json([
         'message' => 'L\'e-mail a bien été envoyé'
      ]);
   }
}
