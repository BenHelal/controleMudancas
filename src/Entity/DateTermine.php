<?php

namespace App\Entity;

use App\Repository\DateTermineRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DateTermineRepository::class)]
class DateTermine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $oldDateTime = null;

    #[ORM\Column(length: 255)]
    private ?string $newDateTime = null;

    #[ORM\Column(length: 255)]
    private ?string $justification = null;

    #[ORM\ManyToOne(inversedBy: 'DatesTermine')]
    private ?Mudancas $mudancas = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOldDateTime(): ?string
    {
        return $this->oldDateTime;
    }

    public function setOldDateTime(string $oldDateTime): static
    {
        $this->oldDateTime = $oldDateTime;

        return $this;
    }

    public function getNewDateTime(): ?string
    {
        return $this->newDateTime;
    }

    public function setNewDateTime(string $newDateTime): static
    {
        $this->newDateTime = $newDateTime;

        return $this;
    }

    public function getJustification(): ?string
    {
        return $this->justification;
    }

    public function setJustification(string $justification): static
    {
        $this->justification = $justification;

        return $this;
    }

    public function getMudancas(): ?Mudancas
    {
        return $this->mudancas;
    }

    public function setMudancas(?Mudancas $mudancas): static
    {
        $this->mudancas = $mudancas;

        return $this;
    }
}
