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
	public function findAllOrdered()	{
		$result = $this->getEntityManager()
		->createQuery('SELECT com.sid, com.temp, com.nombre, com.fecha, com.sede, com.esVisible FROM EasanlesAtletismoBundle:Competicion com ORDER BY com.temp DESC, com.fecha DESC')
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
			$qb->where('com.esVisible = 1');
		}
		$result = $qb->select('com.sid, com.nombre, com.temp, com.sede, com.fecha, com.desc, com.web, com.cartel, com.esFeder, com.esOficial, com.esInscrib')
		->from('EasanlesAtletismoBundle:Competicion', 'com')
		->andWhere('com.temp = :temp')
		->orderBy('com.fecha', 'DESC')
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
		->createQuery('SELECT IDENTITY (ins.idAtl)
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
	public function searchByParameters($temp, $string) {	
		$qb = $this->getEntityManager()->createQueryBuilder('com');
		if (($string != null) || ($string != "")){
			$qb = $qb->andWhere('com.nombre LIKE :string OR com.ubicacion LIKE :string OR com.sede LIKE :string OR com.nivel LIKE :string OR com.feder LIKE :string')
			->setParameter('string', '%'.$string.'%');
		}
		if (($temp != null) && ($temp != "")){
			$qb = $qb->andWhere('com.temp = :temp')
			->setParameter('temp', $temp);
		}
		$result = $qb->select('com.sid, com.temp, com.ubicacion, com.nombre, com.fecha, com.sede, com.esVisible')
		->from('EasanlesAtletismoBundle:Competicion', 'com')
		->orderBy('com.temp', 'DESC')
		->addOrderBy('com.fecha', 'DESC')
		->getQuery()->getResult();
		
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
	 * Competiciones entre dos fechas
	 */
	public function findComsBetween($fechaIni, $fechaFin){
		$qb = $this->getEntityManager()->createQueryBuilder('com');
		if ($fechaIni != null){
			$qb = $qb->andWhere('com.fecha >= :fechaini')
			->setParameter('fechaini', $fechaIni);
		}
		if ($fechaFin != null){
		   $qb = $qb->andWhere('com.fecha <= :fechafin')
		   ->setParameter('fechafin', $fechaFin);
		}
		$result = $qb->select('com.sid, com.nombre, com.temp, com.fecha, com.sede, com.ubicacion, com.web, com.desc, com.esFeder, com.esOficial, com.esVisible, com.esInscrib, com.cartel')
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
	 * Proximas competiciones disponibles para inscribirse
	 */
	/*public function findAvaliableComs($temp){
		return $this->getEntityManager()
		->createQuery(
			 'SELECT com.sid, com.nombre, com.temp, com.sede, com.fecha, com.desc, com.web, com.cartel, com.esFeder, com.esOficial, com.esInscrib
		    FROM EasanlesAtletismoBundle:Competicion com
		    WHERE com.esVisible = 1 AND com.temp = :temp AND (com.fecha IS NULL OR com.fecha >= :ayer) AND com.esOficial = 0
			 ORDER BY com.fecha DESC')
	   ->setParameter('ayer', (new \DateTime())->sub(new \DateInterval("P1D")))
		->setParameter('temp', $temp)
		->getResult();
	}*/
	
	
}
