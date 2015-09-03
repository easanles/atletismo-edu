<?php

namespace Easanles\AtletismoBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Easanles\AtletismoBundle\EasanlesAtletismoBundle;
use Symfony\Component\Config\Definition\Exception\Exception;

class TipoPruebaModalidadRepository extends EntityRepository {
	/*public function findFor($sidTprf) {
		return $this->getEntityManager()
		->createQuery('SELECT tprm.sexo, tprm.entorno FROM EasanlesAtletismoBundle:TipoPruebaModalidad tprm WHERE tprm.sidtprf LIKE :sidtprf')
		->setParameter("sidtprf", $sidTprf)
		->getResult();
	}
	
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
