<?php

namespace Easanles\AtletismoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Prueba
 * 
 * @ORM\Table(name="pru")
 * @ORM\Entity(repositoryClass="Easanles\AtletismoBundle\Entity\Repository\PruebaRepository")
 */
class Prueba {
	
	/**
	 * @var integer
	 * @ORM\Column(name="sidpru", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $sid;
	
	/**
	 * @var integer
	 * @ORM\Column(name="idpru", type="integer")
	 */
	private $id;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Competicion", inversedBy="pruebas")
	 * @ORM\JoinColumn(name="sidcom", referencedColumnName="sidcom")
	 */
	private $sidCom;
	
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
	 * @ORM\ManyToOne(targetEntity="Categoria", inversedBy="pruebas")
	 * @ORM\JoinColumn(name="idcat", referencedColumnName="idcat")
	 */
	private $idCat;
	
	/**
	 * @ORM\ManyToOne(targetEntity="TipoPruebaModalidad", inversedBy="pruebas")
	 * @ORM\JoinColumn(name="sidtprm", referencedColumnName="sidtprm")
	 */
	private $sidTprm;
	
	
	/********************* FOREIGN KEYS *****************************/
	 
	/**
	 * @var array_collection
	 * @ORM\OneToMany(targetEntity="Inscripcion", mappedBy="sidPru", cascade={"all"})
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
	
	public function getSid() {
		return $this->sid;
	}
	public function setSid($sid) {
		$this->sid = $sid;
		return $this;
	}
	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->id = $id;
		return $this;
	}
	public function getSidCom() {
		return $this->sidCom;
	}
	public function setSidCom($sidCom) {
		$this->sidCom = $sidCom;
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
	public function getSidTprm() {
		return $this->sidTprm;
	}
	public function setSidTprm($sidTprm) {
		$this->sidTprm = $sidTprm;
		return $this;
	}
	
}