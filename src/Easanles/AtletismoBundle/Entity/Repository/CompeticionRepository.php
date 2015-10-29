<?php

namespace Easanles\AtletismoBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Easanles\AtletismoBundle\EasanlesAtletismoBundle;
use Symfony\Component\Config\Definition\Exception\Exception;
use Composer\Autoload\ClassLoader;

class CompeticionRepository extends EntityRepository {
	public function findAllOrdered()	{
		$result = $this->getEntityManager()
		->createQuery('SELECT com.sid, com.temp, com.nombre, com.fecha, com.sede FROM EasanlesAtletismoBundle:Competicion com ORDER BY com.temp DESC, com.fecha DESC')
		->getResult();
		foreach ($result as &$com){ // & = Paso por referencia
			$numPruebas = $this->find($com['sid'])->getPruebas()->count();
			$com['numpruebas'] = $numPruebas;
			$com['numatletas'] = count($this->findAtletasIns($com['sid']));
		}
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
	
	public function findTemps(){
		return $this->getEntityManager()
		->createQuery('SELECT com.temp FROM EasanlesAtletismoBundle:Competicion com GROUP BY com.temp ORDER BY com.temp DESC')
		->getResult();
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
		$result = $qb->select('com.sid, com.temp, com.ubicacion, com.nombre, com.fecha, com.sede')
		->from('EasanlesAtletismoBundle:Competicion', 'com')
		->orderBy('com.temp', 'DESC')
		->addOrderBy('com.fecha', 'DESC')
		->getQuery()->getResult();
		
		foreach ($result as &$com){ // & = Paso por referencia
			$numPruebas = $this->find($com['sid'])->getPruebas()->count();
			$com['numpruebas'] = $numPruebas;
			$com['numatletas'] = $this->countAtletasIns($com['sid']);
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
}
