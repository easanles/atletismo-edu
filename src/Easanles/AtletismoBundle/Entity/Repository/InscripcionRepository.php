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
		->createQuery('SELECT atl.id, atl.apellidos, atl.nombre, atl.nick
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
	
	public function findInsPendientes(){
		return $this->getEntityManager()
		->createQuery("SELECT ins.sid, ins.fecha, ins.coste, IDENTITY(ins.idAtl) AS idAtl, atl.apellidos, atl.nombre AS nombreatl, IDENTITY(ins.sidPru) AS sidPru, IDENTITY(pru.sidCom) AS sidCom, com.nombre AS nombrecom, com.temp, com.cartel, tprm.sexo, tprm.entorno, tprf.nombre AS nombretprf, cat.nombre AS nombrecat
				 FROM EasanlesAtletismoBundle:Inscripcion ins
				 JOIN ins.idAtl atl
				 JOIN ins.sidPru pru
				 JOIN pru.sidCom com
				 JOIN pru.sidTprm tprm
				 JOIN tprm.sidTprf tprf
				 JOIN pru.idCat cat
				 WHERE ins.estado LIKE 'Pendiente'
				 ORDER BY com.sid, atl.id ")
		->getResult();
	}
	
	public function findInsGrupal($codigo){
		return $this->getEntityManager()
		->createQuery('SELECT atl.id, atl.apellidos, atl.nombre, atl.nick
				FROM EasanlesAtletismoBundle:Inscripcion ins
				JOIN ins.idAtl atl
				WHERE ins.codGrupo LIKE :codgrupo')
		->setParameter("codgrupo", $codigo)
		->getResult();
	}
	
	public function findInsPagados($temp){
		return $this->getEntityManager()
		->createQuery("SELECT ins.sid, ins.fecha, ins.coste, IDENTITY(ins.idAtl) AS idAtl, atl.apellidos, atl.nombre AS nombreatl, IDENTITY(ins.sidPru) AS sidPru, IDENTITY(pru.sidCom) AS sidCom, com.nombre AS nombrecom, com.temp, com.cartel, tprm.sexo, tprm.entorno, tprf.nombre AS nombretprf, cat.nombre AS nombrecat
				 FROM EasanlesAtletismoBundle:Inscripcion ins
				 JOIN ins.idAtl atl
				 JOIN ins.sidPru pru
				 JOIN pru.sidCom com
				 JOIN pru.sidTprm tprm
				 JOIN tprm.sidTprf tprf
				 JOIN pru.idCat cat
				 WHERE ins.estado LIKE 'Pagado' AND com.temp = :temp AND ins.coste > 0
				 ORDER BY com.sid, atl.id ")
	   ->setParameter("temp", $temp)
		->getResult();
	}
	
	public function findAtlHistoric($idAtl, $temp){
		$sql = "SELECT tprm.entorno, IDENTITY(pru.sidCom) AS sidCom, com.nombre AS nombreCom, com.temp, com.fecha, com.sede, IDENTITY(ins.sidPru) AS sidPru, tprf.nombre AS nombreTprf, tprf.unidades, cat.nombre AS nombreCat
				FROM EasanlesAtletismoBundle:Inscripcion ins
				JOIN ins.sidPru pru
				JOIN pru.sidCom com
				JOIN pru.sidTprm tprm
				JOIN tprm.sidTprf tprf
				JOIN pru.idCat cat
				WHERE ins.idAtl = :idatl";
	   if (($temp != null) && ($temp != "")){
			$sql = $sql." AND com.temp = :temp";
		}
	   $sql = $sql." ORDER BY tprm.entorno ASC, com.fecha ASC, com.sid ASC, pru.sid ASC";
	   $query = $this->getEntityManager()->createQuery($sql);
	   if (($temp != null) && ($temp != "")){
	   	$query = $query->setParameter("temp", $temp);
	   }
		return $query->setParameter("idatl", $idAtl)->getResult();
	}
	
	public function findAtlTemps($idAtl){
		return $this->getEntityManager()
		->createQuery("SELECT com.temp
				 FROM EasanlesAtletismoBundle:Inscripcion ins
				 JOIN ins.sidPru pru
				 JOIN pru.sidCom com
				 WHERE ins.idAtl = :idatl
				 GROUP BY com.temp
				 ORDER BY com.temp DESC")
		->setParameter("idatl", $idAtl)
		->getResult();
	}
}
