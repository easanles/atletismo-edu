<?php

namespace Easanles\AtletismoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Easanles\AtletismoBundle\Helpers\Helpers;

/**
 * Atleta
 *
 * @ORM\Table(name="atl")
 * @ORM\Entity(repositoryClass="Easanles\AtletismoBundle\Entity\Repository\AtletaRepository")
 * @Vich\Uploadable
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
     * @var File
     * @Vich\UploadableField(mapping="atleta_image", fileNameProperty="foto")
     */
    private $fotoFile;
    
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;
    
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
     * @ORM\OneToMany(targetEntity="Participacion", mappedBy="idAtl", cascade={"all"})
     **/
    private $participaciones;
    
    /**
     * @var array_collection
     * @ORM\OneToMany(targetEntity="Inscripcion", mappedBy="idAtl", cascade={"all"})
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
	
	//*******VICH**********
	public function getUpdatedAt() {
		return $this->updatedAt;
	}
	public function setUpdatedAt($updatedAt) {
		$this->updatedAt = $updatedAt;
		return $this;
	}
	public function setFotoFile(File $image = null){
		$this->fotoFile = $image;
		if ($image) {
			$this->updatedAt = new \DateTime('now');
		}
	}
	/**
	 * @return File
	 */
	public function getFotoFile() {
		return $this->fotoFile;
	}
	
	
	/********************** VALIDACION ***********************/
	
	/**
	 * @var string
	 */
	private $warn_dni;
	
	/**
	 * @var string
	 */
	private $warn_nick;
	
	public function getWarnDni() {
		return $this->warn_dni;
	}
	public function setWarnDni($warn_dni) {
		$this->warn_dni = $warn_dni;
		return $this;
	}
	public function getWarnNick() {
		return $this->warn_nick;
	}
	public function setWarnNick($warn_nick) {
		$this->warn_nick = $warn_nick;
		return $this;
	}
	
	/**
	 * @Assert\Callback
	 */
	public function validate(ExecutionContextInterface $context) {
		$this->dni = strtoupper($this->dni);
		$this->lfga = strtoupper($this->lfga);
		$this->lxogade = strtoupper($this->lxogade);
		if ($this->getLxogade() != null){
			$edad = Helpers::getEdad($this->getFnac());
			if (($edad < 6) || ($edad > 16)) {
				$context->buildViolation("Solo personas de entre 6 y 16 años pueden tener licencia XOGADE")
				->atPath('lxogade')
				->addViolation();
			}
		}
	}

	
    
}
