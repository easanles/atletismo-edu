<?php

# Copyright (c) 2016 Eduardo Alonso Sanlés
# Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php

namespace Easanles\AtletismoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;


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
	 */
	private $sid;
	
	 /**
	 * @ORM\ManyToOne(targetEntity="TipoPruebaFormato", inversedBy="modalidades")
	 * @ORM\JoinColumn(name="sidtprf", referencedColumnName="sidtprf")
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
	  * @ORM\OneToMany(targetEntity="Prueba", mappedBy="sidTprm", cascade={"all"})
	  **/
	 private $pruebas;
	 
	 public function __construct() {
	 	$this->pruebas = new ArrayCollection();
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

	/**
	 * @Assert\Callback
	 */
	public function validate(ExecutionContextInterface $context) {
		$this->entorno = strip_tags($this->entorno);
		if (($this->sexo < 0) || ($this->sexo > 2)) {
			$this->sexo = 2;
		}
	}
	 
}
