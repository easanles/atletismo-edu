<?php

namespace Easanles\AtletismoBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Easanles\AtletismoBundle\EasanlesAtletismoBundle;
use Symfony\Component\Config\Definition\Exception\Exception;

class InscripcionRepository extends EntityRepository {
	public function findAllFor($sidCom){
		return $this->getEntityManager()
		->createQuery('SELECT ins.sid, ins.origen, ins.fecha, ins.estado, ins.coste, ins.codGrupo, IDENTITY(ins.idatl), IDENTITY(ins.sidpru)
				FROM EasanlesAtletismoBundle:Inscripcion ins
				JOIN ins.sidPru pru
				JOIN pru.sidCom com
				WHERE com.sid LIKE :sidcom
				GROUP BY ins.idAtl')
		->setParameter("sidcom", $sidCom)
		->getResult();
	}
}
