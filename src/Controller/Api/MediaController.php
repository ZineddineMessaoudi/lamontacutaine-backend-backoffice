<?php

namespace App\Controller\Api;

use App\Repository\MediaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MediaController extends AbstractController
{
    /**
     * Retrieves the flyers for the home page slider.
     *
     * @Route("/api/v1/flyerslider", name="api_v1_flyerslider", methods={"GET"})
     *  
     * @param MediaRepository $mediaRepository The media repository.
     * @return JsonResponse The JSON response containing the flyers array or an error message.
     */
    public function getHomePageFlyers(MediaRepository $mediaRepository): JsonResponse
    {
        $flyers = $mediaRepository->getFlyers();

        if (empty($flyers)) {
            return $this->json(['error' => 'Flyers not found'], Response::HTTP_NOT_FOUND);
        }
        
        return $this->json(['flyers' => $flyers], Response::HTTP_OK, [], ['groups' => 'media']);
    }

    /**
     * Retrieves the photos for the galery slider.
     *
     * @Route("/api/v1/galeryslider", name="api_v1_galeryslider", methods={"GET"})
     *  
     * @param MediaRepository $mediaRepository The media repository.
     * @return JsonResponse The JSON response containing the photo array or an error message.
     */
    public function getGalerySlider(MediaRepository $mediaRepository): JsonResponse
    {
        $galerySliderPhotos = $mediaRepository->getGalerySliderPhotos();

        if (empty($galerySliderPhotos)) {
            return $this->json(['error' => 'Photos not found'], Response::HTTP_NOT_FOUND);
        }
        
        return $this->json(['galerySliderPhotos' => $galerySliderPhotos], Response::HTTP_OK, [], ['groups' => 'media']);
    }

    /**
     * Retrieves the photos of an event.
     *
     * @Route("/api/v1/event/{id}/photos", name="api_v1_eventphotos", methods={"GET"})
     *  
     * @param MediaRepository $mediaRepository The media repository.
     * @return JsonResponse The JSON response containing the photo array or an error message.
     */
    public function getEventPhoto($id, MediaRepository $mediaRepository): JsonResponse
    {
        $eventPhotos = $mediaRepository->getPhotosByEvent($id);

        if (empty($eventPhotos)) {
            return $this->json(['error' => 'Photos not found'], Response::HTTP_NOT_FOUND);
        }
        
        return $this->json(['eventPhotos' => $eventPhotos], Response::HTTP_OK, [], ['groups' => 'media']);
    }
}

