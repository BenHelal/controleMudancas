<?php

namespace App\Entity;

use App\Repository\ExportMudRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;

#[ORM\Entity(repositoryClass: ExportMudRepository::class)]
class ExportMud
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[JoinTable(name: 'ExpMud')]
    #[ORM\OneToOne()]
    private ?Mudancas $mudanca = null;

    #[JoinTable(name: 'ExpProc')]
    #[ORM\OneToOne()]
    private ?Process $process = null;

    #[JoinTable(name: 'ExpSP')]
    #[ORM\ManyToMany(targetEntity: SectorProcess::class, inversedBy: 'exportMuds')]
    private Collection $sectorProcess;

    public function __construct()
    {
        $this->sectorProcess = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMudanca(): ?Mudancas
    {
        return $this->mudanca;
    }

    public function setMudanca(?Mudancas $mudanca): static
    {
        $this->mudanca = $mudanca;

        return $this;
    }

    public function getProcess(): ?Process
    {
        return $this->process;
    }

    public function setProcess(?Process $process): static
    {
        $this->process = $process;

        return $this;
    }

    /**
     * @return Collection<int, SectorProcess>
     */
    public function getSectorProcess(): Collection
    {
        return $this->sectorProcess;
    }

    public function addSectorProcess(SectorProcess $sectorProcess): static
    {
        if (!$this->sectorProcess->contains($sectorProcess)) {
            $this->sectorProcess->add($sectorProcess);
        }

        return $this;
    }

    public function removeSectorProcess(SectorProcess $sectorProcess): static
    {
        $this->sectorProcess->removeElement($sectorProcess);

        return $this;
    }
}
