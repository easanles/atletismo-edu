<?php

namespace Easanles\AtletismoBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Easanles\AtletismoBundle\EasanlesAtletismoBundle;
use Symfony\Component\Config\Definition\Exception\Exception;

class TipoPruebaModalidadRepository extends EntityRepository {
	public function findFor($sidTprf) {
		return $this->getEntityManager()
		->createQuery('SELECT tprm.sid, tprm.sexo, tprm.entorno FROM EasanlesAtletismoBundle:TipoPruebaModalidad tprm WHERE IDENTITY(tprm.sidTprf) LIKE :sidtprf')
		->setParameter("sidtprf", $sidTprf)
		->getResult();
	}
	
	public function findAllEntornos(){
		return $this->getEntityManager()
   	->createQuery('SELECT tprm.entorno FROM EasanlesAtletismoBundle:TipoPruebaModalidad tprm GROUP BY tprm.entorno')
   	->getResult();
	}
	
	public function findUsedTprfsFor($entorno){
		return $this->getEntityManager()
		->createQuery('SELECT tprf.sid, tprf.nombre, tprf.unidades
				 FROM EasanlesAtletismoBundle:TipoPruebaModalidad tprm
				 JOIN tprm.sidTprf tprf
				 WHERE tprm.entorno LIKE :entorno
				 GROUP BY tprm.sidTprf')
	   ->setParameter("entorno", $entorno)
		->getResult();
	}
	
	/*
	public function countFor($tprf) {
		return $this->getEntityManager()
		->createQuery('SELECT count(tprm.sid) AS cuenta FROM EasanlesAtletismoBundle:TipoPruebaModalidad tprm WHERE tprm.sidTprf LIKE :sidtprf')
		->setParameter("sidtprf", $tprf)
		->getResult();
	}
	
	public function checkData($nombre){
      return (false);
	}*/
}
