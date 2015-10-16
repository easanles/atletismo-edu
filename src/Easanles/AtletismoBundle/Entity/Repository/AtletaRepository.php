<?php

namespace Easanles\AtletismoBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Easanles\AtletismoBundle\EasanlesAtletismoBundle;
use Symfony\Component\Config\Definition\Exception\Exception;
use Easanles\AtletismoBundle\Helpers\Helpers;

class AtletaRepository extends EntityRepository {
	public function findAllOrdered()	{
		$result = $this->getEntityManager()
		->createQuery('SELECT atl.id, atl.nombre, atl.apellidos, atl.nick, atl.fnac, atl.sexo, atl.lfga, atl.lxogade FROM EasanlesAtletismoBundle:Atleta atl ORDER BY atl.fnac DESC')
		->getResult();
		return $result;
	}
	
	public function searchByParameters($fnacIni, $fnacFin, $string) {	
		$qb = $this->getEntityManager()->createQueryBuilder('atl');
		if (($string != null) || ($string != "")){
			$qb = $qb->andWhere('atl.id = :exstring
					 OR atl.nombre LIKE :string
					 OR atl.apellidos LIKE :string
					 OR atl.nick LIKE :string
					 OR atl.dni LIKE :exstring
					 OR atl.bloque LIKE :exstring
					 OR atl.cp LIKE :exstring
					 OR atl.localidad LIKE :string
					 OR atl.provincia LIKE :string
					 OR atl.pais LIKE :string
					 OR atl.lfga LIKE :exstring
					 OR atl.lxogade LIKE :exstring')
			->setParameter('exstring', $string)
			->setParameter('string', '%'.$string.'%');
		}
		if (($fnacIni != null) && ($fnacIni != "")){
			$qb = $qb->andWhere('atl.fnac > :fnacini')
			->setParameter('fnacini', $fnacIni->format("Y-m-d"));
		}
	   if (($fnacFin != null) && ($fnacFin != "")){
			$qb = $qb->andWhere('atl.fnac < :fnacfin')
			->setParameter('fnacfin', $fnacFin->format("Y-m-d"));
		}
		$result = $qb->select('atl.id, atl.nombre, atl.apellidos, atl.nick, atl.fnac, atl.sexo, atl.lfga, atl.lxogade')
		->from('EasanlesAtletismoBundle:Atleta', 'atl')
		->orderBy('atl.fnac', 'DESC')
		->getQuery()->getResult();
		
		return $result;
	}
	
}
