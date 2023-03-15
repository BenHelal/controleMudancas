<?php

namespace App\Entity;

use App\Repository\ApiTokenRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApiTokenRepository::class)]
class ApiToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $token = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $expiresAt = null;

    #[ORM\ManyToOne(inversedBy: 'apiTokens')]
    private ?Client $client = null;

    #[ORM\ManyToOne]
    private ?Mudancas $mud = null;

    public function __construct(Client $client,Mudancas $mud)
    {
        $this->token = bin2hex(random_bytes(60));
        $this->client = $client;
        $this->mud = $mud;
        $this->expiresAt = new \DateTime('+1 week');
    }

    public function renewExpiresAt()
    {
        $this->expiresAt = new \DateTime('+1 day');
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

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getMud(): ?Mudancas
    {
        return $this->mud;
    }

    public function setMud(?Mudancas $mud): self
    {
        $this->mud = $mud;

        return $this;
    }
}
