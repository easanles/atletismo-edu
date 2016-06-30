<?php

# Copyright (c) 2016 Eduardo Alonso Sanlés
# Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php

namespace Easanles\AtletismoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;


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
	 private $numint;
	 
	 /**
	  * @var boolean
	  * @ORM\Column(name="escuotatpr", type="boolean", options={"default":0})
	  */
	 private $esCuota;

	 /********************* FOREIGN KEYS *****************************/
	 
	 /**
	  * @var array_collection
	  * @ORM\OneToMany(targetEntity="TipoPruebaModalidad", mappedBy="sidTprf", cascade={"all"})
	  **/
	 private $modalidades;
	 
	 public function __construct() {
	 	$this->numint = 1;
	 	$this->esCuota = false;
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
	public function getEsCuota() {
		return $this->esCuota;
	}
	public function setEsCuota($esCuota) {
		$this->esCuota = $esCuota;
		return $this;
	}

	/**
	 * @Assert\Callback
	 */
	public function validate(ExecutionContextInterface $context) {
		$this->nombre = strip_tags($this->nombre);
	}

	
	
}
