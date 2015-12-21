<?php

namespace Easanles\AtletismoBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Easanles\AtletismoBundle\EasanlesAtletismoBundle;
use Symfony\Component\Config\Definition\Exception\Exception;

class IntentoRepository extends EntityRepository {
	public function getMarcaFor($idAtl, $sidRon) {
		$query = $this->getEntityManager()
		->createQuery('SELECT int.marca FROM EasanlesAtletismoBundle:Intento int WHERE IDENTITY(int.sidRon) LIKE :sidron AND IDENTITY(int.idAtl) LIKE :idatl')
		->setParameter("idatl", $idAtl)
		->setParameter("sidron", $sidRon)
		->getResult();
		if (count($query) == 0) return null;
		else return $query[0]['marca'];
	}
	
}
