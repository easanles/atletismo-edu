<?php

namespace Easanles\AtletismoBundle\Helpers;

use Easanles\AtletismoBundle\Entity\Competicion;
use Easanles\AtletismoBundle\Entity\TipoPruebaFormato;
use Easanles\AtletismoBundle\Entity\TipoPruebaModalidad;
use Easanles\AtletismoBundle\Entity\Prueba;
use Easanles\AtletismoBundle\Entity\Categoria;
use Easanles\AtletismoBundle\Entity\Config;
use Doctrine\ORM\EntityManager;
use Easanles\AtletismoBundle\Entity\Atleta;

class Helpers {
	
	/**
	 * Comprueba si el string recibido como fecha sigue el formato dd/mm
	 * @param string $date Cadena de texto indicando dia y fecha 
	 * @return boolean true si sigue el formato dd/mm
	 */
	public static function checkDayMonth($date){
		return \preg_match("/^\s*\d?\d\s*\/\s*\d\d\s*$/", $date);
	}
	
   /**
    * Dado un dia y un mes, obtiene la temporada a la que pertenece esa fecha
    * @param $doctrine El servicio Doctrine
    * @param int $dia Dia del año en forma numérica
    * @param int $mes Mes del año en forma numérica
    * @param int $ano Año, null si es año actual
    * @return int El año de inicio de la temporada
    */
	public static function getTempYear($doctrine, $dia, $mes, $ano){
    	if ($ano == null){
    		$now = new \DateTime();
    		$ano = $now->format("Y");
    	}
    	$repo = $doctrine->getRepository('EasanlesAtletismoBundle:Config');
    	$fIniTempObj = $repo->findOneBy(array("clave" => "fIniTemp"));
    	if ($fIniTempObj == null) return $ano; //Como si comenzase el 1 de enero
    	else {
    		$fIniTempVal = $fIniTempObj->getValor();
    		$datos = explode("/", $fIniTempVal);
    		if (($mes >= $datos[1]) && ($dia >= $datos[0]))
    			   return $ano;
    		else return $ano - 1;
    	}
	}
	
	/**
	 * Obtiene la edad con respecto a un dia concreto
	 * @param \DateTime $fnac La fecha de nacimiento
	 * @param \DateTime $fecha La fecha de referencia a comparar, null para edad actual a dia de hoy
	 * @return int Edad del atleta a dia $fecha
	 */
	public static function getEdad($fnac, $fecha){
		if ($fecha == null) {
			$fecha =  new \DateTime();
		}	
		$interval = $fecha->diff($fnac);
		return $interval->y;
	}
	
	/**
	 * Obtiene la fecha a partir de la cual se asignan las categorias a los atletas. Depende de los valores de configuracion catAsig y fIniTemp
	 * @param $doctrine El servicio Doctrine
	 * @return La fecha en la que se asignan las categorias de la temporada actual
	 */
	public static function getFechaRefCat($doctrine){
		$repoCfg = $doctrine->getRepository('EasanlesAtletismoBundle:Config');
		$catAsig = $repoCfg->findOneBy(array("clave" => "catAsig"));
		$now = new \DateTime();
		if ($catAsig->getValor() == "0") {
			$fIniTemp = $repoCfg->findOneBy(array("clave" => "fIniTemp"));
			$datos = explode("/", $fIniTemp->getValor());
			$ano = Helpers::getTempYear($doctrine, $now->format("d"), $now->format("m"), null);
			return new \DateTime($datos[0]."/".$datos[1]."/".$ano);
		} else return $now;
	}
	
	/**
	 * Dado un array de categorias obtener a cual pertenece un atleta
	 * @param $categorias La lista de categorias vigentes ordenado de menor a mayor edad maxima terminando en la categoria sin edad maxima si la hubiese
	 * @param \DateTime $fechaRefCat La fecha de referencia para comparar fecha de nacimiento
	 * @param \DateTime $fnac La fecha de nacimiento del atleta
	 * @return Categoria La categoria a la que pertenece el atleta
	 */
	public static function getCategoria($categorias, $fechaRefCat, $fnac){
		foreach($categorias as $cat){
		   if ($cat['edadMax'] != null){
				if (Helpers::getEdad($fnac, $fechaRefCat) <= $cat['edadMax']){
				   return $cat;
			   }
		   } else return $cat;
		}
	}
	
	/**
	 * Obtiene la fecha de inicio en la que un atleta debe haber nacido para pertenecer a esta categoría
	 * @param $doctrine El servicio Doctrine
	 * @param Categoria $cat La categoría a comprobar
	 * @return La fecha inicial de la categoria, año 0 si la edad máxima es null
	 */
	public static function getCatIniDate($doctrine, $cat){
		$edadMax = $cat->getEdadMax();
		if ($edadMax == null){
			return new \DateTime("0000-01-01");
		} else {
			$fecha = Helpers::getFechaRefCat($doctrine);
			return $fecha->sub(new \DateInterval("P".($edadMax+1)."Y1D"))
			             ->add(new \DateInterval("P1D"));
		}
	}
	
