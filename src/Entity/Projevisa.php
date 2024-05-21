<?php

namespace App\Entity;

use App\Repository\ProjevisaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjevisaRepository::class)]
class Projevisa
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Person $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?Person
    {
        return $this->user;
    }

    public function setUser(Person $user): static
    {
        $this->user = $user;

        return $this;
    }
}
