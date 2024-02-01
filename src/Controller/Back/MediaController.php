<?php

namespace App\Controller\Back;

use App\Entity\Media;
use App\Entity\Event;
use App\Form\MediaType;
use App\Repository\MediaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\FileUploader;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;



/**
 * @Route("/media", name="back_media_", methods={"GET"})
 */
class MediaController extends AbstractController
{
    /**
     * Displays a list of medias for a specific event.
     *
     * @Route("/evenement/{id}/liste", name="event_list", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param MediaRepository $mediaRepository
     * @param Event $event The event object
     * @return Response The response containing the media page template and data of the medias
     */
    public function mediaListByEvent(Event $event, MediaRepository $mediaRepository): Response
    {
        $medias = $mediaRepository->findBy(['event'=>$event],['type'=>'ASC','cover_media'=>'DESC','preview_order'=>'DESC']) ;
        
        return $this->render('back/media/list_by_event.html.twig', [
            'medias' => $medias,
            'event' => $event
        ]);
    }

    /**
     * Displays a list of medias with their preview for a specific event.
     *
     * @Route("/evenement/{id}", name="event", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param MediaRepository $mediaRepository
     * @param string $mediaType
     * @return Response The response containing the media page template and data of the medias
     */
    public function mediaPreviewByEvent(Event $event, MediaRepository $mediaRepository): Response
    {
        // Get the flyers for the specified event
        $flyers = $mediaRepository->getFlyersByEvent($event->getId());

        // Get the covers for the specified event
        $covers = $mediaRepository->getCoversByEvent($event->getId());

        // Get the previews for the specified event
        $previews = $mediaRepository->getPreviewsByEvent($event->getId());

        // Get the photos for the specified event
        $photos = $mediaRepository->getPhotosByEvent($event->getId());

        // Render the media list by event template and pass the data
        return $this->render('back/media/preview_by_event.html.twig', [
            'flyers' => $flyers,
            'covers' => $covers,
            'previews' => $previews,
            'photos' => $photos,
            'event' => $event
        ]);
    }

    /**
     * Add single media (flyer, cover or preview depending ) to an event
     *
     * @Route("/{type}/{id}/ajouter", name="create", methods={"GET","POST"}, requirements={"id"="\d+", "type"="flyerl|flyerp|pone|ptwo|pthree|cover"})
     *
     * @param Request $request The request containing the event data.
     * @param EntityManagerInterface $entityManager The entity manager to persist the event in the database.
     * @param FileUploader $fileUploader The file uploader service to handle file uploads.
     * 
     * @return Response The response containing the event creation form.
     */
    public function create(Event $event, $type, Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        // Create a new instance of the Media entity
        $media = new Media();

        // Create a form using the MediaType form type and the new Media entity
        $form = $this->createForm(MediaType::class, $media);

        // Handle the form submission and validation
        $form->handleRequest($request);
        
        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) 
        {

            $image = $form->get('imageFile')->getData();

            if ($image) 
                {
                    $media = new Media();

                    $media->setEvent($event);
                    $media->setGalerySlider(0);
                    $media->setTitle($image->getClientOriginalName());
                    if ($type == 'pone'| $type == 'ptwo'| $type == 'pthree'| $type == 'cover')
                    {
                        $media->setType('image');
                    }
                    else
                    {
                        $media->setType('flyer');
                    }
                    if ($type == 'flyerp')
                    {
                        $imageName = $fileUploader->upload($media->getType(), $image, true);
                    }
                    else
                    {
                        $imageName = $fileUploader->upload($media->getType(), $image);
                    }
                    $media->setUrl($imageName);
                    if ($type == 'cover')
                    {
                        $media->setCoverMedia(1);
                    }
                    else
                    {
                        $media->setCoverMedia(0);
                    }
                    if ($type == 'pone'| $type == 'ptwo'| $type == 'pthree')
                    {
                        switch ($type) 
                        {
                            case 'pone':
                                $media->setPreviewOrder(1);
                            break;
                            case 'ptwo':
                                $media->setPreviewOrder(2);
                            break;
                            case 'pthree':
                                $media->setPreviewOrder(3);
                            break;
                        }
                    }
                    else
                    {
                        $media->setPreviewOrder(null);
                    }

                    $entityManager->persist($media);
                    
                    $entityManager->flush();
                    $this->addFlash('success', $media->getType().' ajouté(e) à '.$media->getEvent()->getTitle());
                    return $this->redirectToRoute('back_media_event', ['id' => $event->getId()]);
                }
                $this->addFlash('error', 'Aucun média n\'a pas été ajouté');
                return $this->redirectToRoute('back_media_event', ['id' => $event->getId()]);
            }
        

