<?php

# Copyright (c) 2016 Eduardo Alonso Sanlés
# Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php

namespace Easanles\AtletismoBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Easanles\AtletismoBundle\EasanlesAtletismoBundle;
use Symfony\Component\Config\Definition\Exception\Exception;

class TipoPruebaFormatoRepository extends EntityRepository {
	public function findAllOrdered()	{
		return $this->getEntityManager()
		->createQuery('SELECT tprf.sid, tprf.nombre, tprf.unidades, tprf.numint
				 FROM EasanlesAtletismoBundle:TipoPruebaFormato tprf
				 WHERE tprf.esCuota = 0
				 ORDER BY tprf.nombre ASC')
		->getResult();
	}
	
	public function checkData($nombre){
		$tprfCheck = $this->getEntityManager()
		->createQuery(
			'SELECT tprf.nombre FROM EasanlesAtletismoBundle:TipoPruebaFormato tprf WHERE tprf.nombre = :nombre ')
		->setParameter('nombre', $nombre)
		->getResult();
      return ($tprfCheck != null);
	}
}
