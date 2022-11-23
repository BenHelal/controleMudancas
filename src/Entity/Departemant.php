<?php

namespace App\Entity;

use App\Repository\DepartemantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DepartemantRepository::class)]
class Departemant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    
    #[ORM\OneToMany(targetEntity: Mudancas::class, mappedBy: 'areaResp')]
    private ?Collection $mudanca = null;
    
    #[ORM\ManyToMany(targetEntity: Mudancas::class)]
    private Collection $mudancas;


    public function __construct()
    {
    }

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


    public function getMudanca()
    {
        return $this->mudanca;
    }

    public function setMudanca(?Mudancas $mudanca): self
    {
        $this->mudanca = $mudanca;
        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?: '';
    }

    public function addMudanca(Mudancas $mudanca): self
    {
        if (!$this->mudancas->contains($mudanca)) {
            $this->mudancas->add($mudanca);
            $mudanca->setAreaResp($this);
        }

        return $this;
    }

    public function removeMudanca(Mudancas $mudanca): self
    {
        if ($this->mudancas->removeElement($mudanca)) {
            // set the owning side to null (unless already changed)
            if ($mudanca->getAreaResp() === $this) {
                $mudanca->setAreaResp(null);
            }
        }
        return $this;
    }
}
