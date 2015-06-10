<?php

namespace Easanles\AtletismoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Competicion
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Easanles\AtletismoBundle\Entity\CompeticionRepository")
 */
class CompeticionPrev
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="temporada", type="string", length=32)
     */
    private $temporada;

    /**
     * @var integer
     *
     * @ORM\Column(name="tipo", type="integer")
     */
    private $tipo;

    /**
     * @var string
     *
     * @ORM\Column(name="provincia", type="string", length=255)
     */
    private $provincia;

    /**
     * @var boolean
     *
     * @ORM\Column(name="es_federada", type="boolean")
     */
    private $esFederada;

    /**
     * @var boolean
     *
     * @ORM\Column(name="es_oficial", type="boolean")
     */
    private $esOficial;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Competicion
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set temporada
     *
     * @param string $temporada
     * @return Competicion
     */
    public function setTemporada($temporada)
    {
        $this->temporada = $temporada;

        return $this;
    }

    /**
     * Get temporada
     *
     * @return string 
     */
    public function getTemporada()
    {
        return $this->temporada;
    }

    /**
     * Set tipo
     *
     * @param integer $tipo
     * @return Competicion
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return integer 
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set provincia
     *
     * @param string $provincia
     * @return Competicion
     */
    public function setProvincia($provincia)
    {
        $this->provincia = $provincia;

        return $this;
    }

    /**
     * Get provincia
     *
     * @return string 
     */
    public function getProvincia()
    {
        return $this->provincia;
    }

    /**
     * Set esFederada
     *
     * @param boolean $esFederada
     * @return Competicion
     */
    public function setEsFederada($esFederada)
    {
        $this->esFederada = $esFederada;

        return $this;
    }

    /**
     * Get esFederada
     *
     * @return boolean 
     */
    public function getEsFederada()
    {
        return $this->esFederada;
    }

    /**
     * Set esOficial
     *
     * @param boolean $esOficial
     * @return Competicion
     */
    public function setEsOficial($esOficial)
    {
        $this->esOficial = $esOficial;

        return $this;
    }

    /**
     * Get esOficial
     *
     * @return boolean 
     */
    public function getEsOficial()
    {
        return $this->esOficial;
    }
}
