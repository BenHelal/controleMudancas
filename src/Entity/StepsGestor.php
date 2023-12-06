<?php

namespace App\Entity;

use App\Repository\StepsGestorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StepsGestorRepository::class)]
class StepsGestor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'stepsGestor')]
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
    private ?string $docTest = null;


    /** Client approvement  */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $dateSol = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $approveSol = null;

    #[ORM\OneToMany(mappedBy: 'ariquivo', targetEntity: Steps::class)]
    private Collection $steps;

    public function __construct()
    {
        $this->steps = new ArrayCollection();
    }
    /********************************/

    public function getDateSol(): ?string
    {
        return $this->dateSol;
    }

    public function setDateSol(string $dateSol): self
    {
        $this->dateSol = $dateSol;

        return $this;
    }

    public function getApproveSol(): ?string
    {
        return $this->approveSol;
    }

    public function setApproveSol(string $approveSol): self
    {
        $this->approveSol = $approveSol;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDocTest(): ?string
    {
        return $this->docTest;
    }

    public function setDocTest(string $docTest): self
    {
        $this->docTest = $docTest;

        return $this;
    }

    /**
     * @return Collection<int, Steps>
     */
    public function getSteps(): Collection
    {
        return $this->steps;
    }

    public function addStep(Steps $step): static
    {
        if (!$this->steps->contains($step)) {
            $this->steps->add($step);
            $step->setAriquivo($this);
        }

        return $this;
    }

    public function removeStep(Steps $step): static
    {
        if ($this->steps->removeElement($step)) {
            // set the owning side to null (unless already changed)
            if ($step->getAriquivo() === $this) {
                $step->setAriquivo(null);
            }
        }

        return $this;
    }
}
