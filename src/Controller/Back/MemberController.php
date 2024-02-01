<?php

namespace App\Controller\Back;

use App\Entity\User;
use App\Entity\Member;
use App\Form\MemberType;
use App\Repository\UserRepository;
use App\Repository\MemberRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 *
 * @Route("/membres", name="back_member_")
 */
class MemberController extends AbstractController
{
    /**
     * Displays a list of members
     * 
     * @Route("/liste", name="list")
     * 
     * @param MemberRepository $memberRepository The member repository to retrieve members from the database.
     * 
     * @return Response The response containing the member list page template and data of members
     */
    public function memberList(MemberRepository $memberRepository): Response
    {
        // Retrieve members from the database
        $members = $memberRepository->findMembers();

        // Render the member list page template and pass the members data
        return $this->render('back/member/member_list.html.twig', [
            'members' => $members,
        ]);
    }

    /**
     * Creates a new member
     * 
     * @Route("/creer", name="create", methods={"GET","POST"})
     * 
     * @param Request $request The request containing the member data.
     * @param EntityManagerInterface $entityManager The entity manager to persist the category in the database.
     * @param UserRepository $userRepository The repository to query for existing users.
     * 
     * @return Response The response containing the category creation form.
     */
    public function create(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        // Create a new Member instance
        $member = new Member();

        // Create a form using the MemberType class
        $form = $this->createForm(MemberType::class, $member, ['addNotBlankConstraint' => true]);

        // Handle the form submission
        $form->handleRequest($request);

        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Check if a user with the same email exists
            $isUser = $userRepository->findOneBy(['email' => $form->get('email')->getData()]);

            if ($isUser != null) {
                // If the user exists, update the existing user
                $member->setUser($isUser);
                $isUser->setFirstname($form->get('firstname')->getData());
                $isUser->setLastname($form->get('lastname')->getData());
                $isUser->setNewsletterSubscriber($form->get('newsletter_subscriber')->getData());
                $entityManager->persist($isUser);
            } else {
                // If the user does not exist, create a new user
                $user = new User();
                $user->setEmail($form->get('email')->getData());
                $user->setFirstname($form->get('firstname')->getData());
                $user->setLastname($form->get('lastname')->getData());
                $user->setNewsletterSubscriber($form->get('newsletter_subscriber')->getData());

                $entityManager->persist($user);
                $member->setUser($user);
            }

            // Handle the member form
            $this->handleMemberForm($form, $member);

            // Hash the password and set it on the member
            $hashedPassword = password_hash($form->get('password')->getData(), PASSWORD_BCRYPT);
            $member->setPassword($hashedPassword);

            // Persist the member to the database
            $entityManager->persist($member);
            $entityManager->flush();

            $this->addFlash('success', 'Membre créé');

            // Redirect to the member list page
            return $this->redirectToRoute('back_member_list');
        }

        // Render the form template
        return $this->renderForm('back/member/add_edit.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * Updates a new member
     * 
     * @Route("/{id<\d+>}/mise-a-jour", name="update", methods={"GET","POST"})
     * 
     * @param Request $request The request containing the member data.
     * @param EntityManagerInterface $entityManager The entity manager to persist the category in the database.
     * 
     * @return Response The response containing the member form.
     */
    public function update(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Find the member by id
        $member = $entityManager->getRepository(Member::class)->find($request->attributes->get('id'));
        // Get the user associated with the member
        $user = $member->getUser();

        // Check if the member or user is null
        if (null === $member || null === $user) {
            // Throw an exception if the member or user is null
            throw $this->createNotFoundException("Ce membre n'existe pas");
        }

        // Create the form with the MemberType and pre-fill it with user data
        $form = $this->createForm(MemberType::class, $member);

        $form->get('firstname')->setData($user->getFirstname());
        $form->get('lastname')->setData($user->getLastname());
        $form->get('email')->setData($user->getEmail());
        $form->get('newsletter_subscriber')->setData($user->isNewsletterSubscriber());

        // Store the current password
        $currentPassword = $member->getPassword();

        // Handle the form submission and validation
        $form->handleRequest($request);

        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {

            // Update the user data with the form data
            $user->setEmail($form->get('email')->getData());
            $user->setFirstname($form->get('firstname')->getData());
            $user->setLastname($form->get('lastname')->getData());
            $user->setNewsletterSubscriber($form->get('newsletter_subscriber')->getData());

            // Persist the updated user entity
            $entityManager->persist($user);
            // Associate the updated user with the member
            $member->setUser($user);

            // Handle the member form
            $this->handleMemberForm($form, $member);

            // Get the new password from the form
            $password = $form->get('password')->getData();

            // Check if a new password is provided
            if (!empty($password)) {
                // Hash the new password
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                // Set the hashed password for the member
                $member->setPassword($hashedPassword);
            } else if ($password == null) {
                // Set the current password for the member if no new password is provided
                $member->setPassword($currentPassword);
            }

            // Persist the updated member entity
            $entityManager->persist($member);
            // Save the changes to the database
            $entityManager->flush();

            $this->addFlash('success', 'Membre modifié');

            // Redirect to the member list page
            return $this->redirectToRoute('back_member_list');
        }

        // Render the form template with the form data
        return $this->renderForm('back/member/add_edit.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * Handles the member form by setting the member's roles and membership expiration date based on the form data.
     *
     * @param $form The form object.
     * @param $member The member object.
     * 
     * @return void
     */
    private function handleMemberForm($form, $member): void
    {
        // Create an empty array to store the roles
        $roles = [];

        // Check if the isAdmin field in the form is true
        if ($form->get('isAdmin')->getData() === 'Oui') {
            // If true, add 'ROLE_ADMIN' to the roles array
            $roles[] = 'ROLE_ADMIN';
            // Set the member's roles to the updated roles array
            $member->setRoles($roles);
        } else {
            // If false, add 'ROLE_USER' to the roles array
            $roles[] = 'ROLE_USER';
            // Set the member's roles to the updated roles array
            $member->setRoles($roles);
        }

        // Check if the membership_statut field in the form is true
        if ($form->get('membership_statut')->getData()) {
            // If true, add 'ROLE_SUBSCRIBER' to the roles array
            $roles[] = 'ROLE_SUBSCRIBER';
            // Set the member's roles to the updated roles array
            $member->setRoles($roles);

            // Get the current date and time
            $today = new DateTime();

            // Create a clone of the current date and time
            $expirationDate = clone $today;

            // Set the expiration date to September 30th of the current year
            $expirationDate->setDate($today->format('Y'), 9, 30);

            // Get the current month and day as integers
            $month = (int)$today->format('n');
            $day = (int)$today->format('j');

            // Check if the current month is September and the current day is less than or equal to 31
            if ($month >= 9 && ($month <= 12 && $day <= 31)) {
                // Add 1 year to the expiration date
                $expirationDate->modify('+1 year');
            }

            // Set the member's membership expiration date to the updated expiration date
            $member->setMembershipExpiration($expirationDate);
        }
    }
}
