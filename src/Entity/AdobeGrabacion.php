<?php

namespace App\Entity;

use App\Repository\AdobeGrabacionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AdobeGrabacionRepository::class)
 */
class AdobeGrabacion
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
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $scoId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $linkDetalles;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $linkDesconectado;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $acceso;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $estado;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duracionSegundos;

    /**
     * @ORM\ManyToOne(targetEntity=AdobeReunion::class, inversedBy="grabaciones")
     */
    private $reunion;

    public function __constructor(){
        $this->estado = 'insertado';
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getScoId(): ?string
    {
        return $this->scoId;
    }

    public function setScoId(?string $scoId): self
    {
        $this->scoId = $scoId;

        return $this;
    }

    public function getLinkDetalles(): ?string
    {
        return $this->linkDetalles;
    }

    public function setLinkDetalles(?string $linkDetalles): self
    {
        $this->linkDetalles = $linkDetalles;

        return $this;
    }

    public function getLinkDesconectado(): ?string
    {
        return $this->linkDesconectado;
    }

    public function setLinkDesconectado(string $linkDesconectado): self
    {
        $this->linkDesconectado = $linkDesconectado;

        return $this;
    }

    public function getAcceso(): ?string
    {
        return $this->acceso;
    }

    public function setAcceso(?string $acceso): self
    {
        $this->acceso = $acceso;

        return $this;
    }

    public function getDuracionSegundos(): ?int
    {
        return $this->duracionSegundos;
    }

    public function setDuracionSegundos(?int $duracionSegundos): self
    {
        $this->duracionSegundos = $duracionSegundos;

        return $this;
    }

    public function getReunion(): ?AdobeReunion
    {
        return $this->reunion;
    }

    public function setReunion(?AdobeReunion $reunion): self
    {
        $this->reunion = $reunion;

        return $this;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(?string $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function loadFromArray($grabacionArray): ?self {
        $this->setNombre($grabacionArray['nombre'])
            ->setLinkDesconectado($grabacionArray['linkDesconectado'])
            ->setLinkDetalles($grabacionArray['linkDetalles'])
            ->setScoId($grabacionArray['scoId'])
            ->setAcceso($grabacionArray['acceso'])
            ->setDuracionSegundos($grabacionArray['duracion']);
        return $this;
    }
}
