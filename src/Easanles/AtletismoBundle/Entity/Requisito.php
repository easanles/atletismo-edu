<?php

namespace Easanles\AtletismoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Requisito
 *
 * @ORM\Table(name="req")
 * @ORM\Entity
 */
class Requisito
{
    /**
     * @var integer
     * @ORM\Column(name="idreq", type="smallint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var string
     * @ORM\Column(name="tiporeq", type="string", length=32)
     */
    private $tipo;
    
    /**
     * @var string
     * @ORM\Column(name="condicionreq", type="string", length=8)
     */
    private $condicion;
    
    /**
     * @var string
     * @ORM\Column(name="nombretpr", type="string", length=255, nullable=true)
     */
    private $nombreTpr;
    
    /**
     * @var integer
     * @ORM\Column(name="sexotpr", type="smallint", nullable=true)
     */
    private $sexoTpr;
    
    /**
     * @var string
     * @ORM\Column(name="entornotpr", type="string", length=255, nullable=true)
     */
    private $entornoTpr;
    
    /******************* GETTERS & SETTERS **************************/
    
	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->id = $id;
		return $this;
	}
	public function getTipo() {
		return $this->tipo;
	}
	public function setTipo($tipo) {
		$this->tipo = $tipo;
		return $this;
	}
	public function getCondicion() {
		return $this->condicion;
	}
	public function setCondicion($condicion) {
		$this->condicion = $condicion;
		return $this;
	}
	public function getNombreTpr() {
		return $this->nombreTpr;
	}
	public function setNombreTpr($nombreTpr) {
		$this->nombreTpr = $nombreTpr;
		return $this;
	}
	public function getSexoTpr() {
		return $this->sexoTpr;
	}
	public function setSexoTpr($sexoTpr) {
		$this->sexoTpr = $sexoTpr;
		return $this;
	}
	public function getEntornoTpr() {
		return $this->entornoTpr;
	}
	public function setEntornoTpr($entornoTpr) {
		$this->entornoTpr = $entornoTpr;
		return $this;
	}
	

    
}
