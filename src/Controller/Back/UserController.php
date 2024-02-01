<?php

namespace App\Controller\Back;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 *
 * @Route("/utilisateurs", name="back_user_")
 */
class UserController extends AbstractController
{
    /**
     * Displays a list of users.
     * 
     * This function is the controller action for the "/liste" route, which displays a list of users.
     * 
     * @Route("/liste", name="list")
     * 
     * @param UserRepository $userRepository The repository to retrieve user data from the database.
     * 
     * @return Response The response containing the rendered user list template.
     */
    public function userList(UserRepository $userRepository): Response
    {
        // Retrieve all users from the database.
        $users = $userRepository->findAll();

        // Render the user list template and pass the user data to it.
        return $this->render('back/user/user_list.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * Updates an user
     * 
     * @Route("/{id<\d+>}/mise-a-jour", name="update", methods={"GET","POST"})
     * 
     * @param Request $request The request containing the user data.
     * @param EntityManagerInterface $entityManager The entity manager to persist the user in the database.
     * 
     * @return Response The response containing the user form.
     */
    public function update(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($request->attributes->get('id'));

        if (null === $user) {
            throw $this->createNotFoundException("Cet utilisateur n'existe pas");
        }

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur "' . $user->getFirstname() . ' ' . $user->getLastname() . '" a bien été mis à jour');

            return $this->redirectToRoute('back_user_list');
        } else if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour de l\'utilisateur');
        }

        return $this->renderForm('back/user/user_edit.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * Deletes a user.
     * 
     * @Route("/{id<\d+>}/supprimer", name="delete", methods={"POST"})
     * 
     * @param Request $request The request containing the user id.
     * @param UserRepository $userRepository The user repository to retrieve user from the database.
     * 
     * @return Response The response redirecting to the user list.
     */
    public function delete(Request $request, UserRepository $userRepository): Response
    {
        // Get the user based on the id from the request attributes
        $user = $userRepository->find($request->attributes->get('id'));

        // Check if the user exists
        if (null === $user) {
            // Throw an exception if the user does not exist
            throw $this->createNotFoundException("L'utilisateur n'existe pas");
        }
        // Check if the CSRF token is valid for deleting the user
        else if ($this->isCsrfTokenValid('delete-user-' . $user->getId(), $request->request->get('_token'))) {
            // Remove the user from the database
            $userRepository->remove($user, true);
            // Add a success flash message indicating that the user has been deleted
            $this->addFlash('success', 'L\'utilisateur "' . $user->getFirstname() . ' ' . $user->getLastname() . '" a bien été supprimée');
        }
        // Check if the CSRF token is invalid for deleting the user
        else if (!$this->isCsrfTokenValid('delete-user-' . $user->getId(), $request->request->get('_token'))) {
            // Add an error flash message indicating that an error occurred
            $this->addFlash('error', 'Une erreur est survenue');
        }

        // Redirect to the user list page
        return $this->redirectToRoute('back_user_list', [], Response::HTTP_SEE_OTHER);
    }
}
