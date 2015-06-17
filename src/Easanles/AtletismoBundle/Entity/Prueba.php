<?php

namespace Easanles\AtletismoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Prueba
 * 
 * @ORM\Table(name="pru")
 * @ORM\Entity
 */
class Prueba {
	
	/**
	 * @var integer
	 * @ORM\Column(name="idpru", type="integer")
	 * @ORM\Id
	 * ORM\ManyToOne(targetEntity="Competicion", inversedBy="pruebas", cascade={"all"})
	 */
	private $id;
	
	/**
	 * @var string
	 * @ORM\Column(name="nombrecom", type="string", length=255)
	 * @ORM\Id
	 * ORM\ManyToOne(targetEntity="Competicion", inversedBy="pruebas", cascade={"all"})
	 */
	private $nombreCom;
	
	/**
	 * @var integer
	 * @ORM\Column(name="tempcom", type="smallint")
	 * @ORM\Id
	 * ORM\ManyToOne(targetEntity="Competicion", inversedBy="pruebas", cascade={"all"})
	 */
	private $tempCom;
	
	/**
	 * @var integer
	 * @ORM\Column(name="rondapru", type="smallint")
	 */
	private $ronda;

	/**
	 * @var string
	 * @ORM\Column(name="nombrepru", type="string", length=255, nullable=true)
	 */
	private $nombre;
	
	/**
	 * @var integer
	 * @ORM\Column(name="idcat", type="integer")
	 * @ORM\ManyToOne(targetEntity="Categoria", inversedBy="pruebas", cascade={"all"})
	 */
	private $idCat;
	
	/**
	 * @var string
	 * @ORM\Column(name="nombretpr", type="string", length=255)
	 */
	private $nombreTpr;
	
	/**
	 * @var integer
	 * @ORM\Column(name="sexotpr", type="smallint")
	 */
	private $sexoTpr;
	
	/**
	 * @var string
	 * @ORM\Column(name="entornotpr", type="string", length=255)
	 */
	private $entornoTpr;
	
	/********************* FOREIGN KEYS *****************************/
	 
	/**
	 * @var array_collection
	 **/
	private $inscripciones;
	 
	/**
	 * @var array_collection
	 **/
	private $intentos;
	 
	public function __construct() {
		$this->inscripciones = new ArrayCollection();
		$this->intentos = new ArrayCollection();
	}
	 
	public function getInscripciones() {
		return $this->inscripciones;
	}
	public function getIntentos() {
		return $this->intentos;
	}
	
	/******************* GETTERS & SETTERS **************************/
	
	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->id = $id;
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
	public function getRonda() {
		return $this->ronda;
	}
	public function setRonda($ronda) {
		$this->ronda = $ronda;
		return $this;
	}
	public function getNombre() {
		return $this->nombre;
	}
	public function setNombre($nombre) {
		$this->nombre = $nombre;
		return $this;
	}
	public function getIdCat() {
		return $this->idCat;
	}
	public function setIdCat($idCat) {
		$this->idCat = $idCat;
		return $this;
	}
	public function getNombreTpr() {
		return $this->nombreTpr;
	}
	public function setNombreTpr($nombreTpr) {
		$this->nombreTpr = $nombreTpr;
		return $this;
	}
	public function getSexoTpr() {
		return $this->sexoTpr;
	}
	public function setSexoTpr($sexoTpr) {
		$this->sexoTpr = $sexoTpr;
		return $this;
	}
	public function getEntornoTpr() {
		return $this->entornoTpr;
	}
	public function setEntornoTpr($entornoTpr) {
		$this->entornoTpr = $entornoTpr;
		return $this;
	}
	
	
	
	
}