<?php

namespace App\Controller\Back;

use App\Repository\CommentRepository;
use App\Repository\EventRepository;
use App\Repository\MemberRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * Displays the home page with events, members, and comments.
     * 
     * @Route("/", name="back_home")
     * 
     * @param EventRepository $eventRepository The event repository to retrieve events from the database.
     * @param MemberRepository $memberRepository The member repository to retrieve members from the database.
     * @param CommentRepository $commentRepository The comment repository to retrieve comments from the database.
     * 
     * @return Response The response containing the home page template and data of events, members, and comments.
     */
    public function home(EventRepository $eventRepository, MemberRepository $memberRepository, CommentRepository $commentRepository): Response
    {
        // Retrieve the latest 16 events from the database
        $events = $eventRepository->findEvents(null, 16);

        // Retrieve members for the home page
        $members = $memberRepository->findMembersHome();

        // Retrieve the latest 10 comments based on the createdAt field
        $comments = $commentRepository->findComments("createdAt", null, 10);

        // Render the home page template with the events, members, and comments data
        return $this->render('back/home/home.html.twig', [
            'events' => $events,
            'members' => $members,
            'comments' => $comments
        ]);
    }
}
