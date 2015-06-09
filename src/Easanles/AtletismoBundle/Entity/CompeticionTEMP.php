<?php

namespace Easanles\AtletismoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Competicion
 * 
 * @ORM\Table(name="com")
 * @ORM\Entity(repositoryClass="Easanles\AtletismoBundle\Entity\CompeticionRepository")
 */
class Competicion {

   /**
   * @ORM\Column(name="nombrecom", type="string", length=255)
   */
   private $nombreCom;
   
   /**
    * @ORM\Column(name="tempcom", type="smallint")
    */
   private $tempCom;
   
   /**
    * @ORM\Column(name="ubicacioncom", type="string", length=255, nullable=true)
    */
   private $ubicacionCom;
   
   /**
    * @ORM\Column(name="sedecom", type="string", length=255, nullable=true)
    */
   private $sedeCom;
   
   /**
    * @ORM\Column(name="fechacom", type="date", nullable=true)
    */
   private $fechaCom;
   
   /**
    * @ORM\Column(name="desccom", type="text", nullable=true)
    */
   private $descCom;
   
   /**
    * @ORM\Column(name="nivelcom", type="string", length=255, nullable=true)
    */
   private $nivelCom;
   
   /**
    * @ORM\Column(name="federcom", type="string", length=255, nullable=true)
    */
   private $federCom;
   
   /**
    * @ORM\Column(name="webcom", type="string", length=255, nullable=true)
    */
   private $webCom;
   
   /**
    * @ORM\Column(name="emailcom", type="string", length=255, nullable=true)
    */
   private $emailCom;
   
   /**
    * @ORM\Column(name="cartelcom", type="string", length=255, nullable=true)
    */
   private $cartelCom;
   
   /**
    * @ORM\Column(name="esfedercom", type="boolean", options={"default":0})
    */
   private $esFederCom;
   
   
}