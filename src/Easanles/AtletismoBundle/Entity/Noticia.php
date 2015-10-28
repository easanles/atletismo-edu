<?php

namespace Easanles\AtletismoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Noticia
 *
 * ORM\Table(name="not")
 * ORM\Entity
 */
class Noticia
{
    /**
     * @var integer
     * @ORM\Column(name="idnot", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var string
     * @ORM\Column(name="titulonot", type="string", length=255)
     */
    private $titulo;
    
    /**
     * @var string
     * @ORM\Column(name="textonot", type="text")
     */
    private $texto;
    
    /**
     * @var \DateTime
     * @ORM\Column(name="fechanot", type="datetime")
     */
    private $fecha;
    
    /**
     * @var string
     * @ORM\Column(name="autornot", type="string", length=255)
     */
    private $autor;
    
    /**
     * @var string
     * @ORM\Column(name="fotonot", type="string", length=255, nullable=true)
     */
    private $foto;
    
    /**
     * @var string
     * @ORM\Column(name="categorianot", type="string", length=255, nullable=true)
     */
    private $categoria;
    
    /******************* GETTERS & SETTERS **************************/
    
	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->id = $id;
		return $this;
	}
	public function getTitulo() {
		return $this->titulo;
	}
	public function setTitulo($titulo) {
		$this->titulo = $titulo;
		return $this;
	}
	public function getTexto() {
		return $this->texto;
	}
	public function setTexto($texto) {
		$this->texto = $texto;
		return $this;
	}
	public function getFecha() {
		return $this->fecha;
	}
	public function setFecha(\DateTime $fecha) {
		$this->fecha = $fecha;
		return $this;
	}
	public function getAutor() {
		return $this->autor;
	}
	public function setAutor($autor) {
		$this->autor = $autor;
		return $this;
	}
	public function getFoto() {
		return $this->foto;
	}
	public function setFoto($foto) {
		$this->foto = $foto;
		return $this;
	}
	public function getCategoria() {
		return $this->categoria;
	}
	public function setCategoria($categoria) {
		$this->categoria = $categoria;
		return $this;
	}
	

}
