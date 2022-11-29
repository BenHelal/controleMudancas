<?php

namespace App\Entity;

use App\Repository\SectorRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SectorRepository::class)]
class Sector
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(cascade: [])]
    private ?Departemant $Departemant = null;

    #[ORM\ManyToOne]
    private ?Person $coordinator = null;

    #[ORM\ManyToOne]
    private ?Person $manager = null;

    public function getId(): ?int
    {
        return $this->id;
    }    
    

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDepartemant(): ?Departemant
    {
        return $this->Departemant;
    }

    public function setDepartemant(?Departemant $Departemant): self
    {
        $this->Departemant = $Departemant;

        return $this;
    }

    public function getCoordinator(): ?Person
    {
        return $this->coordinator;
    }

    public function setCoordinator(?Person $coordinator): self
    {
        $this->coordinator = $coordinator;

        return $this;
    }

    public function getManager(): ?Person
    {
        return $this->manager;
    }

    public function setManager(?Person $manager): self
    {
        $this->manager = $manager;

        return $this;
    }
}
