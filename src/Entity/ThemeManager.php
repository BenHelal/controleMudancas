<?php

namespace App\Entity;

use App\Repository\ThemeManagerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ThemeManagerRepository::class)]
class ThemeManager
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $colorBar = null;

    #[ORM\Column(length: 255)]
    private ?string $titleColor = null;

    #[ORM\Column(length: 255)]
    private ?string $errorColor = null;

    #[ORM\Column(length: 255)]
    private ?string $warrningColor = null;

    #[ORM\Column(length: 255)]
    private ?string $validColor = null;

    #[Assert\File(
        maxSize: '1024k',
        mimeTypes: ['image/png'],
        mimeTypesMessage: 'Carregue uma imagem png válida',
    )]
    #[ORM\Column(length: 255, nullable: true)]
    private  $logo;
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getColorBar(): ?string
    {
        return $this->colorBar;
    }

    public function setColorBar(string $colorBar): self
    {
        $this->colorBar = $colorBar;

        return $this;
    }

    public function getTitleColor(): ?string
    {
        return $this->titleColor;
    }

    public function setTitleColor(string $titleColor): self
    {
        $this->titleColor = $titleColor;

        return $this;
    }

    public function getErrorColor(): ?string
    {
        return $this->errorColor;
    }

    public function setErrorColor(string $errorColor): self
    {
        $this->errorColor = $errorColor;

        return $this;
    }

    public function getWarrningColor(): ?string
    {
        return $this->warrningColor;
    }

    public function setWarrningColor(string $warrningColor): self
    {
        $this->warrningColor = $warrningColor;

        return $this;
    }

    public function getValidColor(): ?string
    {
        return $this->validColor;
    }

    public function setValidColor(string $validColor): self
    {
        $this->validColor = $validColor;

        return $this;
    }

    public function getLogo(){
        return $this->logo;
    }

    public function setLogo( $logo)
    {
        $this->logo = $logo;

        return $this;
    }
}
