<?php

namespace Easanles\AtletismoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TipoPruebaModalidad
 *
 * @ORM\Table(name="tprm")
 * @ORM\Entity
 */
class TipoPruebaModalidad
{
	
	 /**
	 * @var string
	 * @ORM\Column(name="nombretpr", type="string", length=255)
	 * @ORM\Id
	 */
	 private $nombre;
	 
	 /**
	  * @var integer
	  * @ORM\Column(name="sexotpr", type="smallint")
	  * @ORM\Id
	  */
	 private $sexo;
	 
	 /**
	  * @var string
	  * @ORM\Column(name="entornotpr", type="string", length=255)
	  * @ORM\Id
     */
	 private $entorno;
	 
	 /******************* GETTERS & SETTERS **************************/
	 
	public function getNombre() {
		return $this->nombre;
	}
	public function setNombre($nombre) {
		$this->nombre = $nombre;
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
