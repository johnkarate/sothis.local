<?php

namespace App\Entity;

use App\Repository\SDRegistroTiempoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SDRegistroTiempoRepository::class)
 */
class SDRegistroTiempo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=SDTicket::class, inversedBy="registrosTiempo")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ticket;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tecnico;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $tiempo;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $inicio;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fin;

    /**
     * @ORM\Column(type="string", length=2048, nullable=true)
     */
    private $descripcion;

    public function loadFromArray($registroInfo): ?self {
        $this->setTecnico($registroInfo['tecnico'])
            ->setTiempo($registroInfo['tiempo'])
            ->setInicio($registroInfo['inicio'])
            ->setFin($registroInfo['fin'])
            ->setDescripcion($registroInfo['descripcion']);

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTicket(): ?SDTicket
    {
        return $this->ticket;
    }

    public function setTicket(?SDTicket $ticket): self
    {
        $this->ticket = $ticket;

        return $this;
    }

    public function getTecnico(): ?string
    {
        return $this->tecnico;
    }

    public function setTecnico(?string $tecnico): self
    {
        $this->tecnico = $tecnico;

        return $this;
    }

    public function getTiempo(): ?int
    {
        return $this->tiempo;
    }

    public function setTiempo(?int $tiempo): self
    {
        $this->tiempo = $tiempo;

        return $this;
    }

    public function getInicio(): ?\DateTimeInterface
    {
        return $this->inicio;
    }

    public function setInicio(?\DateTimeInterface $inicio): self
    {
        $this->inicio = $inicio;

        return $this;
    }

    public function getFin(): ?\DateTimeInterface
    {
        return $this->fin;
    }

    public function setFin(?\DateTimeInterface $fin): self
    {
        $this->fin = $fin;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = substr($descripcion, 0, 2047);

        return $this;
    }
}
