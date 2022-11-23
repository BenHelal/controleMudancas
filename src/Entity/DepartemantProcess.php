<?php

namespace App\Entity;

use App\Repository\DepartemantProcessRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DepartemantProcessRepository::class)]
class DepartemantProcess
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
    private ?Departemant $Departemant = null;

    
    #[ORM\Column(length: 255 , nullable: true)]
    private ?string $comment = null;

    #[ORM\ManyToOne]
    private ?Person $person = null;

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
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

    public function getDepartemant(): ?Departemant
    {
        return $this->Departemant;
    }

    public function setDepartemant(?Departemant $Departemant): self
    {
        $this->Departemant = $Departemant;

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
