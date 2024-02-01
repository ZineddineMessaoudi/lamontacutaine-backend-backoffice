<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

class DocumentController extends AbstractController
{
    private $jwtEncoder;

    public function __construct(JWTEncoderInterface $jwtManager)
    {
        $this->jwtEncoder = $jwtManager;
    }

/**
 * Retrieves documents.
 * 
 * @Route("/api/v1/documents/{name}/{id<\d+>?}", name="api_v1_documents", methods={"GET"}, requirements={"name"="private|public"})
 * 
 * @param EntityManagerInterface $em The entity manager instance.
 * @param DocumentRepository $documentRepository The document repository instance.
 * @param string $name The name parameter.
 * @param Request $request The request object.
 * @return JsonResponse The JSON response containing the documents array or an error message.
 */
public function list(EntityManagerInterface $em, DocumentRepository $documentRepository, string $name, Request $request)
{
    // Check if the name parameter is 'public'
    if ($name === 'public') {
        $documents = $documentRepository->findBy(['visibility' => true]);

        if (empty($documents)) {
            return $this->json(['error' => 'Documents not found'], Response::HTTP_NO_CONTENT);
        }
    // Check if the name parameter is 'private'
    } else if ($name === 'private') {
        $token = $request->headers->get('Authorization');
        $token = str_replace('Bearer ', '', $token);

        // Check if the token is valid
        try {
            $payload = $this->jwtEncoder->decode($token);
        } catch (\Exception $e) {
            return $this->json(['message' => 'Invalid token'], Response::HTTP_UNAUTHORIZED);
        }

        // Check if the user exists
        $user = $em->getRepository(User::class)->findOneBy(['email' => $payload['username']]);

        $member = $user->getMember();
        if ($member === null) {
            return $this->json(['error' => 'Member not found'], Response::HTTP_NOT_FOUND);
        }
        if ($member->isMembershipStatut() === false) {
            return $this->json(['error' => 'Le membre n\' pas payé sa cotisation.'], Response::HTTP_UNAVAILABLE_FOR_LEGAL_REASONS);
        }

        // Check if the 'id' parameter is not provided
        if (!$request->get('id')) {

            $documents = $documentRepository->findBy(['visibility' => false]);

            if (empty($documents)) {
                return $this->json(['error' => 'Documents not found'], Response::HTTP_NO_CONTENT);
            }
        } else if ($request->get('id')) {
            // If the 'id' parameter is provided, retrieve the document with the specified ID
            $id = $request->get('id');
            $documents = $documentRepository->findBy(['id' => $id, 'visibility' => false]);

            if (empty($documents)) {
                return $this->json(['error' => 'Document not found'], Response::HTTP_NOT_FOUND);
            }

            // Get the file path from the document URL and replace backslashes with forward slashes
            $filePath = str_replace('\\', '/', $documents[0]->getUrl());
            
            if (!file_exists($filePath)) {
                return $this->json(['error' => 'File not found'], Response::HTTP_NOT_FOUND);
            }

            // Set the content disposition and return the file
            return $this->file($filePath)
                ->setContentDisposition(
                    ResponseHeaderBag::DISPOSITION_INLINE,
                    $documents[0]->getTitle()
                );
        }
    }

    // Return the documents informations
    return $this->json(['documents' => $documents], Response::HTTP_OK, [], ['groups' => 'documents']);
}
}
