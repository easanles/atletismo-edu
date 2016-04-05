<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Config\Definition\Exception\Exception;
use Easanles\AtletismoBundle\Helpers\Helpers;
use Symfony\Component\HttpFoundation\JsonResponse;
use Easanles\AtletismoBundle\Entity\TipoPruebaModalidad;

class InformesController extends Controller {

//##########################################################################
//######################### PANTALLA DE RESULTADOS #########################
//##########################################################################
	
	public function pantallaResultadosAction(Request $request, $rol) {
		$parametros = array("rol" => $rol);
		$repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
		$listaTemps = $repoCom->findTemps($rol);
		$parametros['temps'] = $listaTemps;
		$comsData = array();
		foreach ($listaTemps as $temp){
			$comsData[$temp['temp']] = array('temp' => $temp['temp'], 'coms' => $repoCom->findTempComs($temp['temp'], $rol));
		}
		$parametros['coms'] = $comsData;
		$sidRon = $request->query->get('ron');
		if (($sidRon != null) && ($sidRon != "")){
			$repoRon = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Ronda');
			$ron = $repoRon->find($sidRon);
			if ($ron == null){
				$response = new Response('No existe la ronda con identificador "'.$sidRon.'" <a href="'.$this->generateUrl('homepage').'">Volver</a>');
				$response->headers->set('Refresh', '2; url='.$this->generateUrl('homepage'));
				return $response;
			} else {
				$parametros['selRon'] = $ron;
				$parametros['selPru'] = $ron->getSidPru();
				$parametros['selCom'] = $ron->getSidPru()->getSidCom();
				$parametros['selTemp'] = $ron->getSidPru()->getSidCom()->getTemp();
			}
		} else {
			$sidPru = $request->query->get('pru');
			if (($sidPru != null) && ($sidPru != "")){
				$repoPru = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Prueba');
				$pru = $repoPru->find($sidPru);
				if ($pru == null) {
					$response = new Response('No existe la prueba con identificador "'.$sidPru.'" <a href="'.$this->generateUrl('homepage').'">Volver</a>');
					$response->headers->set('Refresh', '2; url='.$this->generateUrl('homepage'));
					return $response;
				} else {
					$parametros['selPru'] = $pru;
					$parametros['selCom'] = $pru->getSidCom();
					$parametros['selTemp'] = $pru->getSidCom()->getTemp();
				}
			} else {
				$sidCom = $request->query->get('com');
				if (($sidCom != null) && ($sidCom != "")){
					$com = $repoCom->find($sidCom);
					if ($com == null) {
						$response = new Response('No existe la competicion con identificador "'.$sidCom.'" <a href="'.$this->generateUrl('homepage').'">Volver</a>');
						$response->headers->set('Refresh', '2; url='.$this->generateUrl('homepage'));
						return $response;
					} else {
						$parametros['selCom'] = $com;
						$parametros['selTemp'] = $com->getTemp();
					}
				}
			}
		}
		return $this->render('EasanlesAtletismoBundle:Informes:pant_resultados.html.twig', $parametros);
	}
	
	public function obtenerPruebasAction(Request $request, $rol){
		$sidCom = $request->query->get('com');
		if ($rol == "user"){
			$repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
			$com = $repoCom->find($sidCom);
			if (($com == null) || ($com->getEsVisible() == false)){
				return new JsonResponse(['result' => null]);
			}
		}
		$resultados = Helpers::obtenerPruebas($this->getDoctrine(), $sidCom);
		return new JsonResponse([
				'result' => $resultados,
		]);
	}
	
	public function obtenerCartelAction(Request $request, $rol){
		$sidCom = $request->query->get('com');
		$repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
		$com = $repoCom->find($sidCom);
		if (($com != null) && (
				($rol == "admin") || (($rol == "user") && ($com->getEsVisible() == true))
				)){
			$helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
			$cartel = $helper->asset($com, 'cartelFile');
			$nombre = $com->getNombre();
		} else {
			$cartel = null;
			$nombre = "";
		}
		return new JsonResponse([
				'cartel' => $cartel,
				'nombre' => $nombre
		]);
	}
	
