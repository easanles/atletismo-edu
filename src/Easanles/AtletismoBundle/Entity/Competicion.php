<?php

namespace Easanles\AtletismoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Competicion
 * 
 * @ORM\Table(name="com")
 * @ORM\Entity(repositoryClass="Easanles\AtletismoBundle\Entity\CompeticionRepository")
 */
class Competicion {

   /**
   * @var string
   * @ORM\Column(name="nombrecom", type="string", length=255)
   * @ORM\Id
   */
   private $nombre;
   
   /**
   * @var integer
   * @ORM\Column(name="tempcom", type="smallint")
   * @ORM\Id
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
   * @ORM\Column(name="fechacom", type="date", nullable=true)
   */
   private $fecha;
   
   /**
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
   * @var boolean
   * @ORM\Column(name="esfedercom", type="boolean", options={"default":0})
   */
   private $esFeder = 0;
   
   /**
   * @var boolean
   * @ORM\Column(name="esoficialcom", type="boolean", options={"default":0})
   */
   private $esOficial = 0;
   
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

   
}