<?php

namespace App\Entity;

use App\Repository\ConfigEmailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConfigEmailRepository::class)]
class ConfigEmail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $host = null;

    #[ORM\Column]
    private ?bool $smtpAuth = null;

    #[ORM\Column]
    private ?int $port = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $emailSystem = null;

    #[ORM\Column(length: 255)]
    private ?string $titleObj = null;

    #[ORM\Column(length: 255)]
    private ?string $subject = null;

    #[ORM\Column(length: 255)]
    private ?string $chartSet = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function setHost(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    public function isSmtpAuth(): ?bool
    {
        return $this->smtpAuth;
    }

    public function setSmtpAuth(bool $smtpAuth): self
    {
        $this->smtpAuth = $smtpAuth;

        return $this;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function setPort(int $port): self
    {
        $this->port = $port;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmailSystem(): ?string
    {
        return $this->emailSystem;
    }

    public function setEmailSystem(string $emailSystem): self
    {
        $this->emailSystem = $emailSystem;

        return $this;
    }

    public function getTitleObj(): ?string
    {
        return $this->titleObj;
    }

    public function setTitleObj(string $titleObj): self
    {
        $this->titleObj = $titleObj;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getChartSet(): ?string
    {
        return $this->chartSet;
    }

    public function setChartSet(string $chartSet): self
    {
        $this->chartSet = $chartSet;

        return $this;
    }
}
