<?php

namespace Easanles\AtletismoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Inscripcion
 *
 * @ORM\Table(name="ins")
 * @ORM\Entity
 */
class Inscripcion
{
    /**
     * @var integer
     * @ORM\Column(name="idatl", type="integer")
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Atleta", inversedBy="inscripciones", cascade={"all"})
     */
    private $idAtl;

    /**
     * @var integer
     * @ORM\Column(name="idpru", type="integer")
     * @ORM\Id
     */
    private $idPru;
    
    /**
     * @var string
     * @ORM\Column(name="nombrecom", type="string", length=255)
     * @ORM\Id
     */
    private $nombreCom;
    
    /**
     * @var integer
     * @ORM\Column(name="tempcom", type="smallint")
     * @ORM\Id
     */
    private $tempCom;
	
    /**
     * @var string
     * @ORM\Column(name="origenins", type="string", length=255, nullable=true)
     */
    private $origen;
    
    /**
     * @var \DateTime
     * @ORM\Column(name="fechains", type="datetime")
     */
    private $fecha;
    
    /**
     * @var string
     * @ORM\Column(name="estadoins", type="string", length=255, nullable=true)
     */
    private $estado;
    
    /**
     * @var string
     * @ORM\Column(name="costeins", type="decimal", precision=10, scale=2, options={"default":0.00})
     */
    private $coste = "0.00";
    
    /**
     * @var integer
     * @ORM\Column(name="codgrupoins", type="integer", nullable=true)
     */
    private $codGrupo;
    
    /******************* GETTERS & SETTERS **************************/
    
	public function getIdAtl() {
		return $this->idAtl;
	}
	public function setIdAtl($idAtl) {
		$this->idAtl = $idAtl;
		return $this;
	}
	public function getIdPru() {
		return $this->idPru;
	}
	public function setIdPru($idPru) {
		$this->idPru = $idPru;
		return $this;
	}
	public function getNombreCom() {
		return $this->nombreCom;
	}
	public function setNombreCom($nombreCom) {
		$this->nombreCom = $nombreCom;
		return $this;
	}
	public function getTempCom() {
		return $this->tempCom;
	}
	public function setTempCom($tempCom) {
		$this->tempCom = $tempCom;
		return $this;
	}
	public function getOrigen() {
		return $this->origen;
	}
	public function setOrigen($origen) {
		$this->origen = $origen;
		return $this;
	}
	public function getFecha() {
		return $this->fecha;
	}
	public function setFecha(\DateTime $fecha) {
		$this->fecha = $fecha;
		return $this;
	}
	public function getEstado() {
		return $this->estado;
	}
	public function setEstado($estado) {
		$this->estado = $estado;
		return $this;
	}
	public function getCoste() {
		return $this->coste;
	}
	public function setCoste($coste) {
		$this->coste = $coste;
		return $this;
	}
	public function getCodGrupo() {
		return $this->codGrupo;
	}
	public function setCodGrupo($codGrupo) {
		$this->codGrupo = $codGrupo;
		return $this;
	}
	
    
    
}
