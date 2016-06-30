<?php

# Copyright (c) 2016 Eduardo Alonso Sanlés
# Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php

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
    * @var \DateTime
    * @ORM\Column(name="fechafincom", type="date", nullable=true)
    */
   private $fechaFin;
   
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
   private $esFeder;
   
   /**
   * @var boolean
   * @ORM\Column(name="esoficialcom", type="boolean", options={"default":0})
   */
   private $esOficial;
   
   /**
   * @var boolean
   * @ORM\Column(name="esvisiblecom", type="boolean", options={"default":0})
   */
   private $esVisible;
   
   /**
   * @var boolean
   * @ORM\Column(name="esinscribcom", type="boolean", options={"default":0})
   */
   private $esInscrib;
   
   /**
    * @var boolean
    * @ORM\Column(name="escuotacom", type="boolean", options={"default":0})
    */
   private $esCuota;
   
   /********************* FOREIGN KEYS *****************************/
     
   /**
    * @var array_collection
    * @ORM\OneToMany(targetEntity="Prueba", mappedBy="sidCom", cascade={"all"})
    **/
   private $pruebas;
   
   /**
    * @var array_collection
    * @ORM\OneToMany(targetEntity="Participacion", mappedBy="sidCom", cascade={"all"})
    **/
   private $participaciones;
   
   public function __construct() {
   	$this->esFeder = false;
   	$this->esOficial = false;
   	$this->esVisible = false;
   	$this->esInscrib = false;
   	$this->esCuota = false;
   	$this->pruebas = new ArrayCollection();
   	$this->participaciones = new ArrayCollection();
   }
   
   public function getPruebas() {
   	return $this->pruebas;
   }
	public function getParticipaciones() {
		return $this->participaciones;
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
	public function getFechaFin() {
		return $this->fechaFin;
	}
	public function setFechaFin($fechaFin) {
		$this->fechaFin = $fechaFin;
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
	public function getEsVisible() {
		return $this->esVisible;
	}
	public function setEsVisible($esVisible) {
		$this->esVisible = $esVisible;
		return $this;
	}
	public function getEsInscrib() {
		return $this->esInscrib;
	}
	public function setEsInscrib($esInscrib) {
		$this->esInscrib = $esInscrib;
		return $this;
	}
	public function getEsCuota() {
		return $this->esCuota;
	}
	public function setEsCuota($esCuota) {
		$this->esCuota = $esCuota;
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
		}
		if ($this->esOficial == true){
			$this->esInscrib = false;
		}
		if (($this->getFecha() != null) && ($this->getFechaFin() == null)){
			$this->fechaFin = $this->fecha;
		}
		if ($this->esCuota == true){
			$this->esFeder = false;
			$this->esOficial = true;
			$this->esVisible = false;
			$this->esInscrib = false;
		}
		if (($this->getFecha() == null) && ($this->getFechaFin() != null)){
			$context->buildViolation("Indicar también la fecha de inicio")
			->atPath('fecha')
			->addViolation();
		}
		if (($this->getFecha() != null) && ($this->getFechaFin() != null)){
			if ($this->getFechaFin() < $this->getFecha()) {
				$context->buildViolation("La fecha de fin es anterior a la fecha de inicio")
				->atPath('fechaFin')
				->addViolation();
			}
		}
	}
   
}