<?php

namespace Easanles\AtletismoBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Easanles\AtletismoBundle\EasanlesAtletismoBundle;
use Symfony\Component\Config\Definition\Exception\Exception;

class CategoriaRepository extends EntityRepository {
   public function findAllCurrent() {
		return $this->getEntityManager()
		->createQuery('SELECT cat.id, cat.nombre, cat.edadMax, cat.tIniVal, cat.tFinVal FROM EasanlesAtletismoBundle:Categoria cat WHERE cat.tFinVal IS NULL')
		->getResult();
	}
	
	public function findOneCurrent($nombre) {
		return $this->getEntityManager()
		->createQuery('SELECT cat.id, cat.nombre, cat.edadMax, cat.tIniVal, cat.tFinVal FROM EasanlesAtletismoBundle:Categoria cat WHERE cat.tFinVal IS NULL AND cat.nombre LIKE :nombre')
		->setParameter("nombre", $nombre)
		->getResult();
	}
	
	public function findCurrentEdadMaxNull() {
		return $this->getEntityManager()
		->createQuery('SELECT cat.id, cat.nombre, cat.edadMax FROM EasanlesAtletismoBundle:Categoria cat WHERE cat.tFinVal IS NULL AND cat.edadMax IS NULL')
		->getResult();
	}
}
