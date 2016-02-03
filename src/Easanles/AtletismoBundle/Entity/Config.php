<?php

namespace Easanles\AtletismoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Config
 *
 * @ORM\Table(name="cfg")
 * @ORM\Entity
 */
class Config {
	
    /**
     * @var string
     * @ORM\Column(name="clavecfg", type="string", length=255)
     * @ORM\Id
     */
    private $clave;
    
    /**
     * @var string
     * @ORM\Column(name="valorcfg", type="string", length=255, nullable=true)
     */
    private $valor;
    
    /******************* GETTERS & SETTERS **************************/
    
	public function getClave() {
		return $this->clave;
	}
	public function setClave($clave) {
		$this->clave = $clave;
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
