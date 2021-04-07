<?php

namespace App\Entity;

use App\Repository\AdobeReunionRepository;
use App\Entity\AdobeCategoria;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AdobeReunionRepository::class)
 */
class AdobeReunion
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $enlace;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nombre;
    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $scoId;

    /**
     * @ORM\OneToMany(targetEntity=AdobeGrabacion::class, mappedBy="reunion")
     */
    private $grabaciones;

    /**
     * @ORM\ManyToOne(targetEntity=AdobeCategoria::class, inversedBy="reuniones")
     */
    private $categoria;

    public function __construct()
    {
        $this->grabaciones = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getScoId(): ?string {
        return $this->scoId;
    }
    public function setScoId(?string $scoId): self {
        $this->scoId = $scoId; 
        return $this;
    }

    public function getCategoria(): ?AdobeCategoria
    {
        return $this->categoria;
    }

    public function setCategoria(?AdobeCategoria $categoria): self
    {
        $this->categoria = $categoria;

        return $this;
    }

    /**
     * @return Collection|AdobeGrabacion[]
     */
    public function getGrabaciones(): Collection
    {
        return $this->grabaciones;
    }

    public function addGrabacione(AdobeGrabacion $grabacione): self
    {
        if (!$this->grabaciones->contains($grabacione)) {
            $this->grabaciones[] = $grabacione;
            $grabacione->setReunion($this);
        }

        return $this;
    }

    public function removeGrabacione(AdobeGrabacion $grabacione): self
    {
        if ($this->grabaciones->removeElement($grabacione)) {
            // set the owning side to null (unless already changed)
            if ($grabacione->getReunion() === $this) {
                $grabacione->setReunion(null);
            }
        }

        return $this;
    }

    public function loadFromArray($reunionArray){
        $this->setNombre($reunionArray['nombre'])
            ->setScoId($reunionArray['scoId'])
            ->setEnlace($reunionArray['enlace']);
        return $this;    
    }

}
