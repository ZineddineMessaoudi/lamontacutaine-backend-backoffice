<?php

namespace App\Controller\Back;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @Route("/evenements", name="back_event_")
 */
class EventController extends AbstractController
{
    /**
     * Displays a list of events.
     *
     * @Route("/liste", name="list")
     *
     * @param EventRepository $eventRepository The event repository to retrieve events from the database.
     * 
     * @return Response The response containing the home page template and data of events.
     */
    public function list(EventRepository $eventRepository): Response
    {
        // Retrieve events from the database using the event repository
        $events = $eventRepository->findEvents();

        // Render the list.html.twig template and pass the events data to it
        return $this->render('back/event/list.html.twig', [
            'events' => $events,
        ]);
    }

    /**
     * Displays a single event
     *
     * @Route("/{id<\d+>}", name="read") // Route annotation to define the URL pattern and route name
     * @param int $id The ID of the event to display
     * @param EventRepository $eventRepository The repository to fetch the event from
     * 
     * @return Response The response containing the event page template and data of the event
     */
    public function read(int $id, EventRepository $eventRepository): Response
    {
        // Fetch the event from the repository based on the provided ID
        $event = $eventRepository->findEvents($id);

        // Render the 'back/event/read.html.twig' template and pass the event data to it
        return $this->render('back/event/read.html.twig', [
            'event' => $event
        ]);
    }

    /**
     * Creates a new event
     *
     * @Route("/creer", name="create", methods={"GET","POST"})
     *
     * @param Request $request The request containing the event data.
     * @param EntityManagerInterface $entityManager The entity manager to persist the event in the database.
     * 
     * @return Response The response containing the event creation form.
     */
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Create a new instance of the Event entity
        $event = new Event();

        // Create a form using the EventType form type and bind it to the $event object
        $form = $this->createForm(EventType::class, $event);

        // Handle the form submission and validation
        $form->handleRequest($request);

        // If the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the $event object to the database
            $entityManager->persist($event);
            // Flush the changes to the database
            $entityManager->flush();

            // Add a success flash message
            $this->addFlash('success', 'Evènement créé');

            // Redirect to the event list page
            return $this->redirectToRoute('back_event_list');
        }

        // Render the form template with the form object
        return $this->renderForm('back/event/add_edit.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * Edit an event.
     *
     * @Route("/{id}/mise-a-jour", name="update", methods={"GET","POST"})
     * 
     * @param Request $request The request containing the event data.
     * @param EntityManagerInterface $em The entity manager to persist the event in the database.
     * @param Event $event The event to edit.
     * 
     * @return Response The response containing the event edit form.
     */
    public function update(Event $event, Request $request, EntityManagerInterface $em): Response
    {
        // Create form using the EventType form class and pass in the $event object
        $form = $this->createForm(EventType::class, $event);

        // Handle the form submission and validation
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the changes to the event object
            $em->persist($event);
            $em->flush();

            // Add a success flash message
            $this->addFlash('success', 'Evènement modifié');

            // Redirect to the event list page
            return $this->redirectToRoute('back_event_list');
        }

        // Render the edit form template with the form and event objects
        return $this->renderForm('back/event/add_edit.html.twig', [
            'form' => $form,
            'event' => $event,
        ]);
    }

    /**
     * Deletes an event.
     *
     * @Route("/{id}/supprimer", name="delete", methods={"POST"}, requirements={"id"="\d+"})
     * 
     * @param Request $request The request containing the CSRF token.
     * @param Event $event The event to delete.
     * @param EventRepository $eventRepository The repository to delete the event from.
     * 
     * @return Response The response containing the event list page.
     */
    public function delete(Request $request, Event $event, EventRepository $eventRepository): Response
    {
        // Check if the CSRF token is valid
        if ($this->isCsrfTokenValid('delete-event', $request->request->get('_token'))) {
            // Remove the event from the repository
            $eventRepository->remove($event, true);
            // Add a success flash message
            $this->addFlash('success', 'Evènement supprimé');
        }

        // Redirect to the event list page
        return $this->redirectToRoute('back_event_list', [], Response::HTTP_SEE_OTHER);
    }
}
