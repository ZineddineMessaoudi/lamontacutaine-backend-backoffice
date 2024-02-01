<?php

namespace App\Controller\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class NewsletterController extends AbstractController
{
    /**
     * Subscribe to the newsletter.
     *
     * @Route("/api/v1/subnewsletter", name="api_v1_subnewsletter", methods={"POST"})
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     * @param SerializerInterface    $serializer
     * @param ValidatorInterface     $validator
     *
     * @return JsonResponse
     */
    public function subscribeToNewsletter(
        Request $request,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $errorData = $this->checkData($data);

        if (count($errorData) > 0) {
            return $this->json($errorData, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Check if user exists in the database
        $user = $em->getRepository(User::class)->findOneBy(['email' => $data['email']]);

        // If user exists, update newsletter subscription
        if ($user !== null) {
            $user->setNewsletterSubscriber($data['cgu']);
        } else {
            // If user doesn't exist, create a new user
            $user = $serializer->deserialize($request->getContent(), User::class, 'json', [
                AbstractNormalizer::OBJECT_TO_POPULATE => new User(),
            ]);

            // Validate user data
            $errors = $validator->validate($user);

            if (count($errors) > 0) {
                return $this->json($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $user->setNewsletterSubscriber($data['cgu']);
            $em->persist($user);
        }

        $em->flush();

        return $this->json(['message' => 'Inscription réussie.'], Response::HTTP_OK);
    }

    /**
     * Checks the validity of the given data array.
     *
     * @param array $data The data array to be checked
     * @return array The error array containing error messages, if any
     */
    private function checkData(array $data): array
    {
        $error = [];

        // Check if $data['email'] is a valid email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return $error = ['message' => 'L\'email est invalide.'];
        }

        // Check if 'cgu' key is present in $data array
        if (!array_key_exists('cgu', $data)) {
            return $error = ['message' => 'La clef cgu est manquante.'];
        }

        // Check if 'cgu' is accepted
        if (!filter_var($data['cgu'], FILTER_VALIDATE_BOOLEAN) || $data['cgu'] === false) {
            return $error = ['message' => 'Vous devez accepter les CGU.'];
        }

        return $error;
    }
}
