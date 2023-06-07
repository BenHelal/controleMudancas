<?php

namespace App\Entity;

use App\Repository\SectorProcessRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SectorProcessRepository::class)]
class SectorProcess
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Process $process = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Sector $Sector = null;

    #[ORM\Column(length: 255 , nullable: true)]
    private ?string $comment = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Person $person = null;

    
    #[ORM\Column(nullable: true)]
    private ?int $appSectorMan = null;

    public function getAppSectorMan(): ?int
    {
        return $this->appSectorMan;
    }

    public function setAppSectorMan(?int $appSectorMan): self
    {
        $this->appSectorMan = $appSectorMan;

        return $this;
    }
    public function getComment()
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProcess(): ?Process
    {
        return $this->process;
    }

    public function setProcess(?Process $process): self
    {
        $this->process = $process;

        return $this;
    }

    public function getSector(): ?Sector
    {
        return $this->Sector;
    }

    public function setSector(?Sector $Sector): self
    {
        $this->Sector = $Sector;

        return $this;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(?Person $person): self
    {
        $this->person = $person;

        return $this;
    }
}
