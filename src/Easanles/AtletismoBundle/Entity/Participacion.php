<?php

namespace Easanles\AtletismoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Participacion
 *
 * @ORM\Table(name="par")
 * @ORM\Entity
 */
class Participacion {
	
   /**
    * @var integer
    * @ORM\Column(name="sidpar", type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $sid;
    
    /**
     * @var integer
     * @ORM\Column(name="idatl", type="integer")
     * @ORM\ManyToOne(targetEntity="Atleta", inversedBy="participaciones", cascade={"all"})
     */
    private $idAtl;
    
	 /**
	  * @var integer
	  * @ORM\Column(name="sidcom", type="integer")
	  * @ORM\ManyToOne(targetEntity="Atleta", inversedBy="participaciones", cascade={"all"})
     */
    private $sidCom;
    
    /**
     * @var string
     * @ORM\Column(name="dorsalpar", type="string", length=32, nullable=true)
     */
    private $dorsal;
    
    /**
     * @var boolean
     * @ORM\Column(name="asistenpar", type="boolean", options={"default":0})
     */
    private $asisten = 0;
    
    /******************* GETTERS & SETTERS **************************/
    
	public function getIdAtl() {
		return $this->idAtl;
	}
	public function setIdAtl($idAtl) {
		$this->idAtl = $idAtl;
		return $this;
	}
	public function getNombreCom() {
		return $this->nombreCom;
	}
	public function setNombreCom($nombreCom) {
		$this->nombreCom = $nombreCom;
		return $this;
	}
	public function getTempCom() {
		return $this->tempCom;
	}
	public function setTempCom($tempCom) {
		$this->tempCom = $tempCom;
		return $this;
	}
	public function getDorsal() {
		return $this->dorsal;
	}
	public function setDorsal($dorsal) {
		$this->dorsal = $dorsal;
		return $this;
	}
	public function getAsisten() {
		return $this->asisten;
	}
	public function setAsisten($asisten) {
		$this->asisten = $asisten;
		return $this;
	}
	
    
    
}
