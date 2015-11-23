<?php

namespace Easanles\AtletismoBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Easanles\AtletismoBundle\EasanlesAtletismoBundle;
use Symfony\Component\Config\Definition\Exception\Exception;

class InscripcionRepository extends EntityRepository {
	public function findAllFor($sidCom){
		return $this->getEntityManager()
		->createQuery('SELECT ins.sid, ins.origen, ins.fecha, ins.estado, ins.coste, ins.codGrupo, IDENTITY(ins.idAtl), IDENTITY(ins.sidPru)
				FROM EasanlesAtletismoBundle:Inscripcion ins
				JOIN ins.sidPru pru
				JOIN pru.sidCom com
				WHERE com.sid LIKE :sidcom
				GROUP BY ins.idAtl')
		->setParameter("sidcom", $sidCom)
		->getResult();
	}
	
	public function findForAtl($sidCom, $idAtl){
		return $this->getEntityManager()
		->createQuery('SELECT ins.sid, ins.origen, ins.fecha, ins.estado, ins.coste, ins.codGrupo,
				 pru.sid, tprf.nombre, tprm.sexo, tprm.entorno 
				FROM EasanlesAtletismoBundle:Inscripcion ins
				JOIN ins.sidPru pru
				JOIN pru.sidTprm tprm
				JOIN tprm.sidTprf tprf
				WHERE IDENTITY(pru.sidCom) LIKE :sidcom
				AND IDENTITY(ins.idAtl) LIKE :idatl')
		->setParameter("sidcom", $sidCom)
		->setParameter("idatl", $idAtl)
		->getResult();
	}
	
	public function maxCodGrupo(){
		$query = $this->getEntityManager()
		->createQuery('SELECT ins.codGrupo FROM EasanlesAtletismoBundle:Inscripcion ins GROUP BY ins.codGrupo ')
		->getResult();
		if (count($query) == 0) return 0;
		else return max($query)['codGrupo'];
	}
}
