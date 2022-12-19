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

    #[ORM\ManyToMany(targetEntity: Person::class)]
    private Collection $sendTo;

    public function __construct()
    {
        $this->sendTo = new ArrayCollection();
    }


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

    /**
     * @return Collection<int, Person>
     */
    public function getSendTo(): Collection
    {
        return $this->sendTo;
    }

    public function addSendTo(Person $sendTo): self
    {
        if (!$this->sendTo->contains($sendTo)) {
            $this->sendTo->add($sendTo);
        }

        return $this;
    }

    public function removeSendTo(Person $sendTo): self
    {
        $this->sendTo->removeElement($sendTo);

        return $this;
    }

}
