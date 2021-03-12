<?php

namespace App\Entity;

use App\Repository\SDTicketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SDTicketRepository::class)
 */
class SDTicket
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $sdId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $solicitanteNombre;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fechaInicio;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fechaCierre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sdSitio;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sdEstado;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sdCuenta;
    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fechaImportacion;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fechaRevision;

    /**
     * @ORM\OneToMany(targetEntity=SDRegistroTiempo::class, mappedBy="ticket", orphanRemoval=true)
     */
    private $registrosTiempo;

    public function __construct()
    {
        $this->registrosTiempo = new ArrayCollection();
    }

    public function loadFromArray($ticketInfo): ?self
    {
        $this->setSdId($ticketInfo['ticketId'])
            ->setNombre($ticketInfo['asunto'])
            ->setSolicitanteNombre($ticketInfo['solicitante'])
            ->setSdSitio($ticketInfo['site'])
            ->setSdEstado($ticketInfo['estado'])
        ;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSdId(): ?int
    {
        return $this->sdId;
    }

    public function setSdId(int $sdId): self
    {
        $this->sdId = $sdId;

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getSolicitanteNombre(): ?string
    {
        return $this->solicitanteNombre;
    }

    public function setSolicitanteNombre(?string $solicitanteNombre): self
    {
        $this->solicitanteNombre = $solicitanteNombre;

        return $this;
    }

    public function getFechaInicio(): ?\DateTimeInterface
    {
        return $this->fechaInicio;
    }

    public function setFechaInicio(?\DateTimeInterface $fechaInicio): self
    {
        $this->fechaInicio = $fechaInicio;

        return $this;
    }

    public function getFechaCierre(): ?\DateTimeInterface
    {
        return $this->fechaCierre;
    }

    public function setFechaCierre(?\DateTimeInterface $fechaCierre): self
    {
        $this->fechaCierre = $fechaCierre;

        return $this;
    }

    public function getSdSitio(): ?string
    {
        return $this->sdSitio;
    }

    public function setSdSitio(?string $sdSitio): self
    {
        $this->sdSitio = $sdSitio;

        return $this;
    }

    public function getSdEstado(): ?string
    {
        return $this->sdEstado;
    }

    public function setSdEstado(?string $sdEstado): self
    {
        $this->sdEstado = $sdEstado;

        return $this;
    }

    public function getSdCuenta(): ?string
    {
        return $this->sdCuenta;
    }

    public function setSdCuenta(string $sdCuenta): self
    {
        $this->sdCuenta = $sdCuenta;
        return $this;
    }

    public function getFechaImportacion(): ?\DateTime {
        return $this->fechaImportacion;
    }

    public function setFechaImportacion(\DateTime $fechaImportacion): ?self {
        $this->fechaImportacion = $fechaImportacion; 
        return $this;
    }

    public function getFechaRevision(): ?\DateTime {
        return $this->fechaRevision;
    }

    public function setFechaRevision(\DateTime $fechaRevision): ?self {
        $this->fechaRevision = $fechaRevision; 
        return $this;
    }

    /**
     * @return Collection|SDRegistroTiempo[]
     */
    public function getRegistrosTiempo(): Collection
    {
        return $this->registrosTiempo;
    }

    public function addRegistrosTiempo(SDRegistroTiempo $registrosTiempo): self
    {
        if (!$this->registrosTiempo->contains($registrosTiempo)) {
            $this->registrosTiempo[] = $registrosTiempo;
            $registrosTiempo->setTicket($this);
        }

        return $this;
    }

    public function removeRegistrosTiempo(SDRegistroTiempo $registrosTiempo): self
    {
        if ($this->registrosTiempo->removeElement($registrosTiempo)) {
            // set the owning side to null (unless already changed)
            if ($registrosTiempo->getTicket() === $this) {
                $registrosTiempo->setTicket(null);
            }
        }

        return $this;
    }
}
