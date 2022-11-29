<?php

namespace App\Entity;

use App\Repository\SectorMudancasRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SectortMudancasRepository::class)]
class SectorMudancass
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade:['persist'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Mudancas $mudancas = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Sector $Sector = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMudancas(): ?Mudancas
    {
        return $this->mudancas;
    }

    public function setMudancas(?Mudancas $mudancas): self
    {
        $this->mudancas = $mudancas;

        return $this;
    }

    public function getSector(): ?Sector
    {
        return $this->Sector;
    }

    public function setDepartemant(?Sector $Sector): self
    {
        $this->Sector = $Sector;

        return $this;
    }
}
