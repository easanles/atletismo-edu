<?php

namespace Easanles\AtletismoBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Easanles\AtletismoBundle\EasanlesAtletismoBundle;
use Symfony\Component\Config\Definition\Exception\Exception;

class UsuarioRepository extends EntityRepository {
	public function findAllOrdered() {
		return $this->getEntityManager()
		->createQuery('SELECT usu.nombre, usu.rol, IDENTITY(usu.idAtl) as idAtl FROM EasanlesAtletismoBundle:Usuario usu ORDER BY usu.rol ASC, usu.nombre ASC')
		->getResult();
	}
	
	public function searchByParameter($query) {
		return $this->getEntityManager()
		->createQuery('SELECT usu.nombre, usu.rol, IDENTITY(usu.idAtl) as idAtl
				 FROM EasanlesAtletismoBundle:Usuario usu
				 LEFT JOIN usu.idAtl atl
				 WHERE usu.nombre LIKE :query
				 OR atl.nombre LIKE :query
				 OR atl.apellidos LIKE :query
				 ORDER BY usu.rol ASC, usu.nombre ASC')
	   ->setParameter("query", "%".$query."%")
		->getResult();
	}
	
}
