<?php

namespace App\Entity;

use App\Repository\StepsTestRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StepsTestRepository::class)]
class StepsTest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'stepsTest')]
    private ?MudancasSoftware $mudancasSoftware = null;


    #[ORM\Column(length: 255)]
    private ?string $step = null;

    #[ORM\Column(length: 500000000, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $date = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $doc = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $aprove = null;

    
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $affect = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAffect(): ?string
    {
        return $this->affect;
    }

    public function setAffect(string $affect): self
    {
        $this->affect = $affect;

        return $this;
    }

    public function getMudancasSoftware(): ?MudancasSoftware
    {
        return $this->mudancasSoftware;
    }

    public function setMudancasSoftware(?MudancasSoftware $mudancasSoftware): self
    {
        $this->mudancasSoftware = $mudancasSoftware;

        return $this;
    }

    public function getStep(): ?string
    {
        return $this->step;
    }

    public function setStep(string $step): self
    {
        $this->step = $step;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(?string $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDoc(): ?string
    {
        return $this->doc;
    }

    public function setDoc(string $doc): self
    {
        $this->doc = $doc;

        return $this;
    }

    public function getAprove(): ?string
    {
        return $this->aprove;
    }

    public function setAprove(?string $aprove): self
    {
        $this->aprove = $aprove;

        return $this;
    }

}