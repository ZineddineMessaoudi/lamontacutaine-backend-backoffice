<?php

namespace App\Entity;

use App\Repository\DocumentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=DocumentRepository::class)
 * @UniqueEntity("title", message="Un document avec ce titre existe déjà.")
 * @ORM\HasLifecycleCallbacks()
 */
class Document
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"documents"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ce champs ne peut pas être vide")
     * @Groups({"documents"})
     */
    private $title;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank(message="Ce champs ne peut pas être vide")
     * @Groups({"documents"})
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups({"documents"})
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"documents"})
     */
    private $url;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"documents"})
     */
    private $visibility;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $updated_at;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

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
        if($this->type == 'document_adherent') {
            $this->url = "..\\private\\documents\\" . $url;
        } else {
            $this->url = "documents\\" . $url;
        }

        return $this;
    }

    public function isVisibility(): ?bool
    {
        return $this->visibility;
    }

    public function setVisibility($docType): self
    {
        if($docType == 'document_adherent') {
            $this->visibility = 0;
        } else {
            $this->visibility = 1;
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

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
