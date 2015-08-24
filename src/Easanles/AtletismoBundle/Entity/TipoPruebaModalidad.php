<?php

namespace Easanles\AtletismoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * TipoPruebaModalidad
 *
 * @ORM\Table(name="tprm")
 * @ORM\Entity(repositoryClass="Easanles\AtletismoBundle\Entity\Repository\TipoPruebaModalidadRepository")
 */
class TipoPruebaModalidad
{
	
	/**
	 * @var integer
	 * @ORM\Column(name="sidtprm", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * ORM\ManyToOne(targetEntity="TipoPruebaModalidad", inversedBy="modalidades", cascade={"all"})
	 */
	private $sid;
	
	 /**
	 * @var integer
	 * @ORM\Column(name="sidtprf", type="integer")
	 */
	 private $sidTprf;
	 
	 /**
	  * @var integer
	  * @ORM\Column(name="sexotpr", type="smallint")
	  */
	 private $sexo;
	 
	 /**
	  * @var string
	  * @ORM\Column(name="entornotpr", type="string", length=255)
     */
	 private $entorno;
	 
	 /********************* FOREIGN KEYS *****************************/
	 
	 /**
	  * @var array_collection
	  **/
	 private $requisitos;
	 
	 /**
	  * @var array_collection
	  **/
	 private $pruebas;
	 
	 public function __construct() {
	 	$this->requisitos = new ArrayCollection();
	 	$this->pruebas = new ArrayCollection();
	 }
	  
	 public function getRequisitos() {
	 	return $this->requisitos;
	 }
	 public function getPruebas() {
	 	return $this->pruebas;
	 }
	 
	 /******************* GETTERS & SETTERS **************************/

	
	public function getSid() {
	   return $this->sid;
	}
	public function setSid($sid) {
	   $this->sid = $sid;
	   return $this;
	}
	public function getSidTprf() {
		return $this->sidTprf;
	}
	public function setSidTprf($sidTprf) {
		$this->sidTprf = $sidTprf;
		return $this;
	}
	public function getSexo() {
		return $this->sexo;
	}
	public function setSexo($sexo) {
		$this->sexo = $sexo;
		return $this;
	}
	public function getEntorno() {
		return $this->entorno;
	}
	public function setEntorno($entorno) {
		$this->entorno = $entorno;
		return $this;
	}

	
	
	 
	 
}
