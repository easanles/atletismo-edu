<?php

# Copyright (c) 2016 Eduardo Alonso Sanlés
# Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php

namespace Easanles\AtletismoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Categoria
 *
 * @ORM\Table(name="cat")
 * @ORM\Entity(repositoryClass="Easanles\AtletismoBundle\Entity\Repository\CategoriaRepository")
 */
class Categoria
{
    /**
     * @var integer
     * @ORM\Column(name="idcat", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var string
     * @ORM\Column(name="nombrecat", type="string", length=255)
     */
    private $nombre;
    
    /**
     * @var integer
     * @ORM\Column(name="edadmaxcat", type="smallint", nullable=true)
     */
    private $edadMax;
    
    /**
     * @var integer
     * @ORM\Column(name="tinivalcat", type="smallint")
     */
    private $tIniVal;
    
    /**
     * @var integer
     * @ORM\Column(name="tfinvalcat", type="smallint", nullable=true)
     */
    private $tFinVal;
    
    /**
     * @var boolean
     * @ORM\Column(name="estodoscat", type="boolean", options={"default":0})
     */
    private $esTodos;
    
    /********************* FOREIGN KEYS *****************************/
      
    /**
     * @var array_collection
     * @ORM\OneToMany(targetEntity="Prueba", mappedBy="idCat", cascade={"all"})
     **/
    private $pruebas;
      
    public function __construct() {
    	 $this->esTodos = false;
       $this->pruebas = new ArrayCollection();
    }
     
    public function getPruebas() {
       return $this->pruebas;
    }
    
    /******************* GETTERS & SETTERS **************************/
    
	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->id = $id;
		return $this;
	}
	public function getNombre() {
		return $this->nombre;
	}
	public function setNombre($nombre) {
		$this->nombre = $nombre;
		return $this;
	}
	public function getEdadMax() {
		return $this->edadMax;
	}
	public function setEdadMax($edadMax) {
		$this->edadMax = $edadMax;
		return $this;
	}
	public function getTIniVal() {
		return $this->tIniVal;
	}
	public function setTIniVal($tIniVal) {
		$this->tIniVal = $tIniVal;
		return $this;
	}
	public function getTFinVal() {
		return $this->tFinVal;
	}
	public function setTFinVal($tFinVal) {
		$this->tFinVal = $tFinVal;
		return $this;
	}
	public function getEsTodos() {
		return $this->esTodos;
	}
	public function setEsTodos($esTodos) {
		$this->esTodos = $esTodos;
		return $this;
	}
		
	/********************** VALIDACION ***********************/
	
	/**
	 * @Assert\Callback
	 */
	public function validate(ExecutionContextInterface $context) {
		if ($this->edadMax == "") $this->edadMax = null;
		if ($this->tFinVal != null){
	   	if ($this->tFinVal < $this->tIniVal) {
		   	$context->buildViolation("La temporada final de validez es menor que la inicial")
			   ->atPath('tFinVal')
			   ->addViolation();
		   }
		}
	}
    
}
