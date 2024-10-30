<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Attachments
{
    const IMAGES = 'images';
    const VIDEOS = 'videos';


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public readonly int $id;

    #[ORM\Column(type: 'text',)]
    public string $url;

    #[ORM\Column(length: 10)]
    public string $type;

    #[ORM\ManyToOne(targetEntity: ExhibitorGroup::class, inversedBy: 'attachments')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?ExhibitorGroup $exhibitorGroup = null;

    public function __construct(string $url, string $type)
    {
        $this->url = $url;
        $this->type = $type;
    }

    public function getExhibitorGroup(): ?ExhibitorGroup
    {
        return $this->exhibitorGroup;
    }

    public function setExhibitorGroup(?ExhibitorGroup $exhibitorGroup): void
    {
        $this->exhibitorGroup = $exhibitorGroup;
    }

}