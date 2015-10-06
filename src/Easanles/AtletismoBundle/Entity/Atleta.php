<?php

namespace Easanles\AtletismoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Atleta
 *
 * @ORM\Table(name="atl")
 * @ORM\Entity(repositoryClass="Easanles\AtletismoBundle\Entity\Repository\AtletaRepository")
 */
class Atleta
{
    /**
     * @var integer
     * @ORM\Column(name="idatl", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var string
     * @ORM\Column(name="nombreatl", type="string", length=255)
     */
    private $nombre;
    
    /**
     * @var string
     * @ORM\Column(name="apellidosatl", type="string", length=255)
     */
    private $apellidos;
    
    /**
     * @var string
     * @ORM\Column(name="nickatl", type="string", length=255, nullable=true)
     */
    private $nick;
    
    /**
     * @var string
     * @ORM\Column(name="dniatl", type="string", length=32, nullable=true)
     */
    private $dni;
    
    /**
     * @var \DateTime
     * @ORM\Column(name="fnacatl", type="date")
     */
    private $fnac;
    
    /**
     * @var boolean
     * @ORM\Column(name="sexoatl", type="boolean")
     */
    private $sexo;
    
    /**
     * @var string
     * @ORM\Column(name="bloqueatl", type="string", length=255, nullable=true)
     */
    private $bloque;
    
    /**
     * @var string
     * @ORM\Column(name="direccionatl", type="string", length=255, nullable=true)
     */
    private $direccion;
    
    /**
     * @var string
     * @ORM\Column(name="cpatl", type="string", length=255, nullable=true)
     */
    private $cp;
    
    /**
     * @var string
     * @ORM\Column(name="localidadatl", type="string", length=255, nullable=true)
     */
    private $localidad;
    
    /**
     * @var string
     * @ORM\Column(name="provinciaatl", type="string", length=255, nullable=true)
     */
    private $provincia;
    
    /**
     * @var string
     * @ORM\Column(name="paisatl", type="string", length=255, nullable=true)
     */
    private $pais;
    
    /**
     * @var string
     * @ORM\Column(name="nacionatl", type="string", length=4, nullable=true)
     */
    private $nacion;
    
    /**
     * @var string
     * @ORM\Column(name="lfgaatl", type="string", length=16, nullable=true)
     */
    private $lfga;
    
    /**
     * @var string
     * @ORM\Column(name="lxogadeatl", type="string", length=16, nullable=true)
     */
    private $lxogade;
    
    /**
     * @var string
     * @ORM\Column(name="fotoatl", type="string", length=255, nullable=true)
     */
    private $foto;
    
    /**
     * @var string
     * @ORM\Column(name="notasatl", type="text", nullable=true)
     */
    private $notas;
    
    /**
     * @var string
     * @ORM\Column(name="emailatl", type="string", length=255, nullable=true)
     */
    private $email;
    

    /********************* FOREIGN KEYS *****************************/
      
    /**
     * @var array_collection
     * ORM\OneToMany(targetEntity="Participacion", mappedBy="sid", cascade={"all"})
     **/
    private $participaciones;
    
    /**
     * @var array_collection
     * ORM\OneToMany(targetEntity="Inscripcion", mappedBy="sid", cascade={"all"})
     **/
    private $inscripciones;
    
    /**
     * @var array_collection
     * ORM\OneToMany(targetEntity="Intento", mappedBy="sid", cascade={"all"})
     **/
    private $intentos;
     
    public function __construct() {
    	$this->participaciones = new ArrayCollection();
    	$this->inscripciones = new ArrayCollection();
    	$this->intentos = new ArrayCollection();
    }
     
    public function getParticipaciones() {
    	return $this->participaciones;
    }
    
    public function getInscripciones() {
    	return $this->inscripciones;
    }
    
    public function getIntentos() {
    	return $this->intentos;
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
	public function getApellidos() {
		return $this->apellidos;
	}
	public function setApellidos($apellidos) {
		$this->apellidos = $apellidos;
		return $this;
	}
	public function getNick() {
		return $this->nick;
	}
	public function setNick($nick) {
		$this->nick = $nick;
		return $this;
	}
	public function getDni() {
		return $this->dni;
	}
	public function setDni($dni) {
		$this->dni = $dni;
		return $this;
	}
	public function getFnac() {
		return $this->fnac;
	}
	public function setFnac(\DateTime $fnac) {
		$this->fnac = $fnac;
		return $this;
	}
	public function getSexo() {
		return $this->sexo;
	}
	public function setSexo($sexo) {
		$this->sexo = $sexo;
		return $this;
	}
	public function getBloque() {
		return $this->bloque;
	}
	public function setBloque($bloque) {
		$this->bloque = $bloque;
		return $this;
	}
	public function getDireccion() {
		return $this->direccion;
	}
	public function setDireccion($direccion) {
		$this->direccion = $direccion;
		return $this;
	}
	public function getCp() {
		return $this->cp;
	}
	public function setCp($cp) {
		$this->cp = $cp;
		return $this;
	}
	public function getLocalidad() {
		return $this->localidad;
	}
	public function setLocalidad($localidad) {
		$this->localidad = $localidad;
		return $this;
	}
	public function getProvincia() {
		return $this->provincia;
	}
	public function setProvincia($provincia) {
		$this->provincia = $provincia;
		return $this;
	}
	public function getPais() {
		return $this->pais;
	}
	public function setPais($pais) {
		$this->pais = $pais;
		return $this;
	}
	public function getNacion() {
		return $this->nacion;
	}
	public function setNacion($nacion) {
		$this->nacion = $nacion;
		return $this;
	}
	public function getLfga() {
		return $this->lfga;
	}
	public function setLfga($lfga) {
		$this->lfga = $lfga;
		return $this;
	}
	public function getLxogade() {
		return $this->lxogade;
	}
	public function setLxogade($lxogade) {
		$this->lxogade = $lxogade;
		return $this;
	}
	public function getFoto() {
		return $this->foto;
	}
	public function setFoto($foto) {
		$this->foto = $foto;
		return $this;
	}
	public function getNotas() {
		return $this->notas;
	}
	public function setNotas($notas) {
		$this->notas = $notas;
		return $this;
	}
	public function getEmail() {
		return $this->email;
	}
	public function setEmail($email) {
		$this->email = $email;
		return $this;
	}
    
}
