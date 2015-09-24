<?php

namespace Easanles\AtletismoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Competicion
 * 
 * @ORM\Table(name="com")
 * @ORM\Entity(repositoryClass="Easanles\AtletismoBundle\Entity\Repository\CompeticionRepository")
 * @Vich\Uploadable
 */
class Competicion {
	
   /**
    * @var integer
    * @ORM\Column(name="sidcom", type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
   */
	private $sid;
	
   /**
   * @var string
   * @ORM\Column(name="nombrecom", type="string", length=255)
   */
   private $nombre;
   
   /**
   * @var integer
   * @ORM\Column(name="tempcom", type="smallint")
   */
   private $temp;
   
   /**
   * @var string
   * @ORM\Column(name="ubicacioncom", type="string", length=255, nullable=true)
   */
   private $ubicacion;
   
   /**
   * @var string
   * @ORM\Column(name="sedecom", type="string", length=255, nullable=true)
   */
   private $sede;
   
   /**
   * @var \DateTime
   * @ORM\Column(name="fechacom", type="date", nullable=true)
   */
   private $fecha;
   
   /**
   * @var string
   * @ORM\Column(name="desccom", type="text", nullable=true)
   */
   private $desc;
   
   /**
   * @var string
   * @ORM\Column(name="nivelcom", type="string", length=255, nullable=true)
   */
   private $nivel;
   
   /**
   * @var string
   * @ORM\Column(name="federcom", type="string", length=255, nullable=true)
   */
   private $feder;
   
   /**
   * @var string
   * @ORM\Column(name="webcom", type="string", length=255, nullable=true)
   */
   private $web;
   
   /**
   * @var string
   * @ORM\Column(name="emailcom", type="string", length=255, nullable=true)
   */
   private $email;
   
   /**
   * @var string
   * @ORM\Column(name="cartelcom", type="string", length=255, nullable=true)
   */
   private $cartel;
   
   /**
    * @var File
    * @Vich\UploadableField(mapping="competicion_image", fileNameProperty="cartel") 
    */
   private $cartelFile;
   
   /**
    * @var \DateTime
    * @ORM\Column(type="datetime", nullable=true)
   */
   private $updatedAt;
   
   /**
   * @var boolean
   * @ORM\Column(name="esfedercom", type="boolean", options={"default":0})
   */
   private $esFeder = false;
   
   /**
   * @var boolean
   * @ORM\Column(name="esoficialcom", type="boolean", options={"default":0})
   */
   private $esOficial = false;
   
   /********************* FOREIGN KEYS *****************************/
     
   /**
    * @var array_collection
    * @ORM\OneToMany(targetEntity="Prueba", mappedBy="sidCom", cascade={"all"})
    **/
   private $pruebas;
   
   /**
    * @var array_collection
    * ORM\OneToMany(targetEntity="Participacion", mappedBy="sid", cascade={"all"})
    **/
   private $participaciones;
   
   /**
    * @var array_collection
    * ORM\OneToMany(targetEntity="ValorRequisito", mappedBy="sid", cascade={"all"})
    **/
   private $valoresRequisitos;
   
   public function __construct() {
   	$this->pruebas = new ArrayCollection();
   	$this->participaciones = new ArrayCollection();
   	$this->valoresRequisitos = new ArrayCollection();
   }
   
   public function getPruebas() {
   	return $this->pruebas;
   }
	public function getParticipaciones() {
		return $this->participaciones;
	}
	public function getValoresRequisitos() {
		return $this->valoresRequisitos;
	}
	   
   /******************* GETTERS & SETTERS **************************/
   
	public function getSid() {
		return $this->sid;
	}
	public function setSid($sid) {
		$this->sid = $sid;
		return $this;
	}
	public function getNombre() {
		return $this->nombre;
	}
	public function setNombre($nombre) {
		$this->nombre = $nombre;
		return $this;
	}
	public function getTemp() {
		return $this->temp;
	}
	public function setTemp($temp) {
		$this->temp = $temp;
		return $this;
	}
	public function getUbicacion() {
		return $this->ubicacion;
	}
	public function setUbicacion($ubicacion) {
		$this->ubicacion = $ubicacion;
		return $this;
	}
	public function getSede() {
		return $this->sede;
	}
	public function setSede($sede) {
		$this->sede = $sede;
		return $this;
	}
	public function getFecha() {
		return $this->fecha;
	}
	public function setFecha($fecha) {
		$this->fecha = $fecha;
		return $this;
	}
	public function getDesc() {
		return $this->desc;
	}
	public function setDesc($desc) {
		$this->desc = $desc;
		return $this;
	}
	public function getNivel() {
		return $this->nivel;
	}
	public function setNivel($nivel) {
		$this->nivel = $nivel;
		return $this;
	}
	public function getFeder() {
		return $this->feder;
	}
	public function setFeder($feder) {
		$this->feder = $feder;
		return $this;
	}
	public function getWeb() {
		return $this->web;
	}
	public function setWeb($web) {
		$this->web = $web;
		return $this;
	}
	public function getEmail() {
		return $this->email;
	}
	public function setEmail($email) {
		$this->email = $email;
		return $this;
	}
	public function getCartel() {
		return $this->cartel;
	}
	public function setCartel($cartel) {
		$this->cartel = $cartel;
		return $this;
	}
	
	//*******VICH**********
	public function getUpdatedAt() {
		return $this->updatedAt;
	}
	public function setUpdatedAt($updatedAt) {
		$this->updatedAt = $updatedAt;
		return $this;
	}
	public function setCartelFile(File $image = null){
		$this->cartelFile = $image;
		if ($image) {
			$this->updatedAt = new \DateTime('now');
		}
	}
	/**
	* @return File
	*/
   public function getCartelFile() {
		 return $this->cartelFile;
	}
	
	public function getEsFeder() {
		return $this->esFeder;
	}
	public function setEsFeder($esFeder) {
		$this->esFeder = $esFeder;
		return $this;
	}
	public function getEsOficial() {
		return $this->esOficial;
	}
	public function setEsOficial($esOficial) {
		$this->esOficial = $esOficial;
		return $this;
	}
	
	/********************** VALIDACION ***********************/
	
	/**
	 * @Assert\Callback
	 */
	public function validate(ExecutionContextInterface $context) {
		$this->nombre = strip_tags($this->nombre);
		if (($this->esFeder == true) && ($this->esOficial == false)) {
			$this->esOficial = true;
		/*   if ($this->unique == null) $texto = "null";
		   else if ($this->unique == false) $texto = "false";
		   else if ($this->unique == true) $texto = "true";
			$context->buildViolation($texto)
				->atPath('nombre')
				->addViolation();
	   if ($this->unique == null) {
			//throw new Exception("This entity hasn't been checked for unique condition");
		} else if ($this->unique != false) {
			$context->buildViolation('Ya existe una competición con este nombre para la temporada '.$this->temp)
				->atPath('nombre')
				->addViolation();
		} else {
			$this->nombre = strip_tags($this->nombre);
			if (($this->esFeder == true) && ($this->esOficial == false)) {
				$this->esOficial = true;
	   	}
		}
		*/
		}
	}

	
   
}