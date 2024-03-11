<?php

namespace App\Entity;

use App\Repository\IARepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;

#[ORM\Entity(repositoryClass: IARepository::class)]
class IA
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: Person::class)]
    private Collection $persons;

    #[ORM\Column(length: 255)]
    private ?string $Api_token = null;


    public function __construct()
    {
        $this->persons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

        

    /**
     * @return Collection<int, Sector>
     */
    public function getPersons(): Collection
    {
        return $this->persons;
    }

    public function setPersons(Collection $persons): self
    {
        $this->persons = $persons;

        return $this;
    }


    public function addPersons(Person $persons): self
    {
        if (!$this->persons->contains($persons)) {
            $this->persons->add($persons);
        }

        return $this;
    }

    public function HeIsPerm(Person $person)
    {
        if (!$this->persons->contains($person)) {
           return false;
        }else{
            return true;
        }
    }

    public function getApiToken(): ?string
    {
        return $this->Api_token;
    }

    public function setApiToken(string $Api_token): static
    {
        $this->Api_token = $Api_token;

        return $this;
    }



}
