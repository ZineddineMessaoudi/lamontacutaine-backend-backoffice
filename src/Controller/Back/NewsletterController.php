<?php

namespace App\Controller\Back;

use App\Model\Newsletter;
use App\Form\NewsletterType;
use App\Repository\UserRepository;
use App\Service\SendMail;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @Route("/newsletter", name="back_newsletter_")
 */
class NewsletterController extends AbstractController
{
    private $sendMail;

    public function __construct(SendMail $sendMail)
    {
        $this->sendMail = $sendMail;
    }

    /**
     * Creates a new newsletter.
     *
     * @Route("/creer", name="create", methods={"GET", "POST"})
     * @param Request $request
     * @param UserRepository $userRepository
     * 
     * @return Response
     */
    public function create(Request $request, UserRepository $userRepository): Response
    {
        // Create a new instance of the Newsletter class
        $newsletter = new Newsletter();

        // Create a form using the NewsletterType class
        $form = $this->createForm(NewsletterType::class);

        // Handle the form submission
        $form->handleRequest($request);

        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Set the subject, title, and body of the newsletter
            $newsletter->setSubject($form->get('subject')->getData());
            $newsletter->setTitle($form->get('title')->getData());
            $newsletter->setBody($form->get('body')->getData());

            // Get the email subscribers from the UserRepository
            $users = $userRepository->findEmailSubscriber();

            // Send the newsletter to the subscribers
            $this->sendMail->sendNewsletter($newsletter, $users);

            // Redirect to the create page after successful submission
            return $this->redirectToRoute('back_newsletter_create');
        }

        // Render the form template with the form variable
        return $this->renderForm('back/newsletter/add.html.twig', [
            'form' => $form
        ]);
    }
}
