<?php

namespace App\Entity;

use App\Repository\IconsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: IconsRepository::class)]
class Icons
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;
    
    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[Assert\File(
        maxSize: '1024k',
        mimeTypes: ['image/png'],
        mimeTypesMessage: 'Carregue uma imagem png válida',
    )]
    #[ORM\Column(length: 255, nullable: true)]
    private $icon;

    #[ORM\Column(length: 255, nullable: true)]
    private $link;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName( $name)
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }   
    
    public function setLink($link)
    {
        $this->link = $link;
        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }    
    
    public function setType( $type)
    {
        $this->type = $type;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }    

    public function getIcon()
    {
        return $this->icon;
    }

    public function setIcon( $icon)
    {
        $this->icon = $icon;
        return $this;
    }
}
