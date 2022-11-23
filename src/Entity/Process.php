<?php

namespace App\Entity;

use App\Repository\ProcessRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Nullable;

#[ORM\Entity(repositoryClass: ProcessRepository::class)]
class Process
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Mudancas::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Mudancas $mudancas = null;

    #[ORM\ManyToMany(targetEntity: Departemant::class)]
    /**
      * @ManyToMany(targetEntity="Departemant")
      * @JoinTable(name="departemant_process")
      **/
    private Collection $departemant;

    #[ORM\Column(type: 'string', length: 255, nullable:true)]
    private $status;

    public function __construct()
    {
        $this->departemant = new ArrayCollection();
    }

    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getMudancas(): ?Mudancas
    {
        return $this->mudancas;
    }

    public function setMudancas(Mudancas $mudancas): self
    {
        $this->mudancas = $mudancas;

        return $this;
    }

    /**
     * @return Collection<int, Departemant>
     */
    public function getDepartemant(): Collection
    {
        return $this->departemant;
    }

    public function addDepartemant(Departemant $departemant): self
    {
        if (!$this->departemant->contains($departemant)) {
            $this->departemant->add($departemant);
        }

        return $this;
    }

    public function removeDepartemant(Departemant $departemant): self
    {
        $this->departemant->removeElement($departemant);

        return $this;
    }
}
