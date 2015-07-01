<?php

namespace Easanles\AtletismoBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Easanles\AtletismoBundle\EasanlesAtletismoBundle;

class CompeticionRepository extends EntityRepository {
	public function findAllOrdered()	{
		return $this->getEntityManager()
		->createQuery(
				'SELECT com.temp, com.ubicacion, com.nombre, com.fecha, com.sede FROM EasanlesAtletismoBundle:Competicion com ORDER BY com.temp DESC, com.fecha DESC'
		)
		->getResult();
	}
}
