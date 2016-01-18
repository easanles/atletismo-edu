<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Config\Definition\Exception\Exception;
use Easanles\AtletismoBundle\Helpers\Helpers;
use Symfony\Component\HttpFoundation\JsonResponse;

class InformesController extends Controller {
	
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
		$parametros = array();
		$tprf = $ron->getSidPru()->getSidTprm()->getSidTprf();
		switch ($tprf->getUnidades()){
			case "segundos": $orden = "ASC"; break;
			case "metros": $orden = "DESC"; break;
			case "puntosdesc": $orden = "DESC"; break;
		   case "puntosasc": $orden = "ASC"; break;
		   default: $orden = "ASC";
		}
		$numIntentos = $tprf->getNumint();
		$parametros["numIntentos"] = $numIntentos;
		$repoInt = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Intento');
		$entradas = $repoInt->findAllEntriesFor($sidRon, $orden);
		$parametros["tablaPrincipal"] = $entradas; //debug
		
		return $this->render('EasanlesAtletismoBundle:Informes:tabla_resultados.html.twig', $parametros);
	}
	
}
