<?php

namespace App\Controller\Back;

use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 *
 * @Route("/commentaires", name="back_comment_")
 */
class CommentController extends AbstractController
{
    /**
     * Display a list of comments.
     * 
     * @Route("/liste", name="list")
     * 
     * @param CommentRepository $commentRepository The repository to retrieve comments from the database.
     * 
     * @return Response The response containing the template and data of comments.
     */
    public function list(CommentRepository $commentRepository): Response
    {
        // Retrieve the comments from the database using the comment repository.
        $comments = $commentRepository->findComments();

        // Render the template 'back/comment/list.html.twig' with the comments data.
        return $this->render('back/comment/list.html.twig', [
            'comments' => $comments
        ]);
    }

    /**
     * Displays a single comment
     * 
     * @Route("/{id<\d+>}", name="read")
     * 
     * @param CommentRepository $commentRepository The comment repository to retrieve comments from the database.
     * @param int $id The id of the comment.
     * 
     * @return Response The response containing the comment page template and data of the comment
     */
    public function read($id, CommentRepository $commentRepository): Response
    {
        // Retrieve the comment with the given id from the comment repository
        $comment = $commentRepository->findComments(null, $id);

        // Render the comment page template and pass the comment data to it
        return $this->render('back/comment/index.html.twig', [
            'comment' => $comment
        ]);
    }

    /**
     * Deletes a comment.
     * 
     * This function is responsible for deleting a comment based on the provided comment id.
     *
     * @Route("/supprimer/{id<\d+>}", name="delete")
     * @param Request $request The request containing the comment id.
     * @param CommentRepository $commentRepository The comment repository to retrieve comments from the database.
     * 
     * @return Response The response redirecting to the comment list.
     */
    public function delete(Request $request, CommentRepository $commentRepository): Response
    {
        // Retrieve the comment object based on the provided comment id
        $comment = $commentRepository->find($request->attributes->get('id'));

        // If the comment does not exist, throw an exception
        if (null === $comment) {
            throw $this->createNotFoundException("Le commentaire n'existe pas");
        }
        // If the CSRF token is valid, delete the comment and display a success message
        else if ($this->isCsrfTokenValid('delete-comment-' . $comment->getId(), $request->request->get('_token'))) {
            $commentRepository->remove($comment, true);
            $this->addFlash('success', 'Le commentaire a bien été supprimé');
        }

        // Redirect to the comment list page
        return $this->redirectToRoute('back_comment_list', [], Response::HTTP_SEE_OTHER);
    }
}
