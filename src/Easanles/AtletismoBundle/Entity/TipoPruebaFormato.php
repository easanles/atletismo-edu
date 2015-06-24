<?php

namespace Easanles\AtletismoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * TipoPruebaFormato
 *
 * @ORM\Table(name="tprf")
 * @ORM\Entity
 */
class TipoPruebaFormato
{
	
	 /**
	 * @var string
	 * @ORM\Column(name="nombretpr", type="string", length=255)
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="TipoPruebaModalidad", inversedBy="modalidades", cascade={"all"})
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
	 private $numInt = 1;

	 /********************* FOREIGN KEYS *****************************/
	 
	 /**
	  * @var array_collection
	  * @ORM\OneToMany(targetEntity="TipoPruebaModalidad", mappedBy="nombre", cascade={"all"})
	  **/
	 private $modalidades;
	 
	 public function __construct() {
	 	$this->modalidades = new ArrayCollection();
	 }
	  
	 public function getModalidades() {
	 	return $this->modalidades;
	 }
	 
	 /******************* GETTERS & SETTERS **************************/ 
	 
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
	public function getNumInt() {
		return $this->numInt;
	}
	public function setNumInt($numInt) {
		$this->numInt = $numInt;
		return $this;
	}

}