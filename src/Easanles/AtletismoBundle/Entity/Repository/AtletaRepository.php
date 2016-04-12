<?php

namespace Easanles\AtletismoBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Easanles\AtletismoBundle\EasanlesAtletismoBundle;
use Symfony\Component\Config\Definition\Exception\Exception;
use Easanles\AtletismoBundle\Helpers\Helpers;

class AtletaRepository extends EntityRepository {
	public function findAllOrdered($alta, $from, $numResultados) {
		$altaVal = $alta == true ? 1 : 0;
		return $this->getEntityManager()
		->createQuery('SELECT atl.id, atl.nombre, atl.apellidos, atl.nick, atl.fnac, atl.sexo, atl.lfga, atl.lxogade, usu.nombre AS nombreUsu, usu.rol
				 FROM EasanlesAtletismoBundle:Atleta atl
				 LEFT JOIN atl.nombreUsu usu
				 WHERE atl.esAlta = :alta
				 ORDER BY atl.fnac DESC, atl.id ASC')
		->setParameter("alta", $altaVal)
		->setFirstResult($from)
		->setMaxResults($numResultados)
		->getResult();
	}
	
	public function searchByParameters($fnacIni, $fnacFin, $string, $alta, $from, $numResultados) {
		$altaVal = $alta == true ? 1 : 0;
		$qb = $this->getEntityManager()->createQueryBuilder('atl')->where('atl.esAlta = :alta')->setParameter('alta', $altaVal);
		if (($string != null) || ($string != "")){
			$qb = $qb->andWhere('atl.id = :exstring
					 OR atl.nombre LIKE :string
					 OR atl.apellidos LIKE :string
					 OR atl.nick LIKE :string
					 OR atl.dni LIKE :exstring
					 OR atl.tipo LIKE :exstring
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
		$result = $qb->select('atl.id, atl.nombre, atl.apellidos, atl.nick, atl.fnac, atl.sexo, atl.lfga, atl.lxogade, usu.nombre AS nombreUsu, usu.rol')
		->from('EasanlesAtletismoBundle:Atleta', 'atl')
		->leftJoin('atl.nombreUsu', 'usu')
		->orderBy('atl.fnac', 'DESC')
		->orderBy('atl.id', 'ASC')
		->getQuery()
	   ->setFirstResult($from)
		->setMaxResults($numResultados)
		->getResult();
		return $result;
	}
	
	public function findTipos(){
		return $this->getEntityManager()
		->createQuery('SELECT atl.tipo FROM EasanlesAtletismoBundle:Atleta atl WHERE atl.tipo IS NOT NULL GROUP BY atl.tipo')
		->getResult();
	}
	
}
