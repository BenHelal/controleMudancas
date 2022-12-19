<?php

namespace App\Entity;

use App\Repository\MudancasRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
#[ORM\Entity(repositoryClass: MudancasRepository::class)]
class Mudancas
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nomeMudanca = null;
    
    #[ORM\Column(length: 255)]
    private ?string $descMudanca = null;

    #[ORM\Column(length: 255)]
    private ?string $descImpacto = null;

    #[ORM\Column(length: 255)]
    private ?string $descImpactoArea = null;

    #[ORM\Column(length: 255)]
    private ?string $justif = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $approved = null;
    
    #[ORM\Column(length: 255, nullable:true)]
    private ?string $done = null;

    #[ORM\Column(length: 255, nullable:true)]
    private ?string $nansenName = null;
    
    #[ORM\Column(length: 255, nullable:true)]
    private ?string $nansenNumber = null;
    

    #[ORM\ManyToOne]
    private ?Person $addBy = null;

    //Gestor da mudança
    #[ORM\ManyToOne]
    private ?Person $mangerMudancas = null;

    //Data estimada de Início
    #[ORM\Column(type:'date', nullable: true)]
    private  $startMudancas = null;

    //Data estimada de Termino
    #[ORM\Column(type:'date', nullable: true)]
    private $endMudancas = null;

    //Data Efetiva de Início
    #[ORM\Column(type:'date', nullable: true)]
    private $effictiveStartDate = null;

    //Custo
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cost = null;

    //Mudança Implementada
    #[ORM\Column(nullable: true)]
    private ?bool $implemented = null;
    
    //Evidencia de Implementação
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $impDesc = null;

    //Data de conclusão da mudança
    #[ORM\Column(type:'date',nullable: true)]
    private $dateOfImp = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comMan = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comGest = null;

    #[ORM\Column(nullable: true)]
    private ?int $appMan = null;

    #[ORM\Column(nullable: true)]
    private ?int $appGest = null;

    #[ORM\Column(type:'datetime')]
    private $dataCreation;

    #[ORM\ManyToMany(targetEntity: Sector::class)]
    private Collection $areaImpact;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Sector $areaResp = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Person $managerUserAdd = null;

    #[ORM\Column(length: 1024, nullable: true)]
    private ?string $managerUserComment = null;

    #[ORM\Column(nullable: true)]
    private ?int $managerUserApp = null;
    
    public function __construct()
    {
        $this->AreaResp = new ArrayCollection();
        $this->areaImpact = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getApproved(): ?string
    {
        return $this->approved;
    }

    public function setApproved(string $approved): self
    {
        $this->approved = $approved;

        return $this;
    }    
    
    public function getNansenName(): ?string
    {
        return $this->nansenName;
    }

    public function setNansenName(string $nansenName): self
    {
        $this->nansenName = $nansenName;

        return $this;
    }    
    
    public function getNansenNumber(): ?string
    {
        return $this->nansenNumber;
    }

    public function setNansenNumber(string $nansenNumber): self
    {
        $this->nansenNumber = $nansenNumber;

        return $this;
    }
    public function getDone(): ?string
    {
        return $this->done;
    }

    public function setDone(string $done): self
    {
        $this->done = $done;

        return $this;
    }
    public function getNomeMudanca(): ?string
    {
        return $this->nomeMudanca;
    }

    public function setNomeMudanca(string $nomeMudanca): self
    {
        $this->nomeMudanca = $nomeMudanca;

        return $this;
    }

    
    public function getDescMudanca(): ?string
    {
        return $this->descMudanca;
    }

    public function setDescMudanca(string $descMudanca): self
    {
        $this->descMudanca = $descMudanca;

        return $this;
    }


    public function getDescImpacto(): ?string
    {
        return $this->descImpacto;
    }

    public function setDescImpacto(string $descImpacto): self
    {
        $this->descImpacto = $descImpacto;

        return $this;
    }

    public function getDescImpactoArea(): ?string
    {
        return $this->descImpactoArea;
    }

    public function setDescImpactoArea(string $descImpactoArea): self
    {
        $this->descImpactoArea = $descImpactoArea;

        return $this;
    }

    public function getJustif(): ?string
    {
        return $this->justif;
    }

    public function setJustif(string $justif): self
    {
        $this->justif = $justif;

        return $this;
    }

    public function getAddBy(): ?Person
    {
        return $this->addBy;
    }

    public function setAddBy(?Person $addBy): self
    {
        $this->addBy = $addBy;

        return $this;
    }

    public function getMangerMudancas(): ?Person
    {
        return $this->mangerMudancas;
    }

    public function setMangerMudancas(?Person $mangerMudancas): self
    {
        $this->mangerMudancas = $mangerMudancas;

        return $this;
    }

    public function getStartMudancas()
    {
        if($this->startMudancas != null){
            return $this->startMudancas->format('Y-m-d');
        }else{
            return $this->startMudancas;
        }
    }

    public function setStartMudancas(\DateTimeInterface $startMudancas): self
    {
        $this->startMudancas = $startMudancas;

        return $this;
    }

    public function getEndMudancas()
    {
        if($this->endMudancas != null){
            return $this->endMudancas->format('Y-m-d');
        }else{
            return $this->endMudancas;
        }
    }

    public function setEndMudancas(\DateTimeInterface $endMudancas): self
    {
        $this->endMudancas = $endMudancas;

        return $this;
    }

    public function getEffictiveStartDate()
    {
        if($this->effictiveStartDate != null){
            return $this->effictiveStartDate->format('Y-m-d');
        }else{
            return $this->effictiveStartDate;
        }
    }

    public function setEffictiveStartDate(\DateTimeInterface $effictiveStartDate): self
    {
        $this->effictiveStartDate = $effictiveStartDate;

        return $this;
    }

    public function getCost(): ?string
    {
        return $this->cost;
    }

    public function setCost(?string $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function isImplemented(): ?bool
    {
        return $this->implemented;
    }

    public function setImplemented(?bool $implemented): self
    {
        $this->implemented = $implemented;

        return $this;
    }

    public function getImpDesc(): ?string
    {
        return $this->impDesc;
    }

    public function setImpDesc(?string $impDesc): self
    {
        $this->impDesc = $impDesc;

        return $this;
    }

    public function getDateOfImp()
    {
        if($this->dateOfImp != null){
            return $this->dateOfImp->format('Y-m-d');
        }else{
            return $this->dateOfImp;
        }
    }

    public function setDateOfImp(\DateTimeInterface $dateOfImp): self
    {
        $this->dateOfImp = $dateOfImp;
        return $this;
    }

    public function getComMan(): ?string
    {
        return $this->comMan;
    }

    public function setComMan(?string $comMan): self
    {
        $this->comMan = $comMan;

        return $this;
    }

    public function getComGest(): ?string
    {
        return $this->comGest;
    }

    public function setComGest(?string $comGest): self
    {
        $this->comGest = $comGest;

        return $this;
    }

    public function getAppMan()
    {
        return $this->appMan;
    }

    public function setAppMan(?bool $appMan): self
    {
        $this->appMan = $appMan;

        return $this;
    }

    public function getAppGest()
    {
        return $this->appGest;
    }

    public function setAppGest(?bool $appGest): self
    {
        $this->appGest = $appGest;

        return $this;
    }

    public function getDataCreation()
    {
        if($this->dataCreation != null){
            return $this->dataCreation->format('Y-m-d H:i:s');
        }else{
            return $this->dataCreation;
        }
    }

    public function setDataCreation(\DateTimeInterface $dataCreation): self
    {
        $this->dataCreation = $dataCreation;

        return $this;
    }

    /**
     * @return Collection<int, Sector>
     */
    public function getAreaImpact(): Collection
    {
        return $this->areaImpact;
    }

    public function setAreaImpact(Collection $areaImpact): self
    {
        $this->areaImpact = $areaImpact;

        return $this;
    }


    public function addAreaImpact(Sector $areaImpact): self
    {
        if (!$this->areaImpact->contains($areaImpact)) {
            $this->areaImpact->add($areaImpact);
        }

        return $this;
    }

    public function removeAreaImpact(Sector $areaImpact): self
    {
        $this->areaImpact->removeElement($areaImpact);

        return $this;
    }

    public function getAreaResp(): ?Sector
    {
        return $this->areaResp;
    }

    public function setAreaResp(?Sector $areaResp): self
    {
        $this->areaResp = $areaResp;

        return $this;
    }

    public function getManagerUserAdd(): ?Person
    {
        return $this->managerUserAdd;
    }

    public function setManagerUserAdd(?Person $managerUserAdd): self
    {
        $this->managerUserAdd = $managerUserAdd;

        return $this;
    }

    public function getManagerUserComment(): ?string
    {
        return $this->managerUserComment;
    }

    public function setManagerUserComment(?string $managerUserComment): self
    {
        $this->managerUserComment = $managerUserComment;

        return $this;
    }

    public function getManagerUserApp(): ?int
    {
        return $this->managerUserApp;
    }

    public function setManagerUserApp(?int $managerUserApp): self
    {
        $this->managerUserApp = $managerUserApp;

        return $this;
    }
}
