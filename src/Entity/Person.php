<?php

namespace App\Entity;

use App\Repository\PersonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
class Person
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 100, nullable:true)]
    private $userName;

    #[ORM\Column(type: 'string', length: 255, nullable:true)]
    private $password;

    #[ORM\Column(type: 'string', length: 255, nullable:true)]
    private $role;

    #[ORM\Column(type: 'string', length: 255, nullable:true)]
    private $permission;

    #[ORM\Column(type: 'string', length: 255, nullable:true)]
    private $email;

    #[ORM\ManyToOne(cascade:[])]
    #[ORM\Column( nullable:true)]
    private Departemant $departemant;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private  $lastConnection = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): self
    {
        $this->userName = $userName;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }    
    
    
    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getPermission(): ?string
    {
        return $this->permission;
    }

    public function setPermission(string $permission): self
    {
        $this->permission = $permission;
        return $this;
    }


    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDepartemant(): ?Departemant
    {
        return $this->departemant;
    }

    public function setDepartemant(Departemant $departemant): self
    {
        $this->departemant = $departemant;

        return $this;
    }

    public function getLastConnection()
    {
        if($this->lastConnection != null){
            return $this->lastConnection->format('Y-m-d H:i:s');
        }else{
            return $this->lastConnection;
        }
    }

    public function setLastConnection(?\DateTimeInterface $lastConnection): self
    {
        $this->lastConnection = $lastConnection;
        return $this;
    }

}