	public function obtenerRondasAction(Request $request, $rol){
		$sidPru = $request->query->get('pru');
		if ($rol == "user"){
			$repoPru = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Prueba');
			$pru = $repoPru->find($sidPru);
			if (($pru == null) || ($pru->getSidCom()->getEsVisible() == false)){
				return new JsonResponse(['result' => null]);
			}
		}
		$repoRon = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Ronda');
		$listaRon = $repoRon->findBy(array("sidPru" => $sidPru));
		$result = array();
		foreach($listaRon as $ron){
			$result[] = array("sid" => $ron->getSid(),
					 "id" => $ron->getId(),
					 "num" => $ron->getNum(),
					 "nombre" => $ron->getNombre());
		}
		return new JsonResponse([
				'result' => $result,
		]);
	}
	
	public function obtenerTablaResultadosAction(Request $request, $rol){
		$sidRon = $request->query->get('ron');
		$repoRon = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Ronda');
		$ron = $repoRon->find($sidRon);
		if ($ron == null) return new Response("No existe la ronda con identificador ".$sidRon);
		if (($rol == "user") && ($ron->getSidPru()->getSidCom()->getEsVisible() == false))
			   return new Response("Acceso denegado (competición oculta)");
		$parametros = array("sidRon" => $sidRon, "rol" => $rol);
		$tprf = $ron->getSidPru()->getSidTprm()->getSidTprf();
		$unidades = $tprf->getUnidades();
		$parametros['unidades'] = $unidades;
		switch ($unidades){
			case "segundos": $orden = "ASC"; break;
			case "metros": $orden = "DESC"; break;
			case "puntosdesc": $orden = "DESC"; break;
		   case "puntosasc": $orden = "ASC"; break;
		   default: $orden = "ASC";
		}
		$numIntentos = $tprf->getNumint();
		$parametros["numIntentos"] = $numIntentos;
		$repoInt = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Intento');
		$entradas = $repoInt->findBestMarcas($sidRon, $orden);
		$parametros["tablaPrincipal"] = $entradas;
		$user = $this->getUser();
		if ($user != null){
			$atl = $user->getIdAtl();
		} else $atl = null;
		if ($atl != null){
			$parametros['destacarAtl'] = $atl;
		}
		$repoCon = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Config');
		$parametros['leyenda'] = $repoCon->findOneBy(array("clave" => "leyenda"))->getValor();
		return $this->render('EasanlesAtletismoBundle:Informes:tabla_resultados.html.twig', $parametros);
	}
	
	public function mostrarIntentosAction(Request $request, $rol){
		$idAtl = $request->query->get('atl');
		$repoAtl = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
		$atl = $repoAtl->find($idAtl);
		if ($atl == null) return new Response("No existe el atleta con identificador ".$idAtl);
		$sidRon = $request->query->get('ron');
		$repoRon = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Ronda');
		$ron = $repoRon->find($sidRon);
		if ($ron == null) return new Response("No existe la ronda con identificador ".$sidRon);
		if (($rol == "user") && ($ron->getSidPru()->getSidCom()->getEsVisible() == false))
			   return new Response("Acceso denegado (competición oculta)");
		$repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
		$entornos = $repoCom->getComEntornos($ron->getSidPru()->getSidCom()->getSid());
		$parametros = array("atl" => $atl, "ron" => $ron, "rol" => $rol, "entornos" => $entornos);
		$parametros['unidades'] = $ron->getSidPru()->getSidTprm()->getSidTprf()->getUnidades();
		$repoInt = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Intento');
		$listaIntentos = $repoInt->findMarcaIntentos($idAtl, $sidRon);
		$datos = array();
		$marcaActual = null;
		$intentosMarca = array();
		foreach($listaIntentos as $int){
			if ($marcaActual == null) $marcaActual = $int['marca'];
			if ($marcaActual != $int['marca']){
				$datos[] = array("marca" => $marcaActual, "intentos" => $intentosMarca);
				$marcaActual = $int['marca'];
				$intentosMarca = array();
			}
			$intentosMarca[] = $int;
		}
		$datos[] = array("marca" => $marcaActual, "intentos" => $intentosMarca);
		$parametros['datos'] = $datos;
		return $this->render('EasanlesAtletismoBundle:Informes:hist_intentos.html.twig', $parametros);
	}
	
//####################################################################
//######################### RECORDS DEL CLUB #########################
//####################################################################	
	
