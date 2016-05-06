<?php

namespace Easanles\AtletismoBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Easanles\AtletismoBundle\EasanlesAtletismoBundle;
use Symfony\Component\Config\Definition\Exception\Exception;

class IntentoRepository extends EntityRepository {
	public function getMarcaFor($idAtl, $sidRon) {
		$query = $this->getEntityManager()
		->createQuery('SELECT int.marca, int.validez FROM EasanlesAtletismoBundle:Intento int WHERE IDENTITY(int.sidRon) LIKE :sidron AND IDENTITY(int.idAtl) LIKE :idatl')
		->setParameter("idatl", $idAtl)
		->setParameter("sidron", $sidRon)
		->getResult();
		if (count($query) == 0) return null;
		else {
			$result = null;
			foreach(array_reverse($query) as $int){
			   if ($int['validez'] == true){
			   	$result = $int['marca'];
			   	break;
			   }
			}
			return $result;
		}
	}
	
	public function findOrderedBy($idAtl, $sidRon) {
		return $this->getEntityManager()
		->createQuery('SELECT int.sid, int.num FROM EasanlesAtletismoBundle:Intento int WHERE IDENTITY(int.sidRon) LIKE :sidron AND IDENTITY(int.idAtl) LIKE :idatl ORDER BY int.num ASC')
		->setParameter("idatl", $idAtl)
		->setParameter("sidron", $sidRon)
		->getResult();
	}
	
	public function countEntriesFor($idAtl, $sidPru){
		$query = $this->getEntityManager()
		->createQuery('SELECT int.sid
				 FROM EasanlesAtletismoBundle:Intento int
				 JOIN int.sidRon ron
				 WHERE IDENTITY(int.idAtl) LIKE :idatl
				 AND IDENTITY(ron.sidPru) LIKE :sidpru')
		->setParameter("idatl", $idAtl)
		->setParameter("sidpru", $sidPru)
		->getResult();
		return count($query);
	}
	
	public function countAtlsFor($sidRon){
		$query = $this->getEntityManager()
		->createQuery('SELECT IDENTITY (int.idAtl)
				 FROM EasanlesAtletismoBundle:Intento int
				 WHERE IDENTITY(int.sidRon) LIKE :sidron
				 GROUP BY int.idAtl')
					 ->setParameter("sidron", $sidRon)
					 ->getResult();
		return count($query);
	}
	
	public function findAllEntriesFor($sidRon, $orden){
		return $this->getEntityManager()
		->createQuery('SELECT int.sid, IDENTITY (int.idAtl), atl.nombre, atl.apellidos, int.num, int.marca, int.validez, int.origen, int.premios
				 FROM EasanlesAtletismoBundle:Intento int
				 JOIN int.idAtl atl
				 WHERE IDENTITY(int.sidRon) LIKE :sidron
				 ORDER BY int.idAtl, int.marca '.$orden.', int.validez ASC')
		->setParameter("sidron", $sidRon)
		->getResult();
	}
	
	/**
	 * $sidRon debe estar validado previamente como numero entero
	 */
	public function findBestMarcas($sidRon, $orden){
      //Unica forma de hacer funcionar subconsultas en Doctrine. 
		return $this->getEntityManager()->getConnection()->fetchAll(
		      'SELECT sidint AS sid, idatl AS idAtl, nombreatl AS nombre, apellidosatl AS apellidos, marcaint AS marca, origenint AS origen, premiosint AS premios
             FROM (
                SELECT sidint, idatl, marcaint, origenint, premiosint
                FROM (
                   SELECT idatl, MAX(numint) AS numint
                   FROM int_
                   WHERE sidron LIKE '.$sidRon.' AND validezint = 1
                   GROUP BY idatl
                ) aux1
                NATURAL JOIN (
                   SELECT sidint, idatl, marcaint, origenint, premiosint, numint
                   FROM int_
                   WHERE sidron LIKE '.$sidRon.' AND validezint = 1
                ) aux2
             ) aux
             NATURAL JOIN atl
             ORDER BY marca '.$orden);
	}
	
	public function findMarcaIntentos($idAtl, $sidRon){
		return $this->getEntityManager()
		->createQuery('SELECT int.marca, int.validez, int.num, int.origen, int.premios
				 FROM EasanlesAtletismoBundle:Intento int
				 WHERE IDENTITY(int.sidRon) LIKE :sidron AND IDENTITY(int.idAtl) LIKE :idatl
				 ORDER BY int.num ASC')
		->setParameter("idatl", $idAtl)
		->setParameter("sidron", $sidRon)
		->getResult();
	}
	
	public function findRecordFor($tipo, $entorno, $tprf, $rol, $idAtl, $temp, $mostrarNoOficiales){
		switch($tprf['unidades']){
			case "segundos": $orden = "ASC"; break;
			case "metros": $orden = "DESC"; break;
			case "puntosdesc": $orden = "DESC"; break;
		   case "puntosasc": $orden = "ASC"; break;
		   default: $orden = "ASC";
		}
		$sql = 'SELECT int.premios, int.marca, IDENTITY(int.idAtl) AS idAtl, atl.apellidos, atl.nombre, cat.nombre AS categoria, com.fecha, com.sede
				 FROM EasanlesAtletismoBundle:Intento int
				 JOIN int.sidRon ron
				 JOIN ron.sidPru pru
				 JOIN pru.idCat cat
				 JOIN pru.sidCom com
				 JOIN pru.sidTprm tprm
				 JOIN int.idAtl atl
				 WHERE tprm.entorno LIKE :entorno AND int.validez = 1';
		if ($mostrarNoOficiales == false) $sql = $sql.' AND com.esOficial = 1';
		if ($rol == "user") $sql = $sql.' AND com.esVisible = 1';
		if (($tipo == 2) || ($tipo == 3)) $sql = $sql.' AND IDENTITY(int.idAtl) LIKE :idatl';
		else $sql = $sql.' AND atl.sexo LIKE :sexo';
	   if (($temp != null) && ($temp != "")){
			$sql = $sql.' AND com.temp LIKE :temp';
		}
		$sql = $sql.' AND IDENTITY(tprm.sidTprf) LIKE :sidtprf
				 ORDER BY int.marca '.$orden.', int.sid ASC';
		$query = $this->getEntityManager()
		->createQuery($sql)
		->setParameter("entorno", $entorno)
		->setParameter("sidtprf", $tprf['sid'])
		->setMaxResults(1);
		if (($tipo == 2) || ($tipo == 3)) $query = $query->setParameter("idatl", $idAtl);
		else $query = $query->setParameter("sexo", $tipo);
		if (($temp != null) && ($temp != "")){
			$query = $query->setParameter("temp", $temp);
		}
		$query = $query->getResult();
		if (count($query) > 0) return array_values($query);
		else return null;
	}

	public function findLastMarcaFor($idAtl, $sidPru) {
		return $this->getEntityManager()
		->createQuery('SELECT int.sid, IDENTITY(int.sidRon) AS sidRon, int.marca, int.premios 
				 FROM EasanlesAtletismoBundle:Intento int
				 JOIN int.sidRon ron
				 WHERE ron.sidPru = :sidpru AND int.idAtl = :idatl AND int.validez = 1
				 ORDER BY ron.num DESC, int.num DESC')
		->setParameter("idatl", $idAtl)
		->setParameter("sidpru", $sidPru)
		->setMaxResults(1)
		->getResult();
	}
	
}
