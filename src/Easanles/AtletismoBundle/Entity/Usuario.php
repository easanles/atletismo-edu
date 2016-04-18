<?php

namespace Easanles\AtletismoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Config
 *
 * @ORM\Table(name="usu")
 * @ORM\Entity(repositoryClass="Easanles\AtletismoBundle\Entity\Repository\UsuarioRepository")
 */
class Usuario implements UserInterface, \Serializable {
	
    /**
     * @var string
     * @ORM\Column(name="nombreusu", type="string", length=255)
     * @ORM\Id
     */
    private $nombre;
    
    /**
     * @var string
     * @ORM\Column(name="contrausu", type="string", length=255)
     */
    private $contra;
    
    /**
     * @var string
     * @ORM\Column(name="rolusu", type="string", length=255)
     */
    private $rol;
    
    /**
     * @var integer
     * @ORM\OneToOne(targetEntity="Atleta", inversedBy="nombreUsu")
     * @ORM\JoinColumn(name="idatl", referencedColumnName="idatl", onDelete="SET NULL")
     */
    private $idAtl;
    
    /******************* GETTERS & SETTERS **************************/
    
	public function getNombre() {
		return $this->nombre;
	}
	public function setNombre($nombre) {
		$this->nombre = $nombre;
		return $this;
	}
	public function getContra() {
		return $this->contra;
	}
	public function setContra($contra) {
		$this->contra = $contra;
		return $this;
	}
	public function getRol() {
		return $this->rol;
	}
	public function setRol($rol) {
		$this->rol = $rol;
		return $this;
	}
	public function getIdAtl() {
		return $this->idAtl;
	}
	public function setIdAtl($idAtl) {
		$this->idAtl = $idAtl;
		return $this;
	}
	
	/************************ SEGURIDAD *****************************/
	
	
	
	/* (non-PHPdoc)
	 * @see \Symfony\Component\Security\Core\User\UserInterface::getRoles()
	 */
	public function getRoles() {
      switch ($this->rol){
      	case "coordinador": return array("ROLE_ADMIN"); break;
      	case "socio": return array("ROLE_USER"); break;
      	default: return array(); break;
      }
	}

	/* (non-PHPdoc)
	 * @see \Symfony\Component\Security\Core\User\UserInterface::getPassword()
	 */
	public function getPassword() {
      return $this->contra;
	}

	/* (non-PHPdoc)
	 * @see \Symfony\Component\Security\Core\User\UserInterface::getSalt()
	 */
	public function getSalt() {
      return null;
	}

	/* (non-PHPdoc)
	 * @see \Symfony\Component\Security\Core\User\UserInterface::getUsername()
	 */
	public function getUsername() {
      return $this->nombre;
	}

	/* (non-PHPdoc)
	 * @see \Symfony\Component\Security\Core\User\UserInterface::eraseCredentials()
	 */
	public function eraseCredentials() {
	}
	
	/** @see \Serializable::serialize() */
	public function serialize(){
		return serialize(array(
				$this->nombre,
				$this->contra,
		));
	}
	
	/** @see \Serializable::unserialize() */
	public function unserialize($serialized){
		list (
				$this->nombre,
				$this->contra,
		) = unserialize($serialized);
	}

}
