<?php

namespace Easanles\AtletismoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ValorRequisito
 *
 * @ORM\Table(name="vrq")
 * @ORM\Entity
 */
class ValorRequisito
{
    /**
     * @var integer
     * @ORM\Column(name="idreq", type="smallint")
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Requisito", inversedBy="valores", cascade={"all"}) 
     */
    private $idReq;
    
    /**
     * @var string
     * @ORM\Column(name="nombrecom", type="string", length=255)
     * @ORM\Id
     */
    private $nombreCom;
     
    /**
     * @var integer
     * @ORM\Column(name="tempcom", type="smallint")
     * @ORM\Id
     */
    private $tempCom;
    
    /**
     * @var string
     * @ORM\Column(name="valorvrq", type="string", length=255)
     */
    private $valor;
    
    /******************* GETTERS & SETTERS **************************/
    
	public function getIdReq() {
		return $this->idReq;
	}
	public function setIdReq($idReq) {
		$this->idReq = $idReq;
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
	public function getValor() {
		return $this->valor;
	}
	public function setValor($valor) {
		$this->valor = $valor;
		return $this;
	}
	
    
    

}
