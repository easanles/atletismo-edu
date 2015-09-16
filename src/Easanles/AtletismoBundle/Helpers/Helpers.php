<?php

namespace Easanles\AtletismoBundle\Helpers;

use Easanles\AtletismoBundle\Entity\Competicion;
use Easanles\AtletismoBundle\Entity\TipoPruebaFormato;
use Easanles\AtletismoBundle\Entity\TipoPruebaModalidad;
use Easanles\AtletismoBundle\Entity\Prueba;

class Helpers {
	
	public static function poblarBD($em){
		$com1 = new Competicion();
		$com1->setNombre("Competicion 1")
		->setTemp(2014);
		$em->persist($com1);
		
		$com2 = new Competicion();
		$com2->setNombre("Competicion 2")
		->setTemp(2015);
		$em->persist($com2);
		
		$tprf = new TipoPruebaFormato();
		$tprf->setNombre("100 metros lisos")
		->setUnidades("Segundos")
		->setNumInt(1);
		$em->persist($tprf);
		$em->flush();
		
		$tprm = new TipoPruebaModalidad();
		$tprm->setSidTprf($tprf)
		->setSexo(0)
		->setEntorno("Campo a través");
		$em->persist($tprm);
		
		$tprm = new TipoPruebaModalidad();
		$tprm->setSidTprf($tprf)
		->setSexo(1)
		->setEntorno("Campo a través");
		$em->persist($tprm);
		
		$tprm = new TipoPruebaModalidad();
		$tprm->setSidTprf($tprf)
		->setSexo(0)
		->setEntorno("Ruta");
		$em->persist($tprm);
		
		$tprm = new TipoPruebaModalidad();
		$tprm->setSidTprf($tprf)
		->setSexo(1)
		->setEntorno("Ruta");
		$em->persist($tprm);
		
		$tprf = new TipoPruebaFormato();
		$tprf->setNombre("Salto de altura")
		->setUnidades("Metros")
		->setNumInt("3");
		$em->persist($tprf);
		
		$tprm3 = new TipoPruebaModalidad();
		$tprm3->setSidTprf($tprf)
		->setSexo(0)
		->setEntorno("Cubierto");
		$em->persist($tprm3);
		
		$tprm4 = new TipoPruebaModalidad();
		$tprm4->setSidTprf($tprf)
		->setSexo(1)
		->setEntorno("Cubierto");
		$em->persist($tprm4);
		
		$tprf = new TipoPruebaFormato();
		$tprf->setNombre("Maratón")
		->setUnidades("Segundos")
		->setNumInt(1);
		$em->persist($tprf);
		$em->flush();
		
		$tprm = new TipoPruebaModalidad();
		$tprm->setSidTprf($tprf)
		->setSexo(0)
		->setEntorno("Ruta");
		$em->persist($tprm);
		
		$tprm = new TipoPruebaModalidad();
		$tprm->setSidTprf($tprf)
		->setSexo(1)
		->setEntorno("Ruta");
		$em->persist($tprm);
		
		$tprf = new TipoPruebaFormato();
		$tprf->setNombre("Prueba por puntos")
		->setUnidades("Puntos")
		->setNumInt(1);
		$em->persist($tprf);
		$em->flush();
		
		$tprm = new TipoPruebaModalidad();
		$tprm->setSidTprf($tprf)
		->setSexo(2)
		->setEntorno("Cubierto");
		$em->persist($tprm);
		
		$tprf = new TipoPruebaFormato();
		$tprf->setNombre("200 metros lisos")
		->setUnidades("Segundos")
		->setNumInt(1);
		$em->persist($tprf);
		$em->flush();
		
		$tprm1 = new TipoPruebaModalidad();
		$tprm1->setSidTprf($tprf)
		->setSexo(0)
		->setEntorno("Aire libre");
		$em->persist($tprm1);
		
		$tprm2 = new TipoPruebaModalidad();
		$tprm2->setSidTprf($tprf)
		->setSexo(1)
		->setEntorno("Aire libre");
		$em->persist($tprm2);
		$em->flush();
		
		$pru = new Prueba();
		$pru->setSidCom($com1)
		->setId(1)
		->setSidTprm($tprm1)
		->setIdCat(1)
		->setRonda(1)
		->setNombre("Cuartos de final A");
		$em->persist($pru);
		
		$pru = new Prueba();
		$pru->setSidCom($com1)
		->setId(2)
		->setSidTprm($tprm1)
		->setIdCat(1)
		->setRonda(1)
		->setNombre("Cuartos de final B");
		$em->persist($pru);
		
		$pru = new Prueba();
		$pru->setSidCom($com1)
		->setId(3)
		->setSidTprm($tprm1)
		->setIdCat(1)
		->setRonda(1)
		->setNombre("Cuartos de final C");
		$em->persist($pru);
		
		$pru = new Prueba();
		$pru->setSidCom($com1)
		->setId(4)
		->setSidTprm($tprm1)
		->setIdCat(1)
		->setRonda(1)
		->setNombre("Cuartos de final D");
		$em->persist($pru);
		
		$pru = new Prueba();
		$pru->setSidCom($com1)
		->setId(5)
		->setSidTprm($tprm1)
		->setIdCat(1)
		->setRonda(2)
		->setNombre("Semifinal A");
		$em->persist($pru);
		
		$pru = new Prueba();
		$pru->setSidCom($com1)
		->setId(6)
		->setSidTprm($tprm1)
		->setIdCat(1)
		->setRonda(2)
		->setNombre("Semifinal B");
		$em->persist($pru);
		
		$pru = new Prueba();
		$pru->setSidCom($com1)
		->setId(7)
		->setSidTprm($tprm1)
		->setIdCat(1)
		->setRonda(3)
		->setNombre("Final");
		$em->persist($pru);
		
		$pru = new Prueba();
		$pru->setSidCom($com1)
		->setId(8)
		->setSidTprm($tprm2)
		->setIdCat(1)
		->setRonda(1)
		->setNombre("Semifinal A");
		$em->persist($pru);
		
		$pru = new Prueba();
		$pru->setSidCom($com1)
		->setId(9)
		->setSidTprm($tprm2)
		->setIdCat(1)
		->setRonda(1)
		->setNombre("Semifinal B");
		$em->persist($pru);
		
		$pru = new Prueba();
		$pru->setSidCom($com1)
		->setId(10)
		->setSidTprm($tprm2)
		->setIdCat(1)
		->setRonda(2)
		->setNombre("Final");
		$em->persist($pru);
		
		$pru = new Prueba();
		$pru->setSidCom($com2)
		->setId(1)
		->setSidTprm($tprm1)
		->setIdCat(1)
		->setRonda(1)
		->setNombre("Final");
		$em->persist($pru);
		
		$pru = new Prueba();
		$pru->setSidCom($com2)
		->setId(2)
		->setSidTprm($tprm2)
		->setIdCat(1)
		->setRonda(1)
		->setNombre("Final");
		$em->persist($pru);
	
	   $pru = new Prueba();
		$pru->setSidCom($com2)
		->setId(3)
		->setSidTprm($tprm3)
		->setIdCat(1)
		->setRonda(1)
		->setNombre("Final");
		$em->persist($pru);
	
	   $pru = new Prueba();
		$pru->setSidCom($com2)
		->setId(4)
		->setSidTprm($tprm4)
		->setIdCat(1)
		->setRonda(1)
		->setNombre("Final");
		$em->persist($pru);
		
		$em->flush();
	}

}