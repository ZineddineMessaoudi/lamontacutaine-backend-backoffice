<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Service\SendMail;
use App\Service\RegenPassword;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RegenPasswordController extends AbstractController
{
   private $sendMail;
   private $regenPassword;

   public function __construct(SendMail $sendMail, RegenPassword $regenPassword)
   {
      $this->sendMail = $sendMail;
      $this->regenPassword = $regenPassword;
   }
   
   /**
    * Regenerates the password for a member via the API.
    *
    * @Route("/api/v1/member/regen-password", name="api_v1_member_regen_password", methods="POST")
    *
    * @param EntityManagerInterface $em - The entity manager used to interact with the database.
    * @param Request $request - The HTTP request object.
    *
    * @return JsonResponse - The JSON response containing the updated member object.
    */
   public function regenPassword(EntityManagerInterface $em, Request $request): JsonResponse
   {
      // Get the JSON data from the request
      $jsonData = json_decode($request->getContent(), true);

      // Find the user by email
      $user = $em->getRepository(User::class)->findOneBy(['email' => $jsonData['email']]);

      // Check if the user exists
      if ($user === null) {
         // Return an error response if there is no member associated with the user
         return $this->json(['message' => 'Member not found'], Response::HTTP_NOT_FOUND);
      }

      // Regenerate the password
      $newPassword = $this->regenPassword->createPassword();

      // Hash the new password and set it on the member
      $member = $user->getMember();
      $member->setPassword(password_hash($newPassword, PASSWORD_BCRYPT));

      $em->persist($member);
      $em->flush();

      // Send the new password to the user
      $body = 'Le nouveau mot de passe de votre compte Lamontacutaine est : ' . $newPassword;
      $this->sendMail->sendCustomEmail($user->getEmail(), 'Nouveau mot de passe', $body);

      // Return a success response
      return $this->json(['message' => 'Password regenerated']);
   }
}