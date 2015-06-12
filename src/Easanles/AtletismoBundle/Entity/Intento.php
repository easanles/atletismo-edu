<?php

namespace Easanles\AtletismoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Intento
 *
 * @ORM\Table(name="int")
 * @ORM\Entity
 */
class Intento
{
    /**
     * @var integer
     * @ORM\Column(name="idatl", type="integer")
     * @ORM\Id
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
     * @var integer
     * @ORM\Column(name="numint", type="smallint")
     * @ORM\Id
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
     * @ORM\Column(name="origenint", type="string", length=255)
     */
    private $origen;
    
    /**
     * @var string
     * @ORM\Column(name="premiosint", type="string", length=255)
     */
    private $premios;
    
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
	
    
    
}
