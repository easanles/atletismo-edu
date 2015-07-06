<?php

namespace Easanles\AtletismoBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Easanles\AtletismoBundle\EasanlesAtletismoBundle;
use Symfony\Component\Config\Definition\Exception\Exception;

class CompeticionRepository extends EntityRepository {
	public function findAllOrdered()	{
		return $this->getEntityManager()
		->createQuery(
				'SELECT com.sid, com.temp, com.ubicacion, com.nombre, com.fecha, com.sede FROM EasanlesAtletismoBundle:Competicion com ORDER BY com.temp DESC, com.fecha DESC'
		)
		->getResult();
	}
	
	public function checkData($nombre, $temp){
		$comCheck = $this->getEntityManager()
		->createQuery(
		//		"SELECT com.nombre, com.temp FROM EasanlesAtletismoBundle:Competicion com WHERE com.nombre = '".$nombre."' AND com.temp = ".$temp )
			'SELECT com.nombre, com.temp FROM EasanlesAtletismoBundle:Competicion com WHERE com.nombre = :nombre AND com.temp = :temp' )
		->setParameter('nombre', $nombre)
	   ->setParameter('temp', $temp)
		->getResult();
      return ($comCheck != null);
	}
}
