<?php

namespace Easanles\AtletismoBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Easanles\AtletismoBundle\EasanlesAtletismoBundle;
use Symfony\Component\Config\Definition\Exception\Exception;

class PruebaRepository extends EntityRepository {
	public function findAllFor($sidCom) {
		return $this->getEntityManager()
		->createQuery('SELECT pru.sid, pru.id, IDENTITY(pru.sidTprm) as tprm, pru.idCat, pru.ronda, pru.nombre FROM EasanlesAtletismoBundle:Prueba pru WHERE IDENTITY(pru.sidCom) LIKE :sidcom')
		->setParameter("sidcom", $sidCom)
		->getResult();
	}
	
	public function findTprs($sidCom){
		return $this->getEntityManager()
		->createQuery('SELECT IDENTITY(pru.sidTprm) AS sidTprm FROM EasanlesAtletismoBundle:Prueba pru WHERE IDENTITY(pru.sidCom) LIKE :sidcom GROUP BY pru.sidTprm ORDER BY pru.sidTprm ASC')
		->setParameter("sidcom", $sidCom)
		->getResult();
	}
	
	public function searchByParameters($sidCom, $sidTprm, $idCat) {
		$qb = $this->getEntityManager()->createQueryBuilder('pru')
		      ->where('IDENTITY(pru.sidCom) LIKE :sidcom')
		      ->setParameter('sidcom', $sidCom);
		if ((($sidTprm != null) && ($sidTprm != ""))){
			$qb = $qb->andWhere('IDENTITY(pru.sidTprm) LIKE :sidtprm')
			      ->setParameter('sidtprm', $sidTprm);
		}
		if ((($idCat != null) && ($idCat != ""))){
			$qb = $qb->andWhere('pru.idCat LIKE :idcat')
			      ->setParameter('idcat', $idCat);
		}
		return $qb->select('pru.sid, pru.id, IDENTITY(pru.sidTprm) AS tprm, pru.idCat, pru.ronda, pru.nombre')
		          ->from('EasanlesAtletismoBundle:Prueba', 'pru')
		          ->getQuery()->getResult();
	}
	
	/*public function checkData($nombre){
      return (false);
	}*/
}
