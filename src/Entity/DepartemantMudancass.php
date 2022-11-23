<?php

namespace App\Entity;

use App\Repository\DepartemantMudancasRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DepartemantMudancasRepository::class)]
class DepartemantMudancass
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
    private ?Departemant $Departemant = null;

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

    public function getDepartemant(): ?Departemant
    {
        return $this->Departemant;
    }

    public function setDepartemant(?Departemant $Departemant): self
    {
        $this->Departemant = $Departemant;

        return $this;
    }
}
