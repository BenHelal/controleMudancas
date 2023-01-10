<?php

namespace App\Entity;

use App\Repository\EmailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmailRepository::class)]
class Email
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $body = null;

    #[ORM\ManyToOne]
    private ?Person $SendBy = null;

    #[ORM\ManyToOne(targetEntity: Person::class)]
    private ?Person $sendTo;

    #[ORM\ManyToOne(cascade: ['persist'])]
    private ?Mudancas $mudancas = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;
        return $this;
    }

    public function getSendBy(): ?Person
    {
        return $this->SendBy;
    }

    public function setSendBy(?Person $SendBy): self
    {
        $this->SendBy = $SendBy;
        return $this;
    }

    public function getSendTo(): ?Person
    {
        return $this->sendTo;
    }

    public function setSendTo(?Person $sendTo): self
    {
        $this->sendTo = $sendTo;
        return $this;
    }

    public function getMudancas(): ?mudancas
    {
        return $this->mudancas;
    }

    public function setMudancas(?mudancas $mudancas): self
    {
        $this->mudancas = $mudancas;

        return $this;
    }

}
