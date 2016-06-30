<?php

# Copyright (c) 2016 Eduardo Alonso Sanlés
# Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php

namespace Easanles\AtletismoBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Easanles\AtletismoBundle\EasanlesAtletismoBundle;
use Symfony\Component\Config\Definition\Exception\Exception;

class ParticipacionRepository extends EntityRepository {
	
	public function findOrderedBy($sidCom) {
		return $this->getEntityManager()
		->createQuery('SELECT par.sid, IDENTITY(par.idAtl) AS idAtl, atl.apellidos, atl.nombre, par.dorsal, par.asisten 
				 FROM EasanlesAtletismoBundle:Participacion par
				 JOIN par.idAtl atl 
				 WHERE IDENTITY(par.sidCom) LIKE :sidcom
				 ORDER BY atl.apellidos ASC, atl.nombre ASC')
		->setParameter("sidcom", $sidCom)
		->getResult();
	}
	
}
