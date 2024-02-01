<?php

namespace App\Entity;

use App\Entity\Event;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\MediaRepository;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=MediaRepository::class)
 * @UniqueEntity("title", message="Ce nom est déjà utilisé")
 * @ORM\HasLifecycleCallbacks()
 */
class Media
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"media", "events"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"media", "events"})
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=20)
     * @Groups({"media", "events"})
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"media", "events"})
     */
    private $url;

    /**
     * @ORM\Column(type="boolean", options={"default": "0"})
     */
    private $galery_slider;

    /**
     * @ORM\Column(type="boolean", options={"default": "0"})
     * @Groups({"media", "events"})
     */
    private $cover_media;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @Groups({"media", "events"})
     */
    private $preview_order;

    /**
     * @ORM\ManyToOne(targetEntity=Event::class, inversedBy="medias")
     * @ORM\JoinColumn(nullable=false)
     */
    private $event;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        if($this->type == 'image') {
            $this->url = "medias\\images\\" . $url;
        } else {
            $this->url = "medias\\flyers\\" . $url;
        }

        return $this;
    }   

    public function isGalerySlider(): ?bool
    {
        return $this->galery_slider;
    }

    public function setGalerySlider(bool $galery_slider): self
    {
        $this->galery_slider = $galery_slider;

        return $this;
    }

    public function isCoverMedia(): ?bool
    {
        return $this->cover_media;
    }

    public function setCoverMedia(bool $cover_media): self
    {
        $this->cover_media = $cover_media;

        return $this;
    }

    public function getPreviewOrder(): ?int
    {
        return $this->preview_order;
    }

    public function setPreviewOrder(?int $preview_order): self
    {
        $this->preview_order = $preview_order;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @ORM\PreRemove
     */
    public function preRemove()
    {
        $filePath = $this->getUrl();

        $newFilePath = str_replace('\\','/',$filePath);

        if (file_exists($newFilePath)) {

            unlink($newFilePath);
        }
    }
}
