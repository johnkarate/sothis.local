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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sdImpacto;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sdTipoSolicitud;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sdModo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sdGrupo;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sdTecnico;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sdPrioridad;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sdCategoria;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sdSubcategoria;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sdArticulo;
    
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
    public function loadFromDetailsArray($ticketInfo): ?self {
        $this->setSdCategoria($ticketInfo['Categoría'])
            ->setSdSubcategoria($ticketInfo['Subcategoría'])
            ->setSdArticulo($ticketInfo['Artículo'])
            ->setSdImpacto($ticketInfo['Impacto'])
            ->setSdTipoSolicitud($ticketInfo['Tipo de solicitud'])
            ->setSdModo($ticketInfo['Modo'])
            ->setSdGrupo($ticketInfo['Grupo'])
            ->setSdTecnico($ticketInfo['Técnico'])
            ->setSdPrioridad($ticketInfo['Prioridad'])
            ->setSdEstado($ticketInfo['Estado']);
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

    public function getSdImpacto(): ?string 
    {
        return $this->sdImpacto;
    }
    public function setSdImpacto($sdImpacto): self 
    {
        $this->sdImpacto = $sdImpacto;
        return $this;
    }

    public function getSdTipoSolicitud(): ?string 
    {
        return $this->sdTipoSolicitud;
    }

    public function setSdTipoSolicitud($sdTipoSolicitud): self
    {
        $this->sdTipoImpacto = $sdTipoSolicitud;
        return $this;
    }

    public function getSdModo(): ?string {
        return $this->sdModo; 
    }
    public function setSdModo($sdModo): self {
        $this->sdModo = $sdModo;
        return $this;
    }

    public function getSdGrupo(): ?string {
        return $this->sdGrupo; 
    }
    public function setSdGrupo($sdGrupo): self {
        $this->sdGrupo = $sdGrupo;
        return $this;
    }

    public function getSdTecnico(): ?string {
        return $this->sdTecnico;
    }
    public function setSdTecnico($sdTecnico): self {
        $this->sdTecnico = $sdTecnico;
        return $this;
    }

    public function getSdPrioridad(): ?string {
        return $sdPrioridad;
    }
    public function setSdPrioridad($sdPrioridad): self {
        $this->sdPrioridad = $sdPrioridad;
        return $this;
    }

    public function getSdCategoria(): ?string {
        return $this->sdCategoria; 
    }
    public function setSdCategoria($sdCategoria): self {
        $this->sdCategoria = $sdCategoria;
        return $this;
    }

    public function getSdSubcategoria(): ?string {
        return $this->sdSubcategoria;
    }
    public function setSdSubcategoria($sdSubcategoria): self {
        $this->sdSubcategoria = $sdSubcategoria;
        return $this;
    }

    public function getSdArticulo(): ?string {
        return $this->sdArticulo;
    }
    public function setSdArticulo($sdArticulo): ?self {
        $this->sdArticulo = $sdArticulo;
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

    public function isClienteMdE(){
        return (!empty($this->getSdSitio())) && in_array(trim($this->getSdSitio()), ['EDEM Site', 'Edificio Lanzadera']);
    }
    public function isCategorizable(){
        $isCategorizable = $this->isClienteMdE() && (empty($this->getSdTipoSolicitud()) || strtolower(trim($this->getSdTipoSolicitud())) == 'no asignado') && (empty($this->getSdGrupo()) || strtolower(trim($this->getSdGrupo())) == 'co5-n1-soporteusuario');
        return $isCategorizable;
    }

    public function calcFechaRevision(){
        $fechaAhora = new \DateTime(); 
        $fechaRevision = $this->getFechaRevision(); 
        if(!empty($fechaRevision) && $fechaRevision > $fechaAhora){
            return $fechaRevision; 
        }
        if($this->isClienteMdE()){
            $fechaAhora->add(new \DateInterval("PT1H"));
        } else {
            $fechaAhora->add(new \DateInterval("PT3H"));
        }
        return $fechaAhora;
    }

    /**
     * Devuelve true si el nombre del ticket coincide con todas las strings de $todasStr y/o coincide con alguna de las $optionalStr
     */
    public function ticketNombreCoincideStrings($todasStr=[], $opcionalStr=[]){
        $matchAlguna = false;
        $ticketNombre = $this->getNombre(); 

        foreach($todasStr as $str){
            if(stripos($str, $ticketNombre) === false){
                return false;
            }
        }
        foreach($opcionalStr as $str){
            if(!$matchAlguna && stripos($str, $ticketNombre)){
                return true;
            }
        }
        return $matchAlguna;
    }
}