	public function pantallaRecordsAction(Request $request, $tipo, $rol){
		$parametros = array("tipo" => $tipo, "rol" => $rol);
		$user = $this->getUser();
		if ($user != null){
		   $atl = $user->getIdAtl();
		} else $atl = null;
		if ($tipo == 2){
    	   if ($user == null){
    	     return $this->redirect($this->generateUrl("login"));
    	   }
    	   if ($atl == null){
    	 	   $response = new Response('El usuario no tiene un atleta asociado <a href="'.$this->generateUrl('homepage').'">Volver</a>');
    	 	   $response->headers->set('Refresh', '2; url='.$this->generateUrl('homepage'));
    	 	   return $response;
    	   } else {
    	   	$parametros['atl'] = $user->getIdAtl();
    	   }
		} else {
			if ($atl != null){
				$parametros['destacarAtl'] = $atl;
			}
		}
		$selTemp = $request->query->get("t");
		$parametros['selTemp'] = $selTemp;		
		$repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
		$listaTemps = $repoCom->findTemps($rol);
		$parametros['temps'] = $listaTemps;
		$repoTprm = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:TipoPruebaModalidad');
		$listaEntornos = $repoTprm->findAllEntornos();
		$parametros["entornos"] = $listaEntornos;
		$repoInt = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Intento');
		$tablas = array();
		foreach ($listaEntornos as $entorno){
			$tabla = array();
			$listaTprfs = $repoTprm->findUsedTprfsFor($entorno['entorno']);
			foreach($listaTprfs as $tprf){
				if ($tipo == 2) $query = $repoInt->findRecordFor($tipo, $entorno['entorno'], $tprf, $rol, $user->getIdAtl()->getId(), $selTemp);
				else $query = $repoInt->findRecordFor($tipo, $entorno['entorno'], $tprf, $rol, null, $selTemp);
				if ($query != null){
					$datos = array("premios" => $query[0]['premios'],
							"marca" => $query[0]['marca'],
							"idAtl" => $query[0]['idAtl'],
							"atleta" => $query[0]['apellidos'].", ".$query[0]['nombre'],
							"categoria" => $query[0]['categoria'],
							"fecha" => $query[0]['fecha'],
							"sede" => $query[0]['sede'],
							"unidades" => $tprf['unidades']
					);
				} else {
					$datos = array("premios" => "",
							"marca" => "",
							"idAtl" => null,
							"atleta" => "",
							"categoria" => "",
							"fecha" => "",
							"sede" => "",
							"unidades" => null
					);
				}
				$datos['prueba'] = $tprf['nombre'];
				$tabla[] = $datos;
			} 
			$tablas[] = array("entorno" => $entorno, "tabla" => $tabla);
		}
		$parametros["tablas"] = $tablas;
		
		return $this->render('EasanlesAtletismoBundle:Informes:pant_records.html.twig', $parametros);
	}

//##########################################################################
//######################### INFORMES DE ASISTENCIA #########################
//##########################################################################

