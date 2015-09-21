<?php

namespace Easanles\AtletismoBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Easanles\AtletismoBundle\EasanlesAtletismoBundle;
use Symfony\Component\Config\Definition\Exception\Exception;

class PruebaRepository extends EntityRepository {
	public function findAllFor($sidCom) {
		return $this->getEntityManager()
		->createQuery('SELECT pru.sid, pru.id, IDENTITY(pru.sidTprm) AS tprm, IDENTITY(pru.idCat) AS cat, pru.ronda, pru.nombre FROM EasanlesAtletismoBundle:Prueba pru WHERE IDENTITY(pru.sidCom) LIKE :sidcom ORDER BY pru.sidTprm DESC, pru.idCat ASC, pru.ronda ASC, pru.id ASC')
		->setParameter("sidcom", $sidCom)
		->getResult();
	}
	
	public function findTprs($sidCom){
		return $this->getEntityManager()
		->createQuery('SELECT IDENTITY(pru.sidTprm) AS sidTprm FROM EasanlesAtletismoBundle:Prueba pru WHERE IDENTITY(pru.sidCom) LIKE :sidcom GROUP BY pru.sidTprm ORDER BY pru.sidTprm ASC')
		->setParameter("sidcom", $sidCom)
		->getResult();
	}

	public function findCats($sidCom){
		return $this->getEntityManager()
		->createQuery('SELECT IDENTITY(pru.idCat) AS idCat FROM EasanlesAtletismoBundle:Prueba pru WHERE IDENTITY(pru.sidCom) LIKE :sidcom GROUP BY pru.idCat ORDER BY pru.idCat ASC')
		->setParameter("sidcom", $sidCom)
		->getResult();
	}
	
	public function maxId($sidCom){
		$query = $this->getEntityManager()
		->createQuery('SELECT pru.id FROM EasanlesAtletismoBundle:Prueba pru WHERE IDENTITY(pru.sidCom) LIKE :sidcom')
		->setParameter("sidcom", $sidCom)
		->getResult();
		if (count($query) == 0) return 0;
		else return max($query)['id'];
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
			$qb = $qb->andWhere('IDENTITY(pru.idCat) LIKE :idcat')
			      ->setParameter('idcat', $idCat);
		}
		return $qb->select('pru.sid, pru.id, IDENTITY(pru.sidTprm) AS tprm, IDENTITY(pru.idCat) AS cat, pru.ronda, pru.nombre')
		          ->from('EasanlesAtletismoBundle:Prueba', 'pru')
		          ->orderBy("pru.sidTprm", "DESC")
		          ->orderBy("pru.idCat", "ASC")
		          ->orderBy("pru.ronda", "ASC")
		          ->orderBy("pru.id", "ASC")
		          ->getQuery()->getResult();
	}
	
	public function getNextRondas($sidCom, $sidTprm, $idCat, $ronda){
		return $this->getEntityManager()->createQueryBuilder('pru')
		->select('pru.sid, pru.ronda')
		->from('EasanlesAtletismoBundle:Prueba', 'pru')
		->where('IDENTITY(pru.sidCom) LIKE :sidcom')
		->setParameter('sidcom', $sidCom)
		->andWhere('IDENTITY(pru.sidTprm) LIKE :sidtprm')
		->setParameter('sidtprm', $sidTprm)
		->andWhere('IDENTITY(pru.idCat) LIKE :idcat')
		->setParameter('idcat', $idCat)
		->andWhere('pru.ronda >= :ronda')
		->setParameter('ronda', $ronda)
		->getQuery()->getResult();
	}
	
	
}
