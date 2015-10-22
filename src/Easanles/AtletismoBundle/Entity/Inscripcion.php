<?php

namespace Easanles\AtletismoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Inscripcion
 *
 * @ORM\Table(name="ins")
 * @ORM\Entity
 */
class Inscripcion {
	
	 /**
     * @var integer
	  * @ORM\Column(name="sidins", type="integer")
	  * @ORM\Id
	  * @ORM\GeneratedValue(strategy="AUTO")
	  */
	 private $sid;
	  
    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="Atleta", inversedBy="inscripciones")
     * @ORM\JoinColumn(name="idatl", referencedColumnName="idatl")
     */
    private $idAtl;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="Prueba", inversedBy="inscripciones")
     * @ORM\JoinColumn(name="sidpru", referencedColumnName="sidpru")
     */
    private $sidPru;
    
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
    
   public function getSid() {
    	return $this->sid;
   }
   public function setSid($sid) {
    	$this->sid = $sid;
    	return $this;
   }
	public function getIdAtl() {
		return $this->idAtl;
	}
	public function setIdAtl($idAtl) {
		$this->idAtl = $idAtl;
		return $this;
	}
   public function getSidPru() {
    	return $this->sidPru;
   }
   public function setSidPru($sidPru) {
      $this->sidPru = $sidPru;
   	return $this;
   }
	public function getIdPru() {
		return $this->idPru;
	}
	public function setIdPru($idPru) {
		$this->idPru = $idPru;
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
