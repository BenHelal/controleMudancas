<?php

namespace App\Entity;

use App\Repository\TokenDataRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TokenDataRepository::class)]
class TokenData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mudancas = null;

    #[ORM\Column(length: 4294967295, nullable: true)]
    private ?string $nomeMudanca = null;
    
    #[ORM\Column(length: 4294967295, nullable: true)]
    private ?string $descMudanca = null;

    #[ORM\Column(length: 4294967295, nullable: true)]
    private ?string $descImpacto = null;

    #[ORM\Column(length: 4294967295, nullable: true)]
    private ?string $descImpactoArea = null;

    #[ORM\Column(length: 4294967295, nullable: true)]
    private ?string $justif = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $approved = null;
    
    #[ORM\Column(length: 255, nullable:true)]
    private ?string $done = null;

    #[ORM\Column(length: 255, nullable:true)]
    private ?string $nansenName = null;
    
    #[ORM\Column(length: 255, nullable:true)]
    private ?string $nansenNumber = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private  $startMudancas = null;

    //Data estimada de Termino
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private $endMudancas = null;

    //Data Efetiva de Início
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private $effictiveStartDate = null;

    //Custo
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cost = null;

    //Mudança Implementada
    #[ORM\Column(length: 255, nullable:true)]
    private ?string $implemented = null;
    
    //Evidencia de Implementação
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $impDesc = null;

    //Data de conclusão da mudança
    #[ORM\Column(type: Types::DATETIME_MUTABLE,nullable: true)]
    private $dateOfImp = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comMan = null;

    
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comClt = null;


    #[ORM\Column(nullable: true)]
    private ?int $appClt = null;

    public function getAppClt(): ?int
    {
        return $this->appClt;
    }

    public function setAppClt(?int $appClt): self
    {
        $this->appClt = $appClt;

        return $this;
    }

    public function getComClt(): ?string
    {
        return $this->comClt;
    }

    public function setComClt(?string $comClt): self
    {
        $this->comClt = $comClt;

        return $this;
    }



    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comGest = null;

    #[ORM\Column(nullable: true)]
    private ?int $appMan = null;

    #[ORM\Column(nullable: true)]
    private ?int $appGest = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private $dataCreation;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $token = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $expiresAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $codeVerif = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private $codeExp;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $resp = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $resp_email = null;
    
    public function getCodeVerif(): ?string
    {
        return $this->codeVerif;
    }
    
    public function setCodeVerif(?string $codeVerif): self
    {
        $this->codeVerif = $codeVerif;

        return $this;
    }

    public function getCodeExp()
    {
        if($this->codeExp != null){
            return $this->codeExp->format('Y-m-d H:i:s');
        }else{
            return $this->codeExp;
        }
    }

    public function setCodeExp(): self
    { 
        date_default_timezone_set("America/Sao_Paulo");
        $this->codeExp = new \DateTime('+30 minutes');
        return $this;
    }

    public function getResp(): ?string
    {
        return $this->resp;
    }

    public function setResp(?string $resp): self
    {
        $this->resp = $resp;

        return $this;
    }

    public function getRespEmail(): ?string
    {
        return $this->resp_email;
    }

    public function setRespEmail(string $resp_email): self
    {
        $this->resp_email = $resp_email;

        return $this;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?\DateTimeInterface $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }


    public function getMudancas(): ?string
    {
        return $this->mudancas;
    }

    public function setMudancas(string $mudancas): self
    {
        $this->mudancas = $mudancas;

        return $this;
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

    public function getImplemented(): ?string
    {
        return $this->implemented;
    }

    public function setImplemented(?string $implemented): self
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

    public function setAppMan(?int $appMan): self
    {
        $this->appMan = $appMan;

        return $this;
    }



    public function getAppGest()
    {
        return $this->appGest;
    }

    public function setAppGest(?int $appGest): self
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








}
