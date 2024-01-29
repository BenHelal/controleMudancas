<?php

namespace App\Entity;

use App\Repository\EmailToSendConfigRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmailToSendConfigRepository::class)]
class EmailToSendConfig
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titleOfMessage = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $subjectMessage = null;

    #[ORM\Column(length: 255)]
    private ?string $body = null;
    
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sendTo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sendFrom = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitleOfMessage(): ?string
    {
        return $this->titleOfMessage;
    }

    public function setTitleOfMessage(?string $titleOfMessage): static
    {
        $this->titleOfMessage = $titleOfMessage;

        return $this;
    }

    public function getSubjectMessage(): ?string
    {
        return $this->subjectMessage;
    }

    public function setSubjectMessage(?string $subjectMessage): static
    {
        $this->subjectMessage = $subjectMessage;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function getSendTo(): ?string
    {
        return $this->sendTo;
    }

    public function setSendTo(?string $sendTo): static
    {
        $this->sendTo = $sendTo;

        return $this;
    }

    public function getSendFrom(): ?string
    {
        return $this->sendFrom;
    }

    public function setSendFrom(?string $sendFrom): static
    {
        $this->sendFrom = $sendFrom;

        return $this;
    }
}