	/**
	 * Obtiene la fecha de fin en la que un atleta debe haber nacido para pertenecer a esta categoría
	 * @param $doctrine El servicio Doctrine
	 * @param Categoria $cat La categoría a comprobar
	 * @return La fecha final de la categoría
	 */
	public static function getCatFinDate($doctrine, $cat){
		$repo = $doctrine->getRepository('EasanlesAtletismoBundle:Categoria');
		$prevCat = $repo->findPreviousCat($cat);
		$fecha = Helpers::getFechaRefCat($doctrine);
		if ($prevCat == null){
			return $fecha;
		} else {
			$edadMax = $prevCat['edadMax'];
			return $fecha->sub(new \DateInterval("P".($edadMax+1)."Y"));
		}
	}
	

	/**
	 * Ajusta los valores iniciales de la base de datos
	 * @param EntityManager $em El EntityManager
	 */
	public static function defaultBDValues($em){
		$fIniTemp = new Config();
		$fIniTemp->setClave("fIniTemp")->setValor("01/11");
		$em->persist($fIniTemp);
		$catAsig = new Config();
		$catAsig->setClave("catAsig")->setValor("0"); //Asignacion al inicio de la temporada
		$em->persist($catAsig);
		$em->flush();
	}
	
	/**
	 * Llena la base de datos con datos de ejemplo para realizar pruebas
	 * @param $em El Entity Manager
	 */
	public static function poblarBD($em){
		$com1 = new Competicion();
		$com1->setNombre("Competicion 1")
		->setTemp(2014)
		->setCartel("ejemplo.jpg");
		$em->persist($com1);
		
		$com2 = new Competicion();
		$com2->setNombre("Competicion 2")
		->setTemp(2015)
		->setCartel("ejemplo.jpg");
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
		
		$cat = new Categoria();
		$cat->setNombre("Prebenjamín")
		->setEdadMax(7)
		->setTIniVal(2012)
		->setTFinVal(2014);
		$em->persist($cat);
		
		$cat = new Categoria();
		$cat->setNombre("Prebenjamín")
		->setEdadMax(6)
		->setTIniVal(2015)
		->setTFinVal(null);
		$em->persist($cat);
		
		$cat = new Categoria();
		$cat->setNombre("Benjamín")
		->setEdadMax(8)
		->setTIniVal(2015)
		->setTFinVal(null);
		$em->persist($cat);
		
		$cat = new Categoria();
		$cat->setNombre("Alevín")
		->setEdadMax(10)
		->setTIniVal(2015)
		->setTFinVal(null);
		$em->persist($cat);
		
		$cat = new Categoria();
		$cat->setNombre("Infantil")
		->setEdadMax(12)
		->setTIniVal(2015)
		->setTFinVal(null);
		$em->persist($cat);
		
		$cat = new Categoria();
		$cat->setNombre("Cadete")
		->setEdadMax(14)
		->setTIniVal(2015)
		->setTFinVal(null);
		$em->persist($cat);
		
		$cat = new Categoria();
		$cat->setNombre("Juvenil")
		->setEdadMax(16)
		->setTIniVal(2015)
		->setTFinVal(null);
		$em->persist($cat);
		
		$cat1 = new Categoria();
		$cat1->setNombre("Junior")
		->setEdadMax(18)
		->setTIniVal(2015)
		->setTFinVal(null);
		$em->persist($cat1);
		
		$cat = new Categoria();
		$cat->setNombre("Promesa")
		->setEdadMax(21)
		->setTIniVal(2015)
		->setTFinVal(null);
		$em->persist($cat);
		
		$cat = new Categoria();
		$cat->setNombre("Senior")
		->setEdadMax(34)
		->setTIniVal(2015)
		->setTFinVal(null);
		$em->persist($cat);
		
		$cat = new Categoria();
		$cat->setNombre("Veteranos")
		->setEdadMax(null)
		->setTIniVal(2015)
		->setTFinVal(null);
		$em->persist($cat);
		$em->flush();
		
		$pru = new Prueba();
		$pru->setSidCom($com1)
		->setId(1)
		->setSidTprm($tprm1)
		->setIdCat($cat)
		->setRonda(1)
		->setNombre("Cuartos de final A");
		$em->persist($pru);
		
		$pru = new Prueba();
		$pru->setSidCom($com1)
		->setId(2)
		->setSidTprm($tprm1)
		->setIdCat($cat)
		->setRonda(1)
		->setNombre("Cuartos de final B");
		$em->persist($pru);
		
		$pru = new Prueba();
		$pru->setSidCom($com1)
		->setId(3)
		->setSidTprm($tprm1)
		->setIdCat($cat)
		->setRonda(1)
		->setNombre("Cuartos de final C");
		$em->persist($pru);
		
		$pru = new Prueba();
		$pru->setSidCom($com1)
		->setId(4)
		->setSidTprm($tprm1)
		->setIdCat($cat)
		->setRonda(1)
		->setNombre("Cuartos de final D");
		$em->persist($pru);
		
		$pru = new Prueba();
		$pru->setSidCom($com1)
		->setId(5)
		->setSidTprm($tprm1)
		->setIdCat($cat)
		->setRonda(2)
		->setNombre("Semifinal A");
		$em->persist($pru);
		
		$pru = new Prueba();
		$pru->setSidCom($com1)
		->setId(6)
		->setSidTprm($tprm1)
		->setIdCat($cat)
		->setRonda(2)
		->setNombre("Semifinal B");
		$em->persist($pru);
		
		$pru = new Prueba();
		$pru->setSidCom($com1)
		->setId(7)
		->setSidTprm($tprm1)
		->setIdCat($cat)
		->setRonda(3)
		->setNombre("Final");
		$em->persist($pru);
		
		$pru = new Prueba();
		$pru->setSidCom($com1)
		->setId(8)
		->setSidTprm($tprm2)
		->setIdCat($cat1)
		->setRonda(1)
		->setNombre("Semifinal A");
		$em->persist($pru);
		
		$pru = new Prueba();
		$pru->setSidCom($com1)
		->setId(9)
		->setSidTprm($tprm2)
		->setIdCat($cat1)
		->setRonda(1)
		->setNombre("Semifinal B");
		$em->persist($pru);
		
		$pru = new Prueba();
		$pru->setSidCom($com1)
		->setId(10)
		->setSidTprm($tprm2)
		->setIdCat($cat1)
		->setRonda(2)
		->setNombre("Final");
		$em->persist($pru);
		
		$pru = new Prueba();
		$pru->setSidCom($com2)
		->setId(1)
		->setSidTprm($tprm1)
		->setIdCat($cat)
		->setRonda(1)
		->setNombre("Final");
		$em->persist($pru);
		
		$pru = new Prueba();
		$pru->setSidCom($com2)
		->setId(2)
		->setSidTprm($tprm2)
		->setIdCat($cat)
		->setRonda(1)
		->setNombre("Final");
		$em->persist($pru);
	
	   $pru = new Prueba();
		$pru->setSidCom($com2)
		->setId(3)
		->setSidTprm($tprm3)
		->setIdCat($cat)
		->setRonda(1)
		->setNombre("Final");
		$em->persist($pru);
	
	   $pru = new Prueba();
		$pru->setSidCom($com2)
		->setId(4)
		->setSidTprm($tprm4)
		->setIdCat($cat)
		->setRonda(1)
		->setNombre("Final");
		$em->persist($pru);
		
		$atl = new Atleta();
		$atl->setNombre("Nombre1")
		->setApellidos("ApellidoA ApellidoB")
		->setSexo(false)
		->setFnac(new \DateTime("1990/07/22"))
		->setNick("Nick1")
		->setDni("1234567A")
		->setLfga("AG-1234567")
		->setLxogade("ABC123456");
		$em->persist($atl);
		
		$atl = new Atleta();
		$atl->setNombre("Nombre2")
		->setApellidos("ApellidoA ApellidoB")
		->setSexo(false)
		->setFnac(new \DateTime("1988/07/22"))
		->setNick("Nick2")
		->setDni("1234568B")
		->setLfga("AG-1234568")
		->setLxogade("ABC123457");
		$em->persist($atl);
		
		$atl = new Atleta();
		$atl->setNombre("Nombre3")
		->setApellidos("ApellidoA ApellidoB")
		->setSexo(true)
		->setFnac(new \DateTime("1992/07/22"))
		->setNick("Nick3")
		->setDni("1234569C")
		->setLfga("AG-1234569")
		->setLxogade("ABC123458");
		$em->persist($atl);
		
		$atl = new Atleta();
		$atl->setNombre("Nombre4")
		->setApellidos("ApellidoA ApellidoB")
		->setSexo(false)
		->setFnac(new \DateTime("1994/07/22"))
		->setNick("Nick4")
		->setDni("1234242C")
		->setLfga("AG-1233426")
		->setLxogade("ABC123454");
		$em->persist($atl);
		
		$atl = new Atleta();
		$atl->setNombre("Nombre5")
		->setApellidos("ApellidoA ApellidoB")
		->setSexo(true)
		->setFnac(new \DateTime("1996/07/22"))
		->setNick("Nick5")
		->setDni("1234560G")
		->setLfga("AG-1764567")
		->setLxogade("ABC124556");
		$em->persist($atl);
		
		$atl = new Atleta();
		$atl->setNombre("Nombre6")
		->setApellidos("ApellidoA ApellidoB")
		->setSexo(false)
		->setFnac(new \DateTime("1976/12/01"))
		->setNick("Nick6")
		->setDni("5234567A")
		->setLfga("AG-4234567");
		$em->persist($atl);
		
		$atl = new Atleta();
		$atl->setNombre("Nombre7")
		->setApellidos("ApellidoA ApellidoB")
		->setSexo(true)
		->setFnac(new \DateTime("2006/07/22"))
		->setDni("9234567A");
		$em->persist($atl);
		
		$atl = new Atleta();
		$atl->setNombre("Nombre8")
		->setApellidos("ApellidoA ApellidoB")
		->setSexo(false)
		->setFnac(new \DateTime("2012/07/22"))
		->setDni("9999999Z");
		$em->persist($atl);
		
		$em->flush();
	}

}