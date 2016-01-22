<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Config\Definition\Exception\Exception;
use Easanles\AtletismoBundle\Helpers\Helpers;
use Symfony\Component\HttpFoundation\JsonResponse;

class InformesController extends Controller {

//##########################################################################
//######################### PANTALLA DE RESULTADOS #########################
//##########################################################################
	
	public function pantallaResultadosAction(Request $request) {
		$parametros = array();
		$repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
		$listaTemps = $repoCom->findTemps();
		$parametros['temps'] = $listaTemps;
		$comsData = array();
		foreach ($listaTemps as $temp){
			$comsData[$temp['temp']] = array('temp' => $temp['temp'], 'coms' => $repoCom->findTempComs($temp['temp']));
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
	
	public function obtenerPruebasAction(Request $request){
		$sidCom = $request->query->get('com');
		$resultados = Helpers::obtenerPruebas($this->getDoctrine(), $sidCom);
		return new JsonResponse([
				'result' => $resultados,
		]);
	}
	
	public function obtenerCartelAction(Request $request){
		$sidCom = $request->query->get('com');
		$repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
		$com = $repoCom->find($sidCom);
		if ($com != null){
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
	
	public function obtenerRondasAction(Request $request){
		$sidPru = $request->query->get('pru');
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
	
	public function obtenerTablaResultadosAction(Request $request){
		$sidRon = $request->query->get('ron');
		$repoRon = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Ronda');
		$ron = $repoRon->find($sidRon);
		if ($ron == null) return new Response("No existe la ronda con identificador ".$sidRon);
		$parametros = array("sidRon" => $sidRon);
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
		
		return $this->render('EasanlesAtletismoBundle:Informes:tabla_resultados.html.twig', $parametros);
	}
	
	public function mostrarIntentosAction(Request $request){
		$idAtl = $request->query->get('atl');
		$repoAtl = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
		$atl = $repoAtl->find($idAtl);
		if ($atl == null) return new Response("No existe el atleta con identificador ".$idAtl);
		$sidRon = $request->query->get('ron');
		$repoRon = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Ronda');
		$ron = $repoRon->find($sidRon);
		if ($ron == null) return new Response("No existe la ronda con identificador ".$sidRon);
		$parametros = array("atl" => $atl, "ron" => $ron);
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
	
	public function pantallaRecordsAction($sexo){
		return $this->render('EasanlesAtletismoBundle:Informes:pant_records.html.twig', array("sexo" => $sexo));
	}
}
