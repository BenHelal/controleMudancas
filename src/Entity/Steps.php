<?php

namespace App\Entity;

use App\Repository\StepsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StepsRepository::class)]
class Steps
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(length: 255)]
    private ?string $dateCreation = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $comments = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $approve = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $dateApp = null;

    

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $dateStatue = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $doc = null;

    
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $docTest = null;

    #[ORM\ManyToOne(inversedBy: 'steps')]
    private ?StepsGestor $ariquivo = null;

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDateStatue(): ?string
    {
        return $this->dateStatue;
    }

    public function setDateStatue(string $dateStatue): self
    {
        $this->dateStatue = $dateStatue;

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

    public function getDocTest(): ?string
    {
        return $this->docTest;
    }

    public function setDocTest(string $docTest): self
    {
        $this->docTest = $docTest;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getDateCreation(): ?string
    {
        return $this->dateCreation;
    }

    public function setDateCreation(string $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(string $comments): static
    {
        $this->comments = $comments;

        return $this;
    }

    public function getApprove(): ?string
    {
        return $this->approve;
    }

    public function setApprove(string $approve): static
    {
        $this->approve = $approve;

        return $this;
    }

    public function getDateApp(): ?string
    {
        return $this->dateApp;
    }

    public function setDateApp(?string $dateApp): static
    {
        $this->dateApp = $dateApp;

        return $this;
    }

    public function getAriquivo(): ?StepsGestor
    {
        return $this->ariquivo;
    }

    public function setAriquivo(?StepsGestor $ariquivo): static
    {
        $this->ariquivo = $ariquivo;

        return $this;
    }
}
