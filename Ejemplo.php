<?php

namespace Easanles\AtletismoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ejemplo
 *
 * @ORM\Table(name="ejemplo")
 * @ORM\Entity(repositoryClass="Easanles\AtletismoBundle\Entity\EjemploRepository")
 */
class Ejemplo
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
     * @var array
     *
     * @ORM\Column(name="array", type="array")
     */
    private $array;

    /**
     * @var boolean
     *
     * @ORM\Column(name="bol", type="boolean")
     */
    private $bol;

    /**
     * @var integer
     *
     * @ORM\Column(name="integ", type="integer")
     */
    private $integ;

    /**
     * @var integer
     *
     * @ORM\Column(name="smallintej", type="smallint")
     */
    private $smallintej;

    /**
     * @var string
     *
     * @ORM\Column(name="string", type="string", length=255)
     */
    private $string;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     */
    private $text;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datetimetzz", type="datetimetz")
     */
    private $datetimetzz;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="daterime", type="datetime")
     */
    private $datetimee;

    /**
     * @var float
     *
     * @ORM\Column(name="floaty", type="float")
     */
    private $floaty;

    /**
     * @var string
     *
     * @ORM\Column(name="decimalto", type="decimal")
     */
    private $decimalto;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateee", type="date")
     */
    private $dateee;

    /**
     * @var guid
     *
     * @ORM\Column(name="guidy", type="guid")
     */
    private $guidy;


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
     * Set array
     *
     * @param array $array
     * @return Ejemplo
     */
    public function setArray($array)
    {
        $this->array = $array;

        return $this;
    }

    /**
     * Get array
     *
     * @return array 
     */
    public function getArray()
    {
        return $this->array;
    }

    /**
     * Set bol
     *
     * @param boolean $bol
     * @return Ejemplo
     */
    public function setBol($bol)
    {
        $this->bol = $bol;

        return $this;
    }

    /**
     * Get bol
     *
     * @return boolean 
     */
    public function getBol()
    {
        return $this->bol;
    }

    /**
     * Set integ
     *
     * @param integer $integ
     * @return Ejemplo
     */
    public function setInteg($integ)
    {
        $this->integ = $integ;

        return $this;
    }

    /**
     * Get integ
     *
     * @return integer 
     */
    public function getInteg()
    {
        return $this->integ;
    }

    /**
     * Set smallintej
     *
     * @param integer $smallintej
     * @return Ejemplo
     */
    public function setSmallintej($smallintej)
    {
        $this->smallintej = $smallintej;

        return $this;
    }

    /**
     * Get smallintej
     *
     * @return integer 
     */
    public function getSmallintej()
    {
        return $this->smallintej;
    }

    /**
     * Set string
     *
     * @param string $string
     * @return Ejemplo
     */
    public function setString($string)
    {
        $this->string = $string;

        return $this;
    }

    /**
     * Get string
     *
     * @return string 
     */
    public function getString()
    {
        return $this->string;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Ejemplo
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set datetimetzz
     *
     * @param \DateTime $datetime
     * @return Ejemplo
     */
    public function setDatetimetzz($datetimetzz)
    {
        $this->datetimetzz = $datetimetzz;

        return $this;
    }

    /**
     * Get datetimetzz
     *
     * @return \DateTime 
     */
    public function getDatetimetzz()
    {
        return $this->datetimetzz;
    }

    /**
     * Set daterime
     *
     * @param \DateTime $daterime
     * @return Ejemplo
     */
    public function setDateTimee($datetimee)
    {
        $this->datetimee = $datetimee;

        return $this;
    }

    /**
     * Get datetimee
     *
     * @return \DateTime 
     */
    public function getDateTimee()
    {
        return $this->datetimee;
    }

    /**
     * Set floaty
     *
     * @param float $floaty
     * @return Ejemplo
     */
    public function setFloaty($floaty)
    {
        $this->floaty = $floaty;

        return $this;
    }

    /**
     * Get floaty
     *
     * @return float 
     */
    public function getFloaty()
    {
        return $this->floaty;
    }

    /**
     * Set decimalto
     *
     * @param string $decimalto
     * @return Ejemplo
     */
    public function setDecimalto($decimalto)
    {
        $this->decimalto = $decimalto;

        return $this;
    }

    /**
     * Get decimalto
     *
     * @return string 
     */
    public function getDecimalto()
    {
        return $this->decimalto;
    }

    /**
     * Set dateee
     *
     * @param \DateTime $dateee
     * @return Ejemplo
     */
    public function setDateee($dateee)
    {
        $this->dateee = $dateee;

        return $this;
    }

    /**
     * Get dateee
     *
     * @return \DateTime 
     */
    public function getDateee()
    {
        return $this->dateee;
    }

    /**
     * Set guidy
     *
     * @param guid $guidy
     * @return Ejemplo
     */
    public function setGuidy($guidy)
    {
        $this->guidy = $guidy;

        return $this;
    }

    /**
     * Get guidy
     *
     * @return guid 
     */
    public function getGuidy()
    {
        return $this->guidy;
    }
}
