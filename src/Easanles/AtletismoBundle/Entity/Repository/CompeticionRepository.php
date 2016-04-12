<?php

namespace Easanles\AtletismoBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Easanles\AtletismoBundle\EasanlesAtletismoBundle;
use Symfony\Component\Config\Definition\Exception\Exception;
use Composer\Autoload\ClassLoader;

class CompeticionRepository extends EntityRepository {
	/**
	 * Listado de competiciones
	 */
	public function findAllOrdered($from, $numResultados)	{
		$result = $this->getEntityManager()
		->createQuery('SELECT com.sid, com.temp, com.nombre, com.fecha, com.fechaFin, com.sede, com.esVisible FROM EasanlesAtletismoBundle:Competicion com ORDER BY com.temp DESC, com.fecha DESC')
		->setFirstResult($from)
		->setMaxResults($numResultados)
		->getResult();
		foreach ($result as $key => $com){
			$numPruebas = $this->find($com['sid'])->getPruebas()->count();
			$result[$key]['numpruebas'] = $numPruebas;
			$result[$key]['numatletas'] = count($this->findAtletasIns($com['sid']));
		}
		return $result;
	}

	/**
	 * Competiciones de una temporada
	 */
	public function findTempComs($temp, $rol){
		$qb = $this->getEntityManager()->createQueryBuilder('com');
		if ($rol == "user"){
			$qb->where('com.esVisible = 1')->orderBy('com.fecha', 'ASC');
		} else $qb->orderBy('com.fecha', 'DESC');
		$result = $qb->select('com.sid, com.nombre, com.temp, com.sede, com.fecha, com.fechaFin, com.desc, com.web, com.cartel, com.esFeder, com.esOficial, com.esInscrib')
		->from('EasanlesAtletismoBundle:Competicion', 'com')
		->andWhere('com.temp = :temp')
		->setParameter('temp', $temp)
		->getQuery()->getResult();
		foreach ($result as $key => $com){
			$numPruebas = $this->find($com['sid'])->getPruebas()->count();
			$result[$key]['numpruebas'] = $numPruebas;
		}
		return $result;
	}
	
