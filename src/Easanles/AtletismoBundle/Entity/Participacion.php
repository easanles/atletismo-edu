<?php

# Copyright (c) 2016 Eduardo Alonso Sanlés
# Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php

namespace Easanles\AtletismoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Participacion
 *
 * @ORM\Table(name="par")
 * @ORM\Entity(repositoryClass="Easanles\AtletismoBundle\Entity\Repository\ParticipacionRepository")
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
     * @ORM\ManyToOne(targetEntity="Atleta", inversedBy="participaciones")
     * @ORM\JoinColumn(name="idatl", referencedColumnName="idatl")
     */
    private $idAtl;
    
	 /**
	  * @var integer
	  * @ORM\ManyToOne(targetEntity="Competicion", inversedBy="participaciones")
	  * @ORM\JoinColumn(name="sidcom", referencedColumnName="sidcom")
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
    private $asisten;
    
    /******************* GETTERS & SETTERS **************************/
    
	public function getSid() {
		return $this->sid;
	}
	public function setSid($sid) {
		$this->sid = $sid;
		return $this;
	}
    public function getIdAtl() {
		return $this->idAtl;
	}
	public function setIdAtl($idAtl) {
		$this->idAtl = $idAtl;
		return $this;
	}
	public function getSidCom() {
		return $this->sidCom;
	}
	public function setSidCom($sidCom) {
		$this->sidCom = $sidCom;
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
