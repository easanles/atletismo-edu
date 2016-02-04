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
use Easanles\AtletismoBundle\Entity\Inscripcion;
use Easanles\AtletismoBundle\Entity\Participacion;
use Easanles\AtletismoBundle\Entity\Ronda;
use Easanles\AtletismoBundle\Entity\Intento;
use Easanles\AtletismoBundle\Entity\Usuario;

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
		switch ($catAsig->getValor()){
			case "temp": {
				$fIniTemp = $repoCfg->findOneBy(array("clave" => "fIniTemp"));
				$datos = explode("/", $fIniTemp->getValor());
				$ano = Helpers::getTempYear($doctrine, $now->format("d"), $now->format("m"), null);
				return new \DateTime($datos[0]."-".$datos[1]."-".$ano);
			} break;
			case "year": {
				return new \DateTime("31-12-".$now->sub(new \DateInterval("P1Y"))->format('Y'));
			} break;
			case "daily": {
				return $now;
			} break;
			default: {
				return $now;
			} break;
		}
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
		$catAsig->setClave("catAsig")->setValor("year"); //Asignacion al inicio del año
		$em->persist($catAsig);
		$defaultUsu = new Usuario();
		$defaultUsu->setNombre("admin") //Al menos un administrador. Borrar o cambiar clave despues.
		   ->setContra('$2a$04$DhlYDQ.4c1e7E8HUQLGxReVc0Ug7OhqNoknBPa1kIw02G4TP8cfn.')
		   ->setRol("coordinador")
		   ->setIdAtl(null);
		$em->persist($defaultUsu);
		$em->flush();
	}
	
	/**
	 * Obtiene la lista de pruebas de una competición agrupando por tipo de prueba y categoría
	 * @param unknown $doctrine El servicio Doctrine
	 * @param unknown $sidCom El código identificador de la competición
	 */
	public static function obtenerPruebas($doctrine, $sidCom){
		$repoPru = $doctrine->getRepository('EasanlesAtletismoBundle:Prueba');
		$repoTprm = $doctrine->getRepository('EasanlesAtletismoBundle:TipoPruebaModalidad');
		$repoCat = $doctrine->getRepository('EasanlesAtletismoBundle:Categoria');
		$prus = $repoPru->findAllOrderedFor($sidCom);
		$result = array();
		$cats = array();
		$currentTprm = null;
		foreach($prus as $pru){
			if ($currentTprm == null){
				$currentTprm = $pru['tprm'];
			} else if ($pru['tprm'] != $currentTprm){
				$tprm = $repoTprm->find($currentTprm);
				if ($tprm->getSexo() == 0) $sexo = ", masculino";
				else if ($tprm->getSexo() == 1) $sexo = ", femenino";
				else $sexo = "";
				$nombre = $tprm->getSidTprf()->getNombre().$sexo.". ".$tprm->getEntorno();
				$result[] = array("tprm" => $nombre, "cats" => $cats);
				$currentTprm = $pru['tprm'];
				$cats = array();
			}
			$cats[] = array("sid" => $pru['sid'], "nombre" => $repoCat->find($pru['cat'])->getNombre());
		}
		if (count($prus) > 0 ){
			$tprm = $repoTprm->find($currentTprm);
				if ($tprm->getSexo() == 0) $sexo = ", masculino";
				else if ($tprm->getSexo() == 1) $sexo = ", femenino";
				else $sexo = "";
				$nombre = $tprm->getSidTprf()->getNombre().$sexo.". ".$tprm->getEntorno();
			$result[] = array("tprm" => $nombre, "cats" => $cats);
		}
		return $result;
	}
	
	/**
	 * Llena la base de datos con datos de ejemplo para realizar pruebas
	 * @param $em El Entity Manager
	 */
	public static function poblarBD($em){
		$com1 = new Competicion();
		$com1->setNombre("Competición 1")
		->setTemp(2014)
		->setFecha(new \DateTime("2015/08/22"))
		->setCartel("ejemplo.jpg");
		$em->persist($com1);
		
		$com2 = new Competicion();
		$com2->setNombre("Competición 2")
		->setTemp(2015)
		->setFecha(new \DateTime("2015/11/07"))
		->setCartel("ejemplo.jpg");
		$em->persist($com2);
		
		$com3 = new Competicion();
		$com3->setNombre("Competición 3")
		->setTemp(2015)
		->setFecha(new \DateTime("2015/12/12"))
		->setCartel("ejemplo.jpg")
		->setEsFeder(true)
		->setEsOficial(true);
		$em->persist($com3);
		
		$tprf = new TipoPruebaFormato();
		$tprf->setNombre("100 metros lisos")
		->setUnidades("segundos")
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
		->setUnidades("metros")
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
		->setUnidades("segundos")
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
		->setUnidades("puntosdesc")
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
		->setUnidades("segundos")
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
		$pru->setId(1)
		->setSidCom($com1)
		->setCoste("4.95")
		->setIdCat($cat)
		->setSidTprm($tprm1);
		$em->persist($pru);
		$em->flush();
		
		$ron = new Ronda();
		$ron->setId(1)
		->setSidPru($pru)
		->setNum(1)
		->setNombre("Cuartos de final A");
		$em->persist($ron);

		$ron = new Ronda();
		$ron->setId(2)
		->setSidPru($pru)
		->setNum(1)
		->setNombre("Cuartos de final B");
		$em->persist($ron);
		
		$ron = new Ronda();
		$ron->setId(3)
		->setSidPru($pru)
		->setNum(1)
		->setNombre("Cuartos de final C");
		$em->persist($ron);
		
		$ron = new Ronda();
		$ron->setId(4)
		->setSidPru($pru)
		->setNum(1)
		->setNombre("Cuartos de final D");
		$em->persist($ron);
		
		$ron = new Ronda();
		$ron->setId(5)
		->setSidPru($pru)
		->setNum(2)
		->setNombre("Semifinal A");
		$em->persist($ron);
		
		$ron = new Ronda();
		$ron->setId(6)
		->setSidPru($pru)
		->setNum(2)
		->setNombre("Semifinal B");
		$em->persist($ron);
		
		$ron = new Ronda();
		$ron->setId(7)
		->setSidPru($pru)
		->setNum(3)
		->setNombre("Final");
		$em->persist($ron);
		
		$pru = new Prueba();
		$pru->setId(2)
		->setSidCom($com1)
		->setCoste("3.95")
		->setIdCat($cat1)
		->setSidTprm($tprm2);
		$em->persist($pru);
		$em->flush();
		
		$ron = new Ronda();
		$ron->setId(1)
		->setSidPru($pru)
		->setNum(1)
		->setNombre("Semifinal A");
		$em->persist($ron);
		
		$ron = new Ronda();
		$ron->setId(2)
		->setSidPru($pru)
		->setNum(1)
		->setNombre("Semifinal B");
		$em->persist($ron);
		
		$ron = new Ronda();
		$ron->setId(3)
		->setSidPru($pru)
		->setNum(2)
		->setNombre("Final");
		$em->persist($ron);
		
		$pru = new Prueba();
		$pru->setId(1)
		->setSidCom($com2)
		->setCoste("2.45")
		->setIdCat($cat)
		->setSidTprm($tprm1);
		$em->persist($pru);
		$em->flush();
		
		$ron = new Ronda();
		$ron->setId(1)
		->setSidPru($pru)
		->setNum(1)
		->setNombre("Final");
		$em->persist($ron);
		
		$pru = new Prueba();
		$pru->setId(2)
		->setSidCom($com2)
		->setCoste("2.45")
		->setIdCat($cat)
		->setSidTprm($tprm2);
		$em->persist($pru);
		$em->flush();
		
		$ron = new Ronda();
		$ron->setId(1)
		->setSidPru($pru)
		->setNum(1)
		->setNombre("Semifinal A");
		$em->persist($ron);
		
		$ron = new Ronda();
		$ron->setId(2)
		->setSidPru($pru)
		->setNum(1)
		->setNombre("Semifinal B");
		$em->persist($ron);
		
		$ron = new Ronda();
		$ron->setId(3)
		->setSidPru($pru)
		->setNum(2)
		->setNombre("Final");
		$em->persist($ron);
		
		$pru = new Prueba();
		$pru->setId(3)
		->setSidCom($com2)
		->setCoste("2.45")
		->setIdCat($cat)
		->setSidTprm($tprm3);
		$em->persist($pru);
		$em->flush();
		
		$ron = new Ronda();
		$ron->setId(1)
		->setSidPru($pru)
		->setNum(1)
		->setNombre("Final");
		$em->persist($ron);
		
		$pru = new Prueba();
		$pru->setId(4)
		->setSidCom($com2)
		->setCoste("2.45")
		->setIdCat($cat)
		->setSidTprm($tprm4);
		$em->persist($pru);
		$em->flush();
		
		$ron1 = new Ronda();
		$ron1->setId(1)
		->setSidPru($pru)
		->setNum(1)
		->setNombre("Semifinal A");
		$em->persist($ron1);
		
		$ron = new Ronda();
		$ron->setId(2)
		->setSidPru($pru)
		->setNum(1)
		->setNombre("Semifinal B");
		$em->persist($ron);
		
		$ron = new Ronda();
		$ron->setId(3)
		->setSidPru($pru)
		->setNum(2)
		->setNombre("Final");
		$em->persist($ron);
		
		$atl1 = new Atleta();
		$atl1->setNombre("Nombre1")
		->setApellidos("ApellidoA ApellidoB")
		->setSexo(false)
		->setFnac(new \DateTime("1990/07/22"))
		->setNick("Nick1")
		->setDni("12345678A")
		->setLfga("AG-1234567")
		->setLxogade("ABC123456");
		$em->persist($atl1);
		
		$ins = new Inscripcion();
		$ins->setIdAtl($atl1)
		->setSidPru($pru)
		->setFecha(new \DateTime)
		->setOrigen("test")
		->setCoste(1.00)
		->setEstado("Pendiente");
		$em->persist($ins);
		
		$atl = new Atleta();
		$atl->setNombre("Nombre2")
		->setApellidos("ApellidoA ApellidoB")
		->setSexo(false)
		->setFnac(new \DateTime("1988/07/22"))
		->setNick("Nick2")
		->setDni("12345689B")
		->setLfga("AG-1234568")
		->setLxogade("ABC123457");
		$em->persist($atl);
		
		$ins = new Inscripcion();
		$ins->setIdAtl($atl)
		->setSidPru($pru)
		->setFecha(new \DateTime)
		->setOrigen("test")
		->setCoste(1.20)
		->setEstado("Pagado");
		$em->persist($ins);
		
		$ins = new Inscripcion();
		$ins->setIdAtl($atl)
		->setSidPru($pru)
		->setFecha(new \DateTime)
		->setOrigen("test")
		->setCoste(4.00)
		->setEstado("Pagado");
		$em->persist($ins);
		
		$par = new Participacion();
		$par->setIdAtl($atl)
		->setSidCom($pru->getSidCom())
		->setDorsal(1234)
		->setAsisten(true);
		$em->persist($par);
		
		$atl = new Atleta();
		$atl->setNombre("Nombre3")
		->setApellidos("ApellidoA ApellidoB")
		->setSexo(true)
		->setFnac(new \DateTime("1972/07/22"))
		->setNick("Nick3")
		->setDni("12345698C")
		->setLfga("AG-1234569");
		$em->persist($atl);
		
		$atl = new Atleta();
		$atl->setNombre("Nombre4")
		->setApellidos("ApellidoA ApellidoB")
		->setSexo(false)
		->setFnac(new \DateTime("1994/07/22"))
		->setNick("Nick4")
		->setDni("12342424C")
		->setLfga("AG-1233426")
		->setLxogade("ABC123454");
		$em->persist($atl);
		
		$atl = new Atleta();
		$atl->setNombre("Nombre5")
		->setApellidos("ApellidoA ApellidoB")
		->setSexo(true)
		->setFnac(new \DateTime("1996/07/22"))
		->setNick("Nick5")
		->setDni("12345600G")
		->setLfga("AG-1764567")
		->setLxogade("ABC124556");
		$em->persist($atl);
		
		$atl6 = new Atleta();
		$atl6->setNombre("Nombre6")
		->setApellidos("ApellidoA ApellidoB")
		->setSexo(false)
		->setFnac(new \DateTime("1976/12/01"))
		->setNick("Nick6")
		->setDni("52345675A")
		->setLfga("AG-4234567");
		$em->persist($atl6);
		
		$atl = new Atleta();
		$atl->setNombre("Nombre7")
		->setApellidos("ApellidoA ApellidoB")
		->setSexo(true)
		->setFnac(new \DateTime("2006/07/22"))
		->setDni("92345678A");
		$em->persist($atl);
		
		$atl = new Atleta();
		$atl->setNombre("Nombre8")
		->setApellidos("ApellidoA ApellidoB")
		->setSexo(false)
		->setFnac(new \DateTime("2012/07/22"))
		->setDni("99999999Z");
		$em->persist($atl);
		
		$atl9 = new Atleta();
		$atl9->setNombre("Nombre9")
		->setApellidos("Otros Apellidos")
		->setSexo(false)
		->setFnac(new \DateTime("1981/04/18"))
		->setDni("63526420F");
		$em->persist($atl9);
		
		$int = new Intento();
		$int->setIdAtl($atl1)
		->setSidRon($ron1)
		->setNum(1)
		->setValidez(false)
		->setOrigen("test")
		->setMarca(3.40);
		$em->persist($int);
		
		$int = new Intento();
		$int->setIdAtl($atl1)
		->setSidRon($ron1)
		->setNum(2)
		->setValidez(true)
		->setOrigen("test")
		->setMarca(3.40);
		$em->persist($int);
		
		$usu = new Usuario();
		$usu->setNombre("usuario6")
		->setContra('$2a$04$H5G9G/lmx5QLL/Zm3fOyTu92bPmI4/RKnyZrWH48EXJRcA6qXh1yO')
		->setRol("socio")
		->setIdAtl($atl6);
		$em->persist($usu);
		
		$usu = new Usuario();
		$usu->setNombre("user")
		->setContra('$2a$04$WT1Ed.63TVkKNpPMQZivquS6e4NJlTTo9HRxbrYPYnCV/NqeVlQDa')
		->setRol("socio")
		->setIdAtl(null);
		$em->persist($usu);
		
		$usu = new Usuario();
		$usu->setNombre("coordinador")
		->setContra('$2a$04$9jIBy4sJT/qCpXNcJYHOMek..3ZA.EIqF7zrzYIzQrHwaTjUwvt9q')
		->setRol("coordinador")
		->setIdAtl($atl9);
		$em->persist($usu);
		
		$em->flush();
	}

}