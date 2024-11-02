<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class ExhibitorGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public readonly int $id;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom de groupe ne peut pas être vide.')]
    public string $groupName;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'La description ne peut pas être vide.')]
    public string $description;

    #[ORM\Column(type: 'text', length: 255, nullable: true)]
    public ?string $website = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\NotBlank(message: 'L\'email est obligatoire.')]
    #[Assert\Email(message: 'Veuillez fournir un email valide.')]
    private string $emailContact;

    #[ORM\Column(type: 'boolean')]
    public bool $archived = false;

    #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: 'exhibitorGroups')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Event $event = null;

    #[ORM\OneToMany(targetEntity: Exhibitor::class, mappedBy: 'exhibitorGroup')]
    public iterable $exhibitors;  // Les exposants appartenant à ce groupe

    #[ORM\OneToMany(targetEntity: Attachments::class, mappedBy: 'exhibitorGroup')]
    public iterable $attachments;  // Les utilisateurs (exposants) associés à ce groupe

    public function __construct()
    {
        $this->exhibitors = new ArrayCollection();
        $this->attachments = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
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

    public function getGroupName(): string
    {
        return $this->groupName;
    }

    public function setGroupName(string $groupName): ExhibitorGroup
    {
        $this->groupName = $groupName;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): ExhibitorGroup
    {
        $this->description = $description;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): ExhibitorGroup
    {
        $this->website = $website;

        return $this;
    }

    public function getEmailContact(): string
    {
        return $this->emailContact;
    }

    public function setEmailContact(string $emailContact): ExhibitorGroup
    {
        $this->emailContact = $emailContact;

        return $this;
    }

    public function isArchived(): bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): ExhibitorGroup
    {
        $this->archived = $archived;

        return $this;
    }

    public function getExhibitors(): iterable
    {
        return $this->exhibitors;
    }

    public function setExhibitors(iterable $exhibitors): ExhibitorGroup
    {
        $this->exhibitors = $exhibitors;

        return $this;
    }

    public function getAttachments(): Collection
    {
        return $this->attachments;
    }

    public function addAttachment(Attachments $attachment): void
    {
        if (!$this->attachments->contains($attachment)) {
            $this->attachments->add($attachment);
            $attachment->setExhibitorGroup($this);
        }
    }

    public function removeAttachment(Attachments $attachment): void
    {
        if ($this->attachments->contains($attachment)) {
            $this->attachments->removeElement($attachment);
            if ($attachment->getExhibitorGroup() === $this) {
                $attachment->setExhibitorGroup(null);
            }
        }
    }
}