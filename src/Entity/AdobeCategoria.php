<?php

namespace App\Entity;

use App\Repository\AdobeCategoriaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use App\Entity\AdobeReunion;

/**
 * @ORM\Entity(repositoryClass=AdobeCategoriaRepository::class)
 */
class AdobeCategoria
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $scoId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $prefix;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $enlace;

    /**
     * @ORM\ManyToOne(targetEntity=AdobeCategoria::class, inversedBy="categoriasHijo")
     */
    private $categoriaPadre;

    /**
     * @ORM\OneToMany(targetEntity=AdobeCategoria::class, mappedBy="categoriaPadre")
     */
    private $categoriasHijo;

    /**
     * @ORM\OneToMany(targetEntity=AdobeReunion::class, mappedBy="categoria")
     */
    private $reuniones;

    public function __construct()
    {
        $this->categoriasHijo = new ArrayCollection();
        $this->reuniones = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScoId(): ?string
    {
        return $this->scoId;
    }

    public function setScoId(?string $scoId): self
    {
        $this->scoId = $scoId;

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getEnlace(): ?string
    {
        return $this->enlace;
    }

    public function setEnlace(?string $enlace): self
    {
        $this->enlace = $enlace;

        return $this;
    }

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function setPrefix(?string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function getCategoriaPadre(): ?self
    {
        return $this->categoriaPadre;
    }

    public function setCategoriaPadre(?self $categoriaPadre): self
    {
        $this->categoriaPadre = $categoriaPadre;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getCategoriasHijo(): Collection
    {
        return $this->categoriasHijo;
    }

    public function addCategoriasHijo(self $categoriasHijo): self
    {
        if (!$this->categoriasHijo->contains($categoriasHijo)) {
            $this->categoriasHijo[] = $categoriasHijo;
            $categoriasHijo->setCategoriaPadre($this);
        }

        return $this;
    }

    public function removeCategoriasHijo(self $categoriasHijo): self
    {
        if ($this->categoriasHijo->removeElement($categoriasHijo)) {
            // set the owning side to null (unless already changed)
            if ($categoriasHijo->getCategoriaPadre() === $this) {
                $categoriasHijo->setCategoriaPadre(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getReuniones(): Collection
    {
        return $this->reuniones;
    }

    public function addReunion(AdobeReunion $reunion): self
    {
        if (!$this->reuniones->contains($reunion)) {
            $this->reuniones[] = $reunion;
            $reunion->setCategoria($this);
        }

        return $this;
    }

    public function removeReunion(AdobeReunion $reunion): self
    {
        if ($this->reuniones->removeElement($reunion)) {
            // set the owning side to null (unless already changed)
            if ($reunion->getCategoria() === $this) {
                $reunion->setCategoria(null);
            }
        }

        return $this;
    }

    public function loadFromArray($categoriaArray): ?self{
        $this->setEnlace($categoriaArray['enlace'])
            ->setScoId($categoriaArray['scoId'])
            ->setNombre($categoriaArray['nombre'])
            ->setPrefix($categoriaArray['prefix']);
        return $this;
    }
}
