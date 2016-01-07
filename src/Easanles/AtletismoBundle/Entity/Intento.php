<?php

namespace Easanles\AtletismoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Intento
 *
 * @ORM\Table(name="int_")
 * @ORM\Entity(repositoryClass="Easanles\AtletismoBundle\Entity\Repository\IntentoRepository")
 */
class Intento {
	
	 /**
	  * @var integer
	  * @ORM\Column(name="sidint", type="integer")
	  * @ORM\Id
	  * @ORM\GeneratedValue(strategy="AUTO")
	  */
	 private $sid;
	
    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="Atleta", inversedBy="intentos")
     * @ORM\JoinColumn(name="idatl", referencedColumnName="idatl")
     */
    private $idAtl;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="Ronda", inversedBy="intentos")
     * @ORM\JoinColumn(name="sidron", referencedColumnName="sidron")
     */
    private $sidRon;
    
    /**
     * @var integer
     * @ORM\Column(name="numint", type="smallint")
     */
    private $num;
    
    /**
     * @var string
     * @ORM\Column(name="marcaint", type="decimal", precision=10, scale=3, nullable=true)
     */
    private $marca;
    
    /**
     * @var boolean
     * @ORM\Column(name="validezint", type="boolean")
     */
    private $validez;
    
    /**
     * @var string
     * @ORM\Column(name="origenint", type="string", length=255, nullable=true)
     */
    private $origen;
    
    /**
     * @var string
     * @ORM\Column(name="premiosint", type="string", length=255, nullable=true)
     */
    private $premios;
    
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
	public function getSidRon() {
		return $this->sidRon;
	}
	public function setSidRon($sidRon) {
		$this->sidRon = $sidRon;
		return $this;
	}
	public function getNum() {
		return $this->num;
	}
	public function setNum($num) {
		$this->num = $num;
		return $this;
	}
	public function getMarca() {
		return $this->marca;
	}
	public function setMarca($marca) {
		$this->marca = $marca;
		return $this;
	}
	public function getValidez() {
		return $this->validez;
	}
	public function setValidez($validez) {
		$this->validez = $validez;
		return $this;
	}
	public function getOrigen() {
		return $this->origen;
	}
	public function setOrigen($origen) {
		$this->origen = $origen;
		return $this;
	}
	public function getPremios() {
		return $this->premios;
	}
	public function setPremios($premios) {
		$this->premios = $premios;
		return $this;
	}
	
	/********************** VALIDACION ***********************/
	
	/**
	 * @Assert\Callback
	 */
	public function validate(ExecutionContextInterface $context) {
		if (($this->validez == true) && (($this->marca == null) || ($this->marca == ""))) {
			$context->buildViolation("Si el intento es válido, introduzca una marca")
			->atPath('marca')
			->addViolation();
		}
	}
    
}