   public function pantallaAsistenciaAction(Request $request){
   	$parametros = array();
   	$repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
   	$sidCom = $request->query->get('com');
		if (($sidCom != null) && ($sidCom != "")){
		   $com = $repoCom->find($sidCom);
			if ($com == null) {
				$response = new Response('No existe la competicion con identificador "'.$sidCom.'" <a href="'.$this->generateUrl('homepage').'">Volver</a>');
				$response->headers->set('Refresh', '2; url='.$this->generateUrl('homepage'));
				return $response;
			} else {
				$parametros['selCom'] = $com;
				$parametros['selTemp'] = $com->getTemp();
			}
		}
   	$listaTemps = $repoCom->findTemps("admin");
   	$parametros['temps'] = $listaTemps;
   	$comsData = array();
   	foreach ($listaTemps as $temp){
   		$comsData[$temp['temp']] = array('temp' => $temp['temp'], 'coms' => $repoCom->findTempComs($temp['temp'], "admin"));
   	}
   	$parametros['coms'] = $comsData;
   	return $this->render('EasanlesAtletismoBundle:Informes:pant_asistencia.html.twig', $parametros);
   }
   
   public function obtenerParticipacionesAction(Request $request){
   	$sidCom = $request->query->get('com');
   	if (($sidCom == null) || ($sidCom == "")){
   		return new Response("No se ha recibido el parámetro necesario");
   	}
   	$repoPar = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Participacion');
   	$listaPar = $repoPar->findOrderedBy($sidCom);
   	$repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
   	$listaAtlIns = $repoCom->findAtletasIns($sidCom);
   	foreach ($listaPar as &$par){
   		$par['categoria'] = Helpers::getAtlCurrentCat($this->getDoctrine(), $par['idAtl']);
   		foreach($listaAtlIns as $key => $atl){
   			if ($atl['idAtl'] == $par['idAtl']){
   				unset($listaAtlIns[$key]);
   				break;
   			}
   		}
   	}
   	$repoAtl = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
   	$listaIns = array();
   	foreach ($listaAtlIns as $atl){
   		$atlObj = $repoAtl->find($atl['idAtl']);
   		$listaIns[] = array(
   				"idAtl" => $atl['idAtl'],
   				"nombre" => $atlObj->getNombre(),
   				"apellidos" => $atlObj->getApellidos(),
   				"categoria" => Helpers::getAtlCurrentCat($this->getDoctrine(), $atl['idAtl'])
   		);
   	}
   	return $this->render('EasanlesAtletismoBundle:Informes:tabla_asistencia.html.twig',
   			 array("pars" => $listaPar, "inss" => $listaIns, "sidCom" => $sidCom));
   }
   
//##########################################################################
//############################ PAGOS PENDIENTES ############################
//##########################################################################
   
