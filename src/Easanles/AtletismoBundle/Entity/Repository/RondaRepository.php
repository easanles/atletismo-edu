<?php

# Copyright (c) 2016 Eduardo Alonso Sanlés
# Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php

namespace Easanles\AtletismoBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Easanles\AtletismoBundle\EasanlesAtletismoBundle;
use Symfony\Component\Config\Definition\Exception\Exception;

class RondaRepository extends EntityRepository {
	public function findAllFor($sidPru) {
		return $this->getEntityManager()
		->createQuery('SELECT ron.sid, ron.id, IDENTITY(ron.sidPru) AS pru, ron.num, ron.nombre FROM EasanlesAtletismoBundle:Ronda ron WHERE IDENTITY(ron.sidPru) LIKE :sidpru ORDER BY ron.num ASC, ron.id ASC')
		->setParameter("sidpru", $sidPru)
		->getResult();
	}
	
}
