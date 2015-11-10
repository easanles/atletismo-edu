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
     * @var string
     * @ORM\Column(name="costepru", type="decimal", precision=10, scale=2, options={"default":0.00})
     */
   private $coste;
	
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
	 * @ORM\OneToMany(targetEntity="Ronda", mappedBy="sidPru", cascade={"all"})
	 **/
	private $rondas;
	 
	public function __construct() {
		$this->inscripciones = new ArrayCollection();
		$this->rondas = new ArrayCollection();
		$this->coste = 0.00;
	}
	 
	public function getInscripciones() {
		return $this->inscripciones;
	}
	public function getRondas() {
		return $this->rondas;
	}
	
	public function addRonda(Ronda $ron) {
		$ron->setSidPru($this);
		$this->rondas->add($ron);
	}
	public function removeRonda(Ronda $ron) {
		$this->rondas->removeElement($ron);
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
	public function getCoste() {
		return $this->coste;
	}
	public function setCoste($coste) {
		$this->coste = $coste;
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