   private function makeComsArray($listaIns){
   	$comsArray = array();
   	$currentSidCom = null;
   	$atlsArray = array();
   	$currentIdAtl = null;
   	$inssArray = array();
   	$costeTotal = 0;
   	$costeCom = 0;
   	$costeAtl = 0;
   	foreach ($listaIns as $key => $ins){
   		if (($currentSidCom == null) || ($ins['sidCom'] != $currentSidCom)){
   			$currentSidCom = $ins['sidCom'];
   			if (count($inssArray) > 0){
   				$atlsArray[] = array(
   						"id" => $listaIns[$key-1]['idAtl'],
   						"apellidos" => $listaIns[$key-1]['apellidos'],
   						"nombre" => $listaIns[$key-1]['nombreatl'],
   						"costeAtl" => $costeAtl,
   						"inss" => $inssArray
   				);
   				$inssArray = array();
   				$costeAtl = 0;
   			}
   			if (count($atlsArray) > 0){
   				$comsArray[] = array(
   						"sid" => $listaIns[$key-1]['sidCom'],
   						"nombre" => $listaIns[$key-1]['nombrecom'],
   						"temp" => $listaIns[$key-1]['temp'],
   						"cartel" => $listaIns[$key-1]['cartel'],
   						"costeCom" => $costeCom,
   						"atls" => $atlsArray
   				);
   				$costeCom = 0;
   				$atlsArray = array();
   			}
   		}
   		if (($currentIdAtl == null) || ($ins['idAtl'] != $currentIdAtl)){
   			$currentIdAtl = $ins['idAtl'];
   			if (count($inssArray) > 0){
   				$atlsArray[] = array(
   						"id" => $listaIns[$key-1]['idAtl'],
   						"apellidos" => $listaIns[$key-1]['apellidos'],
   						"nombre" => $listaIns[$key-1]['nombreatl'],
   						"costeAtl" => $costeAtl,
   						"inss" => $inssArray
   				);
   				$inssArray = array();
   				$costeAtl = 0;
   			}
   		}
   		$nombrePrueba = $ins['nombretprf'];
   		if ($ins['sexo'] == 0) $nombrePrueba = $nombrePrueba.", masculino";
   		else if ($ins['sexo'] == 1) $nombrePrueba = $nombrePrueba.", femenino";
   		$nombrePrueba = $nombrePrueba.". ".$ins['entorno'];
   		$inssArray[] = array(
   				"sid" => $ins['sid'],
   				"coste" => $ins['coste'],
   				"fecha" => $ins['fecha'],
   				"prueba" => $nombrePrueba
   		);
   		$costeTotal = $costeTotal + $ins['coste'];
   		$costeCom = $costeCom + $ins['coste'];
   		$costeAtl = $costeAtl + $ins['coste'];
   	}
   	if (count($inssArray) > 0){
   		$atlsArray[] = array(
   				"id" => $listaIns[count($listaIns)-1]['idAtl'],
   				"apellidos" => $listaIns[count($listaIns)-1]['apellidos'],
   				"nombre" => $listaIns[count($listaIns)-1]['nombreatl'],
   				"costeAtl" => $costeAtl,
   				"inss" => $inssArray
   		);
   	}
   	if (count($atlsArray) > 0){
   		$comsArray[] = array(
   				"sid" => $listaIns[count($listaIns)-1]['sidCom'],
   				"nombre" => $listaIns[count($listaIns)-1]['nombrecom'],
   				"temp" => $listaIns[count($listaIns)-1]['temp'],
   				"cartel" => $listaIns[count($listaIns)-1]['cartel'],
   				"costeCom" => $costeCom,
   				"atls" => $atlsArray
   		);
   	}
   	return array($comsArray, $costeTotal);
   }
   
   public function pagosPendientesAction(){
   	$repoIns = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Inscripcion');
   	$listaIns = $repoIns->findInsPendientes();
      $comsArray = $this->makeComsArray($listaIns);
   	return $this->render('EasanlesAtletismoBundle:Informes:pant_pagos.html.twig', array(
   			'coms' => $comsArray[0], 'count' => count($listaIns), 'costeTotal' => $comsArray[1]));
   }
   
   public function marcarPagadoAction(Request $request){
   	$selIns = $request->request->get('selIns');
   	if ($selIns == null) return new Response("No se ha recibido el parámetro necesario");
   	$repoIns = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Inscripcion');
   	$em = $this->getDoctrine()->getManager();
   	foreach($selIns as $sidIns){
   		$ins = $repoIns->find($sidIns);
   		if ($ins != null){
   			$ins->setEstado("Pagado");
   		   $em->persist($ins);
   		}
   	}
   	$em->flush();
   	return new Response("OK");
   }
   
//##########################################################################
//################################ INGRESOS ################################
//##########################################################################

   public function pantallaIngresosAction(Request $request){
   	$temp = $request->query->get('t');
   	if (($temp == null) || ($temp == "")){
   		$temp = Helpers::getCurrentTemp($this->getDoctrine());
   	}
   	$parametros = array("selTemp" => $temp);
   	$repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
   	$listaTemps = $repoCom->findTemps("admin");
   	$parametros['temps'] = $listaTemps;
   	$repoIns = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Inscripcion');
   	$listaIns = $repoIns->findInsPagados($temp);
   	$comsArray = $this->makeComsArray($listaIns);
   	$parametros['coms'] = $comsArray[0];
   	$parametros['costeTotal'] = $comsArray[1];
   	return $this->render('EasanlesAtletismoBundle:Informes:pant_ingresos.html.twig', $parametros);
   }

}
