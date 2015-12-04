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
		->createQuery('SELECT ins.sid AS sid, ins.origen, ins.fecha, ins.estado, ins.coste, ins.codGrupo,
				 pru.sid AS sidPru, tprf.nombre, tprm.sexo, tprm.entorno 
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
	
	public function countInsForPru($sidPru){
		$query = $this->getEntityManager()
		->createQuery('SELECT ins.sid AS sid
				FROM EasanlesAtletismoBundle:Inscripcion ins
				WHERE IDENTITY(ins.sidPru) LIKE :sidpru')
					->setParameter("sidpru", $sidPru)
					->getResult();
		return count($query);
	}
	
	public function findInsForPru($sidPru){
		return $this->getEntityManager()
		->createQuery('SELECT atl.apellidos, atl.nombre, atl.nick
				FROM EasanlesAtletismoBundle:Inscripcion ins
				JOIN ins.idAtl atl
				WHERE IDENTITY(ins.sidPru) LIKE :sidpru')
					->setParameter("sidpru", $sidPru)
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
