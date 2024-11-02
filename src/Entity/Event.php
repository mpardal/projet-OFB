<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public readonly int $id;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le titre ne peut pas être vide.')]
    public string $title;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'La description ne peut pas être vide.')]
    public string $description;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'L\'adresse ne peut pas être vide.')]
    #[Assert\Length(max: 255)]
    public string $address;

    #[ORM\Column(type: 'string', length: 5)]
    #[Assert\NotBlank(message: 'Le code postal ne peut pas être vide.')]
    #[Assert\Regex(pattern: '/^\d{5}$/', message: 'Le code postal doit être composé de 5 chiffres.')]
    public string $zipCode;

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\NotBlank(message: 'La ville ne peut pas être vide.')]
    #[Assert\Length(max: 100)]
    public string $city;

    #[ORM\Column(type: 'datetime')]
    public \DateTimeInterface $startDate;

    #[ORM\Column(type: 'datetime')]
    public \DateTimeInterface $endDate;

    #[ORM\OneToMany(targetEntity: ExhibitorGroup::class, mappedBy: 'event')]
    private Collection $exhibitorGroups;

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $banner;

    #[ORM\Column(type: 'integer', nullable: true)]
    public ?int $weezEventId;

    #[ORM\Column(type: 'boolean')]
    private bool $archived = false;

    public function __construct()
    {
        $this->exhibitorGroups = new ArrayCollection();
    }

    public function getExhibitorGroups(): Collection
    {
        return $this->exhibitorGroups;
    }

    public function addExhibitorGroup(ExhibitorGroup $exhibitorGroup): self
    {
        if (!$this->exhibitorGroups->contains($exhibitorGroup)) {
            $this->exhibitorGroups->add($exhibitorGroup);
            $exhibitorGroup->setEvent($this);
        }

        return $this;
    }

    public function removeExhibitorGroup(ExhibitorGroup $exhibitorGroup): self
    {
        if ($this->exhibitorGroups->contains($exhibitorGroup)) {
            $this->exhibitorGroups->removeElement($exhibitorGroup);
            $exhibitorGroup->setEvent(null);
        }

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): Event
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Event
    {
        $this->description = $description;

        return $this;
    }

    public function getStartDate(): \DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): Event
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): \DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): Event
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): Event
    {
        $this->address = $address;

        return $this;
    }

    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): Event
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): Event
    {
        $this->city = $city;

        return $this;
    }

    public function getBanner(): ?string
    {
        return $this->banner;
    }

    public function setBanner(?string $banner): Event
    {
        $this->banner = $banner;

        return $this;
    }

    public function getWeezeventId(): int
    {
        return $this->weezEventId;
    }

    public function setWeezeventId(int $weezEventId): Event
    {
        $this->weezEventId = $weezEventId;

        return $this;
    }

    public function isArchived(): bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): self
    {
        $this->archived = $archived;

        return $this;
    }
}