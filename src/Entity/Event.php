<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=EventRepository::class)
 * @UniqueEntity("title", message="Un évènement avec ce titre existe déjà.")
 */
class Event
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"media", "events"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     * @Assert\NotBlank(message="Ce champs ne peut pas être vide")
     * @Groups({"media", "events"})
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Ce champs ne peut pas être vide")
     * @Groups({"media", "events"})
     */
    private $description;

    // TODO : Add assert for date (start_date < end_date)
    /**
     * @ORM\Column(type="datetime")
     * @Assert\LessThanOrEqual(propertyPath="end_date", message="La date de fin ne peut pas être antérieure à la date de début de l'évènement")
     * @Assert\NotBlank(message="Ce champs ne peut pas être vide")
     * @Groups({"media", "events"})
     */
    private $start_date;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank(message="Ce champs ne peut pas être vide")
     * @Groups({"media", "events"})
     */
    private $end_date;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\LessThanOrEqual(propertyPath="end_date", message="La date de fin d'inscirption ne peut pas être après la fin de l'évènement")
     * @Groups({"media", "events"})
     */
    private $inscription_end_date;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @Assert\PositiveOrZero(message="Ce champs doit être positif ou nul")
     * @Groups({"media", "events"})
     */
    private $maximum_capacity;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ce champs ne peut pas être vide")
     * @Groups({"media", "events"})
     */
    private $event_location;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url(message="Ce lien n'est pas valide")
     * @Groups({"media", "events"})
     */
    private $hello_asso_url;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"media", "events"})
     */
    private $info_highlight;
    
    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     * @Assert\PositiveOrZero(message="Ce champs doit être positif ou nul")
     * @Assert\NotEqualTo(value = 0)
     * @Groups({"media", "events"})
     */
    private $price;

    /**
     * @ORM\Column(type="boolean", options={"default": "false"})
     * @Groups({"media", "events"})
     */
    private $open_to_trader;

    /**
     * @ORM\ManyToMany(targetEntity=Category::class, inversedBy="events")
     * @Groups({"media", "events"})
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=Media::class, mappedBy="event", orphanRemoval=true)
     * @Groups({"media", "events"})
     */
    private $medias;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="event", orphanRemoval=true)
     */
    private $comments;

    public function __construct()
    {
        $this->category = new ArrayCollection();
        $this->medias = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->start_date;
    }

    public function setStartDate(?\DateTimeInterface $start_date): self
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->end_date;
    }

    public function setEndDate(?\DateTimeInterface $end_date): self
    {
        $this->end_date = $end_date;

        return $this;
    }

    public function getInscriptionEndDate(): ?\DateTimeInterface
    {
        return $this->inscription_end_date;
    }

    public function setInscriptionEndDate(?\DateTimeInterface $inscription_end_date): self
    {
        $this->inscription_end_date = $inscription_end_date;

        return $this;
    }

    public function getMaximumCapacity(): ?int
    {
        return $this->maximum_capacity;
    }

    public function setMaximumCapacity(?int $maximum_capacity): self
    {
        $this->maximum_capacity = $maximum_capacity;

        return $this;
    }

    public function getEventLocation(): ?string
    {
        return $this->event_location;
    }

    public function setEventLocation(string $event_location): self
    {
        $this->event_location = $event_location;

        return $this;
    }

    public function isOpenToTrader(): ?bool
    {
        return $this->open_to_trader;
    }

    public function setOpenToTrader(bool $open_to_trader): self
    {
        $this->open_to_trader = $open_to_trader;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->category->contains($category)) {
            $this->category[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->category->removeElement($category);

        return $this;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getMedias(): Collection
    {
        return $this->medias;
    }

    public function addMedia(Media $media): self
    {
        if (!$this->medias->contains($media)) {
            $this->medias[] = $media;
            $media->setEvent($this);
        }

        return $this;
    }

    public function removeMedia(Media $media): self
    {
        if ($this->medias->removeElement($media)) {
            // set the owning side to null (unless already changed)
            if ($media->getEvent() === $this) {
                $media->setEvent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setEvent($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getEvent() === $this) {
                $comment->setEvent(null);
            }
        }

        return $this;
    }

    public function getHelloAssoUrl(): ?string
    {
        return $this->hello_asso_url;
    }

    public function setHelloAssoUrl(?string $hello_asso_url): self
    {
        $this->hello_asso_url = $hello_asso_url;

        return $this;
    }

    public function getInfoHighlight(): ?string
    {
        return $this->info_highlight;
    }

    public function setInfoHighlight(?string $info_highlight): self
    {
        $this->info_highlight = $info_highlight;

        return $this;
    }
}
