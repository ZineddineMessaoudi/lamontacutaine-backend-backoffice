<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\Event;
use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentController extends AbstractController
{
   /**
    * @Route("/api/v1/comment", name="api_v1_comment_create", methods="POST")
    *
    * @param EntityManagerInterface $em
    * @param Request $request
    * @param SerializerInterface $serializer
    * @param ValidatorInterface $validator
    * @return JsonResponse
    */
   public function create(Request $request, EntityManagerInterface $em, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
   {
      try {
         // Get the JSON data from the request
         $json = $request->getContent();

         // Decode the JSON data into an associative array
         $jsonData = json_decode($json, true);

         // Deserialize the JSON data into a Comment object
         $comment = $serializer->deserialize($json, Comment::class, 'json', [
            AbstractNormalizer::GROUPS => 'comment'
         ]);

         // Validate the Comment object
         $errorComment = $validator->validate($comment);

         // If there are validation errors, return the errors as a JSON response
         if ($errorComment->count() > 0) {
            return $this->json($errorComment, Response::HTTP_UNPROCESSABLE_ENTITY);
         }

         // Check if the required fields (email, firstname, lastname) are present in the JSON data
         if (
            !isset($jsonData['email']) ||
            !isset($jsonData['firstname']) ||
            !isset($jsonData['lastname'])
         ) {
            return $this->json(
               ['error' => 'Il manque des champs de l\'utilisateur'],
               Response::HTTP_UNPROCESSABLE_ENTITY
            );
         } elseif (
            empty($jsonData['email']) ||
            empty($jsonData['firstname']) ||
            empty($jsonData['lastname'])
         ) {
            return $this->json(
               ['error' => 'Certains champs de l\'utilisateur sont vides'],
               Response::HTTP_UNPROCESSABLE_ENTITY
            );
         }

         // Find the user based on the email in the JSON data
         $user = $em->getRepository(User::class)->findOneBy(['email' => $jsonData['email']]);

         // If the user does not exist, create a new user
         if ($user === null) {
            $newUser = new User();
            $newUser->setEmail($jsonData['email']);
            $newUser->setFirstname($jsonData['firstname']);
            $newUser->setLastname($jsonData['lastname']);
            $newUser->setNewsletterSubscriber(false);

            $em->persist($newUser); // Persist the new user to the database
            $comment->setUser($newUser); // Set the comment's user to the new user
         } else {
            $comment->setUser($user); // Set the comment's user to the existing user
         }

         // If the eventId is present in the JSON data, set the comment's event
         if (isset($jsonData['eventId'])) {
            $comment->setEvent($em->getRepository(Event::class)->find($jsonData['eventId']));
         }

         $comment->setCreatedAt(new \DateTime()); // Set the comment's creation date
         $em->persist($comment); // Persist the comment to the database
         $em->flush(); // Save changes to the database

         // Return a success JSON response
         return $this->json(
            'Merci pour votre commentaire',
            Response::HTTP_CREATED,
            [],
            ['groups' => 'comment']
         );
      } catch (\Exception $e) {
         // If an exception occurs, return an error JSON response
         return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
      }
   }
}
