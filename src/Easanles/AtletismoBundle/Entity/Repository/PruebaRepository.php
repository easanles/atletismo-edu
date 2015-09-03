<?php

namespace Easanles\AtletismoBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Easanles\AtletismoBundle\EasanlesAtletismoBundle;
use Symfony\Component\Config\Definition\Exception\Exception;

class PruebaRepository extends EntityRepository {
	public function findFor($sidCom) {
		return $this->getEntityManager()
		->createQuery('SELECT pru.sid, pru.id, pru.sidTprm, pru.idCat, pru.ronda, pru.nombre FROM EasanlesAtletismoBundle:Prueba pru WHERE IDENTITY(pru.sidCom) LIKE :sidcom')
		->setParameter("sidcom", $sidCom)
		->getResult();
	}
	
	/*public function checkData($nombre){
      return (false);
	}*/
}
