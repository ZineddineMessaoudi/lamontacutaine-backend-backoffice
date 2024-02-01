<?php

namespace App\Controller\Back;

use App\Entity\Document;
use App\Form\DocumentType;
use App\Service\FileUploader;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/document", name="back_document_")
 */
class DocumentController extends AbstractController
{
    /**
     * Retrieves all documents
     * 
     * @Route("/liste", name="list")
     * 
     * @param DocumentRepository $documentRepository The repository for documents.
     * 
     * @return Response The response object.
     */
    public function list(DocumentRepository $documentRepository): Response
    {
        $documents = $documentRepository->findBy([], ['date' => 'DESC']);

        // Render the 'list.html.twig' template and pass the 'documents' variable to it
        return $this->render('back/document/list.html.twig', [
            'documents' => $documents,
        ]);
    }

    /**
     * Creates a new document.
     * 
     * @Route("/ajouter", name="create", methods={"GET","POST"})
     * 
     * @param Request $request The request containing the event data.
     * @param EntityManagerInterface $entityManager The entity manager to persist the event in the database.
     * @param FileUploader $fileUploader The file uploader to handle file uploads.
     * 
     * @return Response The response containing the event creation form.
     */
    public function create(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        // Create a new instance of the Document class
        $document = new Document();

        // Create a form using the DocumentType form type and pass the document instance
        $form = $this->createForm(DocumentType::class, $document);

        // Handle the form submission
        $form->handleRequest($request);

        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {

            // Get the uploaded document file
            $documentFile = $form->get('documentFile')->getData();

            // Check if a document file is uploaded
            if ($documentFile) {

                // Set the document type based on the form data
                $document->setType($form->get('type')->getData());

                // Upload the document file and get the uploaded file name
                $documentName = $fileUploader->upload($document->getType(), $documentFile);

                $document->setUrl($documentName);

                $document->setVisibility($document->getType());

                $document->setCreatedAt(new \DatetimeImmutable());
                $document->setUpdatedAt(new \DatetimeImmutable());

                // Persist the document entity
                $entityManager->persist($document);

                // Flush the changes to the database
                $entityManager->flush();

                $this->addFlash('success', 'Document ajouté');

                // Redirect to the document list page
                return $this->redirectToRoute('back_document_list');
            }
        }

        // Render the form template with the form instance
        return $this->renderForm('back/document/add_edit.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * Deletes a document.
     *
     * @Route("/{id}/supprimer", name="delete", methods={"POST"}, requirements={"id"="\d+"})
     * 
     * @param Request $request The request object.
     * @param Document $document The document to delete.
     * @param DocumentRepository $documentRepository The repository for documents.
     * @param FileUploader $fileUploader The file uploader to handle file uploads.
     * 
     * @return Response The response object.
     */
    public function delete(Request $request, Document $document, DocumentRepository $documentRepository): Response
    {
        // Check if the CSRF token is valid
        $csrfTokenValid = $this->isCsrfTokenValid('delete-document-' . $document->getId(), $request->request->get('_token'));

        if ($csrfTokenValid) {
            // Remove the document using the document repository's `remove` method
            $documentRepository->remove($document, true);

            // Set a flash message with the success message "Document supprimé"
            $this->addFlash('success', 'Document supprimé');
        }

        // Redirect the user to the 'back_document_list' route
        return $this->redirectToRoute('back_document_list', [], Response::HTTP_SEE_OTHER);
    }
}
