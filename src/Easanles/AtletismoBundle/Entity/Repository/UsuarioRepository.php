<?php

namespace Easanles\AtletismoBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Easanles\AtletismoBundle\EasanlesAtletismoBundle;
use Symfony\Component\Config\Definition\Exception\Exception;

class UsuarioRepository extends EntityRepository {
	public function findAllOrdered($from, $numResultados) {
		return $this->getEntityManager()
		->createQuery('SELECT usu.nombre, usu.rol, IDENTITY(usu.idAtl) as idAtl FROM EasanlesAtletismoBundle:Usuario usu ORDER BY usu.rol ASC, usu.nombre ASC')
		->setFirstResult($from)
		->setMaxResults($numResultados)
		->getResult();
	}
	
	public function searchByParameter($query, $from, $numResultados) {
		return $this->getEntityManager()
		->createQuery('SELECT usu.nombre, usu.rol, IDENTITY(usu.idAtl) as idAtl
				 FROM EasanlesAtletismoBundle:Usuario usu
				 LEFT JOIN usu.idAtl atl
				 WHERE usu.nombre LIKE :query
				 OR atl.nombre LIKE :query
				 OR atl.apellidos LIKE :query
				 ORDER BY usu.rol ASC, usu.nombre ASC')
	   ->setParameter("query", "%".$query."%")
	   ->setFirstResult($from)
	   ->setMaxResults($numResultados)
		->getResult();
	}
	
	public function countCoordinadores(){
		$query = $this->getEntityManager()
		->createQuery('SELECT usu.nombre FROM EasanlesAtletismoBundle:Usuario usu WHERE usu.rol = :rolcoordinador')
		->setParameter("rolcoordinador", "coordinador")
		->getResult();
		return count($query);
	}
	
}
