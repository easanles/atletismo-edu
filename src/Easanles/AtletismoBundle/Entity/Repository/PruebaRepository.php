<?php

namespace Easanles\AtletismoBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Easanles\AtletismoBundle\EasanlesAtletismoBundle;
use Symfony\Component\Config\Definition\Exception\Exception;

class PruebaRepository extends EntityRepository {
	public function findAllFor($sidCom, $from, $numResultados) {
		return $this->getEntityManager()
		->createQuery('SELECT pru.sid, pru.id, IDENTITY(pru.sidTprm) AS tprm, IDENTITY(pru.idCat) AS cat, pru.coste FROM EasanlesAtletismoBundle:Prueba pru WHERE IDENTITY(pru.sidCom) LIKE :sidcom ORDER BY pru.idCat ASC, pru.id ASC')
		->setParameter("sidcom", $sidCom)
		->setFirstResult($from)
		->setMaxResults($numResultados)
		->getResult();
	}
	
	public function findAllOrderedFor($sidCom) {
		return $this->getEntityManager()
		->createQuery('SELECT pru.sid, IDENTITY(pru.sidTprm) AS tprm, IDENTITY(pru.idCat) AS cat FROM EasanlesAtletismoBundle:Prueba pru WHERE IDENTITY(pru.sidCom) LIKE :sidcom ORDER BY pru.sidTprm ASC, pru.idCat ASC')
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
	
	public function searchByParameters($sidCom, $idCat, $from, $numResultados) {
		$qb = $this->getEntityManager()->createQueryBuilder('pru')
		      ->where('IDENTITY(pru.sidCom) LIKE :sidcom')
		      ->setParameter('sidcom', $sidCom);
		if ((($idCat != null) && ($idCat != ""))){
			$qb = $qb->andWhere('IDENTITY(pru.idCat) LIKE :idcat')
			      ->setParameter('idcat', $idCat);
		}
		return $qb->select('pru.sid, pru.id, IDENTITY(pru.sidTprm) AS tprm, IDENTITY(pru.idCat) AS cat, pru.coste,
				tprm_.sexo, tprm_.entorno, tprf.nombre')
		          ->from('EasanlesAtletismoBundle:Prueba', 'pru')
		          ->join('pru.sidTprm', 'tprm_')
		          ->join('tprm_.sidTprf', 'tprf')
		          ->orderBy("pru.idCat", "ASC")
		          ->orderBy("pru.id", "ASC")
		          ->getQuery()
				    ->setFirstResult($from)
		          ->setMaxResults($numResultados)
		          ->getResult();
	}
	
}
