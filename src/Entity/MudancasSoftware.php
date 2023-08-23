<?php

namespace App\Entity;

use App\Repository\MudancasSoftwareRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MudancasSoftwareRepository::class)]
class MudancasSoftware
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    

    public function getId(): ?int
    {
        return $this->id;
    }

}