	/**
	 * Lista de atletas inscritos
	 */
	public function findAtletasIns($sidCom){
		return $this->getEntityManager()
		->createQuery('SELECT IDENTITY (ins.idAtl) AS idAtl
				FROM EasanlesAtletismoBundle:Inscripcion ins
				JOIN ins.sidPru pru
				JOIN pru.sidCom com
				WHERE com.sid LIKE :sidcom
				GROUP BY ins.idAtl')
	   ->setParameter("sidcom", $sidCom)
		->getResult();
	}
	
	/**
	 * Lista de temporadas
	 */
	public function findTemps($rol){
		$qb = $this->getEntityManager()->createQueryBuilder('com');
		if ($rol == "user"){
			$qb->where('com.esVisible = 1');
		}
		return $qb->select('com.temp')
		->from('EasanlesAtletismoBundle:Competicion', 'com')
		->groupBy('com.temp')
		->orderBy('com.temp', 'DESC')
		->getQuery()->getResult();
	}
	
	/**
	 * Busqueda por parametros
	 */
	public function searchByParameters($temp, $string, $from, $numResultados) {	
		$qb = $this->getEntityManager()->createQueryBuilder('com');
		if (($string != null) || ($string != "")){
			$qb = $qb->andWhere('com.nombre LIKE :string OR com.ubicacion LIKE :string OR com.sede LIKE :string OR com.nivel LIKE :string OR com.feder LIKE :string')
			->setParameter('string', '%'.$string.'%');
		}
		if (($temp != null) && ($temp != "")){
			$qb = $qb->andWhere('com.temp = :temp')
			->setParameter('temp', $temp);
		}
		$result = $qb->select('com.sid, com.temp, com.ubicacion, com.nombre, com.fecha, com.fechaFin, com.sede, com.esVisible')
		->from('EasanlesAtletismoBundle:Competicion', 'com')
		->orderBy('com.temp', 'DESC')
		->addOrderBy('com.fecha', 'DESC')
		->getQuery()
		->setFirstResult($from)
		->setMaxResults($numResultados)
		->getResult();
		
		foreach ($result as $key => $com){
			$numPruebas = $this->find($com['sid'])->getPruebas()->count();
			$result[$key]['numpruebas'] = $numPruebas;
			$result[$key]['numatletas'] = count($this->findAtletasIns($com['sid']));
		}
		return $result;
	}
	
	/**
    * Comprobar la existencia de una competicion
	 */
	public function checkData($nombre, $temp){
		$comCheck = $this->getEntityManager()
		->createQuery(
			'SELECT com.nombre, com.temp FROM EasanlesAtletismoBundle:Competicion com WHERE com.nombre = :nombre AND com.temp = :temp' )
		->setParameter('nombre', $nombre)
	   ->setParameter('temp', $temp)
		->getResult();
      return ($comCheck != null);
	}
	
	/**
	 * Competiciones siguientes, actuales o previas
	 */
	public function findComsTimedList($modo, $fechaLimite){
		$qb = $this->getEntityManager()->createQueryBuilder('com');
		switch ($modo){
			case "sig": {
				$hoy = new \DateTime();
				$qb = $qb->where('com.fecha >= :fechaini')
				->setParameter('fechaini', $hoy);
				if ($fechaLimite != null){
					$qb = $qb->andWhere('com.fechaIni <= :fechafin')
					->setParameter('fechafin', $fechaLimite);
				}
			} break;
			case "hoy": {
				$hoy = new \DateTime();
				$ayer = (new \DateTime())->sub(new \DateInterval("P1D"));
				$qb = $qb->where('com.fecha <= :fechaini')
				->andWhere('com.fechaFin >= :fechafin')
				->setParameter('fechaini', $hoy)
				->setParameter('fechafin', $ayer);
			} break;
			case "pre": {
				$ayer = (new \DateTime())->sub(new \DateInterval("P1D"));
				$qb = $qb->andWhere('com.fechaFin <= :fechafin')
				->setParameter('fechafin', $ayer);
				if ($fechaLimite != null){
					$qb = $qb->andWhere('com.fechaFin >= :fechaini')
					->setParameter('fechaini', $fechaLimite);
				}
			} break;
			default: {} break;
		}
		$result = $qb->select('com.sid, com.nombre, com.temp, com.fecha, com.fechaFin, com.sede, com.ubicacion, com.web, com.desc, com.esFeder, com.esOficial, com.esVisible, com.esInscrib, com.cartel')
		->from('EasanlesAtletismoBundle:Competicion', 'com')
		->orderBy('com.fecha', 'ASC')
		->getQuery()->getResult();
		foreach ($result as $key => $com){
			$numPruebas = $this->find($com['sid'])->getPruebas()->count();
			$result[$key]['numpruebas'] = $numPruebas;
			$result[$key]['numatletas'] = count($this->findAtletasIns($com['sid']));
		}
		return $result;
	}
	
	/**
	 * Competiciones en las que se ha inscrito un atleta en una temporada
	 */
	public function findAtlComs($idAtl, $temp){
		$query = $this->getEntityManager()
		->createQuery(
			'SELECT com.sid
		    FROM EasanlesAtletismoBundle:Inscripcion ins
          JOIN ins.idAtl atl
			 JOIN ins.sidPru pru
			 JOIN pru.sidCom com
			 WHERE atl.id = :idatl AND com.esVisible = 1 AND com.temp = :temp
			 GROUP BY com.sid
			 ORDER BY com.fecha DESC')
	   ->setParameter('temp', $temp)
		->setParameter('idatl', $idAtl)
		->getResult();
		$result = array();
		foreach ($query as $row){
			$result[] = $row['sid'];
		}
		return $result;
	}
	
	/**
	 * Obtener los entornos de una competicion
	 */
	public function getComEntornos($sidCom){
		return $this->getEntityManager()
		->createQuery('
				SELECT tprm.entorno
				FROM EasanlesAtletismoBundle:Prueba pru
				JOIN pru.sidTprm tprm
				WHERE pru.sidCom = :sidcom
				GROUP BY tprm.entorno ')
		->setParameter('sidcom', $sidCom)
		->getResult();
	}
	
}
