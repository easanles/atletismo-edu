<?php

namespace Easanles\AtletismoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * TipoPruebaFormato
 *
 * @ORM\Table(name="tprf")
 * @ORM\Entity(repositoryClass="Easanles\AtletismoBundle\Entity\Repository\TipoPruebaFormatoRepository")
 */
class TipoPruebaFormato {
	
	/**
	 * @var integer
	 * @ORM\Column(name="sidtprf", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $sid;
	
	 /**
	 * @var string
	 * @ORM\Column(name="nombretpr", type="string", length=255)
	 */
	 private $nombre;
	 
	 /**
	  * @var string
	  * @ORM\Column(name="unidadestpr", type="string", length=32)
	  */
	 private $unidades;
	 
	 /**
	  * @var integer
	  * @ORM\Column(name="numinttpr", type="smallint", options={"default":1})
	  */
	 private $numint = 1;

	 /********************* FOREIGN KEYS *****************************/
	 
	 /**
	  * @var array_collection
	  * @ORM\OneToMany(targetEntity="TipoPruebaModalidad", mappedBy="sidTprf", cascade={"all"})
	  **/
	 private $modalidades;
	 
	 public function __construct() {
	 	$this->modalidades = new ArrayCollection();
	 }
	  
	 public function getModalidades() {
	 	return $this->modalidades;
	 }
	 
	 public function addModalidad(TipoPruebaModalidad $tprm) {
	 	$tprm->setSidTprf($this);
	 	$this->modalidades->add($tprm);
	 }
	 
	 public function removeModalidad(TipoPruebaModalidad $tprm) {
	    $this->modalidades->removeElement($tprm);
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
	public function getUnidades() {
		return $this->unidades;
	}
	public function setUnidades($unidades) {
		$this->unidades = $unidades;
		return $this;
	}
	public function getNumint() {
		return $this->numint;
	}
	public function setNumint($numint) {
		$this->numint = $numint;
		return $this;
	}

}
