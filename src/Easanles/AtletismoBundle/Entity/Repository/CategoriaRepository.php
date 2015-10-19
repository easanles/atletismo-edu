<?php

namespace Easanles\AtletismoBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Easanles\AtletismoBundle\EasanlesAtletismoBundle;
use Symfony\Component\Config\Definition\Exception\Exception;

class CategoriaRepository extends EntityRepository {
   public function findAllCurrent() {
		$result = $this->getEntityManager()
		->createQuery('SELECT cat.id, cat.nombre, cat.edadMax, cat.tIniVal, cat.tFinVal FROM EasanlesAtletismoBundle:Categoria cat WHERE cat.tFinVal IS NULL AND cat.edadMax IS NOT NULL ORDER BY cat.edadMax ASC')
		->getResult();
		$edadMaxNull = $this->findCurrentEdadMaxNull();
		if (count($edadMaxNull) != 0){
			$result[] = $this->findCurrentEdadMaxNull()[0];
		}
		return $result;
	}
	
	public function findOneCurrent($nombre) {
		return $this->getEntityManager()
		->createQuery('SELECT cat.id, cat.nombre, cat.edadMax, cat.tIniVal, cat.tFinVal FROM EasanlesAtletismoBundle:Categoria cat WHERE cat.tFinVal IS NULL AND cat.nombre LIKE :nombre')
		->setParameter("nombre", $nombre)
		->getResult();
	}
	
	public function findCurrentEdadMaxNull() {
		return $this->getEntityManager()
		->createQuery('SELECT cat.id, cat.nombre, cat.edadMax, cat.tIniVal, cat.tFinVal FROM EasanlesAtletismoBundle:Categoria cat WHERE cat.tFinVal IS NULL AND cat.edadMax IS NULL')
		->getResult();
	}
	
	public function findPreviousCat($cat){
		if ($cat->getEdadMax() == null){
			$query = $this->getEntityManager()
			->createQuery("SELECT cat.id, cat.nombre, cat.edadMax, cat.tIniVal, cat.tFinVal FROM EasanlesAtletismoBundle:Categoria cat
					 WHERE cat.tFinVal IS NULL AND cat.edadMax IS NOT NULL ORDER BY cat.edadMax DESC")
			->getResult();
		} else {
			$query = $this->getEntityManager()
			->createQuery("SELECT cat.id, cat.nombre, cat.edadMax, cat.tIniVal, cat.tFinVal FROM EasanlesAtletismoBundle:Categoria cat
					 WHERE cat.tFinVal IS NULL AND cat.edadMax < :edadMax ORDER BY cat.edadMax DESC")
			->setParameter("edadMax", $cat->getEdadMax())
			->getResult();
		}
		if (count($query) == 0) return null;
		else return $query[0];
	}
	
	//NOTA: Para evitar multiples consultas a la BD usar la funcion Helpers::getCategoria($categorias, $edad)
	public function findForEdad($edad) {
		$current = $this->findAllCurrent();
		foreach($current as $cat){
			if ($cat['edadMax'] != null){
			   if ($edad <= $cat['edadMax']){
			   	return $cat;
			   }
			} else return $cat;
		}
	}
	
}