        return $this->renderForm('back/media/add.html.twig', [
            'form' => $form,
            'type' => $type
        ]);
    }

    /**
     * add multiples medias with type: image, cover value: false, preview order: null to an event
     *
     * @Route("/ajouter", name="massupload", methods={"GET","POST"})
     *
     * @param Request $request The request containing the event data.
     * @param EntityManagerInterface $entityManager The entity manager to persist the event in the database.
     * @return Response The response containing the event creation form.
     */
    public function massUpload(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $media = new Media();

        $form = $this->createForm(MediaType::class, $media, [
            'multiple_mass' => true,
            'help_mass' => '(max. 25 fichiers à la fois)',
            'constraints_mass' => [
                new NotBlank([
                    'message' => "Merci d'ajouter un fichier",
                ]),  
                new All([
                    new Image([
                        'maxSize' => '20M'
                    ])
                ])
            ],
            'constraints_mass_event' => [
                new NotBlank([
                    'message' => "Veuillez sélectionner un évènement.",
                ]),
            ],
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $images = $form->get('imageFile')->getData();

            if ($images) {
                // Loop through each image
                foreach ($images as $image) {

                    // Create a new instance of the Media entity
                    $media = new Media();

                    // Set the event relation of the media to the selected event from the form
                    $media->setEvent($form->get('event')->getData());
                    // Set the type of the media to 'image'
                    $media->setType('image');
                    // Upload the image file using the file uploader service and get the image name
                    $imageName = $fileUploader->upload('image', $image);
                    // Set the URL of the media to the image name
                    $media->setUrl($imageName);
                    // Set the title of the media to the original name of the image file
                    $media->setTitle($image->getClientOriginalName());
                    // Set the cover media flag to false
                    $media->setCoverMedia(0);
                    // Set the gallery slider flag to false
                    $media->setGalerySlider(0);
                    // Set the preview order to null
                    $media->setPreviewOrder(null);

                    // Persist the media entity
                    $entityManager->persist($media);
                }

                // Flush the changes to the database
                $entityManager->flush();
                $this->addFlash('success', 'Médias ajoutés à '.$form->get('event')->getData()->getTitle());

                return $this->redirectToRoute('back_media_event', ['id' =>$form->get('event')->getData()->getId()]);
            }
            $this->addFlash('error', 'Aucun média n\'a pas été ajouté à '.$form->get('event')->getData()->getTitle());
            return $this->redirectToRoute('back_media_event', ['id' =>$form->get('event')->getData()->getId()]);
        }

        // Render the add.html.twig template with the form
        return $this->renderForm('back/media/add.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * Deletes a media.
     * 
     * @Route("/{id}/supprimer", name="delete", methods={"POST"}, requirements={"id"="\d+"})
     * 
     * @param Request $request The request containing the media ID.
     * @param Media $media The media to delete.
     * @param MediaRepository $mediaRepository The media repository to delete the media from the database.
     * 
     * @return Response The response containing the redirection to the media list.
     */
    public function delete(Request $request, Media $media, MediaRepository $mediaRepository): Response
    {
        if ($this->isCsrfTokenValid('delete-media-' . $media->getId(), $request->request->get('_token'))) {
            $mediaRepository->remove($media, true);
            $this->addFlash('success', $media->getType().' supprimé(e)');

        }
        $route = $request->headers->get('referer');

        return $this->redirect($route);
    }
}
