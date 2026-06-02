<?php

namespace App\Entity;

use App\Repository\SeanceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SeanceRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Seance
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['seance:read', 'seance:list'])]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire.')]
    #[Assert\Length(max: 255)]
    #[Groups(['seance:read', 'seance:list', 'seance:write'])]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['seance:read', 'seance:write'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: 'La date et l\'heure sont obligatoires.')]
    #[Assert\GreaterThan('now', message: 'La séance doit être dans le futur.')]
    #[Groups(['seance:read', 'seance:list', 'seance:write'])]
    private ?\DateTimeInterface $dateHeure = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\Positive(message: 'La durée doit être positive.')]
    #[Groups(['seance:read', 'seance:list', 'seance:write'])]
    private ?int $duree = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\Positive(message: 'Le nombre de places doit être positif.')]
    #[Groups(['seance:read', 'seance:list', 'seance:write'])]
    private ?int $placesMax = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['seance:read', 'seance:list', 'seance:write'])]
    private ?string $lieu = null;

    #[ORM\Column(options: ['default' => true])]
    #[Groups(['seance:read', 'seance:list'])]
    private bool $disponible = true;

    #[ORM\ManyToOne(inversedBy: 'seances')]
    #[Groups(['seance:read'])]
    private ?User $createdBy = null;

    #[ORM\OneToMany(mappedBy: 'seance', targetEntity: Reservation::class, orphanRemoval: true)]
    private Collection $reservations;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['seance:read'])]
    private ?\DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getDateHeure(): ?\DateTimeInterface
    {
        return $this->dateHeure;
    }

    public function setDateHeure(\DateTimeInterface $dateHeure): static
    {
        $this->dateHeure = $dateHeure;
        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): static
    {
        $this->duree = $duree;
        return $this;
    }

    public function getPlacesMax(): ?int
    {
        return $this->placesMax;
    }

    public function setPlacesMax(int $placesMax): static
    {
        $this->placesMax = $placesMax;
        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(?string $lieu): static
    {
        $this->lieu = $lieu;
        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setSeance($this);
        }
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function isDisponible(): bool
    {
        return $this->disponible;
    }

    public function setDisponible(bool $disponible): static
    {
        $this->disponible = $disponible;
        return $this;
    }

    public function getPlacesRestantes(): int
    {
        $confirmed = $this->reservations->filter(
            fn(Reservation $r) => $r->getStatut() !== 'annulee'
        )->count();
        return max(0, $this->placesMax - $confirmed);
    }
}
