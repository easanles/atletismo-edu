<?php

namespace Easanles\AtletismoBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Easanles\AtletismoBundle\EasanlesAtletismoBundle;
use Symfony\Component\Config\Definition\Exception\Exception;
use Composer\Autoload\ClassLoader;

class CompeticionRepository extends EntityRepository {
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
	
	public function findTempComs($temp, $rol){
		$qb = $this->getEntityManager()->createQueryBuilder('com');
		if ($rol == "user"){
			$qb->where('com.esVisible = 1');
		}
		return $qb->select('com.sid, com.temp, com.nombre, com.fecha')
		->from('EasanlesAtletismoBundle:Competicion', 'com')
		->andWhere('com.temp = :temp')
		->orderBy('com.fecha', 'DESC')
		->setParameter('temp', $temp)
		->getQuery()->getResult();
		
		$result = $this->getEntityManager()
		->createQuery('SELECT com.sid, com.temp, com.nombre, com.fecha FROM EasanlesAtletismoBundle:Competicion com WHERE com.temp = :temp ORDER BY com.fecha DESC')
		->getResult();
		return $result;
	}
	
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
	
	public function checkData($nombre, $temp){
		$comCheck = $this->getEntityManager()
		->createQuery(
			'SELECT com.nombre, com.temp FROM EasanlesAtletismoBundle:Competicion com WHERE com.nombre = :nombre AND com.temp = :temp' )
		->setParameter('nombre', $nombre)
	   ->setParameter('temp', $temp)
		->getResult();
      return ($comCheck != null);
	}
	
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
}
