<?php

namespace Easanles\AtletismoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Ronda
 * 
 * @ORM\Table(name="ron")
 * @ORM\Entity()
 */
class Ronda {
	
	/**
	 * @var integer
	 * @ORM\Column(name="sidron", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $sid;
	
	/**
	 * @var integer
	 * @ORM\Column(name="idron", type="integer")
	 */
	private $id;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Prueba", inversedBy="rondas")
	 * @ORM\JoinColumn(name="sidpru", referencedColumnName="sidpru")
	 */
	private $sidPru;
	
	/**
	 * @var integer
	 * @ORM\Column(name="numron", type="smallint", options={"default":1})
	 */
	private $num;

	/**
	 * @var string
	 * @ORM\Column(name="nombreron", type="string", length=255, nullable=true)
	 */
	private $nombre;
	
	
	/********************* FOREIGN KEYS *****************************/
	 
	/**
	 * @var array_collection
	 **/
	private $intentos;
	 
	public function __construct() {
		$this->intentos = new ArrayCollection();
		$this->num = 1;
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
	public function getSidPru() {
		return $this->sidPru;
	}
	public function setSidPru($sidPru) {
		$this->sidPru = $sidPru;
		return $this;
	}
	public function getNum() {
		return $this->num;
	}
	public function setNum($num) {
		$this->num = $num;
		return $this;
	}
	public function getNombre() {
		return $this->nombre;
	}
	public function setNombre($nombre) {
		$this->nombre = $nombre;
		return $this;
	}
	
}