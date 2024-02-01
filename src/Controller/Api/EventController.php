<?php

namespace App\Controller\Api;

use App\Repository\EventRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EventController extends AbstractController
{
    /**
     * Retrieves the events for the agenda.
     *
     * This function is responsible for handling the GET request to the "/api/v1/events" route.
     * It retrieves all events or events from a specific year (if year paramater is provided) from the EventRepository and returns a JSON response containing
     * the events array or an error message.
     *
     * @Route("/api/v1/events/{year<\d+>?}", name="api_v1_events", methods={"GET"})
     *  
     * @param EventRepository $eventRepository The event repository to fetch events from.
     * @return JsonResponse The JSON response containing the events array or an error message.
     */
    public function list(EventRepository $eventRepository, Request $request): JsonResponse
    {
        if (!$request->get('year')) {
        // Retrieve all events from the EventRepository, ordered by date
        $events = $eventRepository->findAllOrderByDate();

            // If no events are found, return a JSON response with an error message and HTTP status code 204 (No Content)
            if (empty($events)) {
                return $this->json(['error' => 'Events not found'], Response::HTTP_NO_CONTENT);
            }
        }elseif ($request->get('year')) {
        $events = $eventRepository->findByYear($request->get('year'));
        }

        // Return a JSON response with the events array, HTTP status code 200 (OK), and the 'events' serialization group
        return $this->json(['events' => $events], Response::HTTP_OK, [], ['groups' => 'events']);
    }

    /**
     * Retrieves a single event.
     *
     * This route is responsible for retrieving a single event with the given ID.
     * 
     * @Route("/api/v1/event/{id}", name="api_v1_event_read", methods={"GET"})
     * @param int $id The ID of the event to retrieve.
     * @param EventRepository $eventRepository The repository for events.
     * @return JsonResponse The JSON response containing the event or an error message.
     */
    public function read(int $id, EventRepository $eventRepository): JsonResponse
    {
        // Retrieve the event with the given ID from the event repository
        $event = $eventRepository->find($id);

        // If the event does not exist, return a JSON response with an error message
        if (empty($event)) {
            return $this->json(['error' => 'Event not found'], Response::HTTP_NOT_FOUND);
        }

        // Return a JSON response containing the event
        // Include the 'events' serialization group to control the fields included in the response
        return $this->json(['event' => $event], Response::HTTP_OK, [], ['groups' => 'events']);
    }
}
