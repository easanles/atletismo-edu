<?php

namespace Easanles\AtletismoBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Easanles\AtletismoBundle\EasanlesAtletismoBundle;
use Symfony\Component\Config\Definition\Exception\Exception;

class AtletaRepository extends EntityRepository {
	public function findAllOrdered()	{
		$result = $this->getEntityManager()
		->createQuery('SELECT atl.id, atl.nombre, atl.apellidos, atl.nick, atl.fnac, atl.sexo, atl.lfga, atl.lxogade FROM EasanlesAtletismoBundle:Atleta atl ORDER BY atl.fnac DESC')
		->getResult();
		return $result;
	}
	
	public function searchByParameters() {	
	}
	
}
