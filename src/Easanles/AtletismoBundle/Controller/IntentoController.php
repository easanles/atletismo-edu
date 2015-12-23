<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Easanles\AtletismoBundle\Helpers\Helpers;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Config\Definition\Exception\Exception;
use Easanles\AtletismoBundle\Entity\TipoPruebaModalidad;
use Symfony\Component\HttpFoundation\JsonResponse;
use Easanles\AtletismoBundle\Entity\Intento;
use Easanles\AtletismoBundle\Form\Type\IntType;
use Easanles\AtletismoBundle\Form\Type\IntTypeGroup;

class IntentoController extends Controller {
	
	public function pantallaIntentosAction(Request $request) {
		$sidCom = $request->query->get('com');
		$repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
		$parametros = array();
		$getPruebas = false;
		if (($sidCom != null) && ($sidCom != "")){
			$com = $repoCom->find($sidCom);
			if ($com == null){
				$response = new Response('No existe la competicion con el identificador "'.$sidCom.'" <a href="'.$this->generateUrl('listado_competiciones').'">Volver</a>');
				$response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_competiciones'));
				return $response;
			}
			else {
				$parametros["selCom"] = $sidCom;
				$getPruebas = true;
			}
		}
		$temp = Helpers::getTempYear($this->getDoctrine(), date('d'), date('m'), date('Y'));
		$parametros["currentTemp"] = $temp;
		$comDisponibles = $repoCom->findTempComs($temp);
		$parametros["coms"] = $comDisponibles;
		if ($getPruebas){

		}
      return $this->render('EasanlesAtletismoBundle:Intento:pant_intento.html.twig', $parametros);
	}
	
	private function obtenerPruebas($sidCom){
		$repoPru = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Prueba');
		$repoTprm = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:TipoPruebaModalidad');
		$repoCat = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Categoria');
		$prus = $repoPru->findAllOrderedFor($sidCom);
		$result = array();
		$cats = array();
		$currentTprm = null;
		foreach($prus as $pru){
			if ($currentTprm == null){
				$currentTprm = $pru['tprm'];
			} else if ($pru['tprm'] != $currentTprm){
				$tprm = $repoTprm->find($currentTprm);
				$sexo = ($tprm->getSexo() == 0) ? "Masculino" : "Femenino";
				$nombre = $tprm->getSidTprf()->getNombre().". ".$sexo.", ".$tprm->getEntorno();
				$result[] = array("tprm" => $nombre, "cats" => $cats);
				$currentTprm = $pru['tprm'];
				$cats = array();
			}
			$cats[] = array("sid" => $pru['sid'], "nombre" => $repoCat->find($pru['cat'])->getNombre());
		}
		if (count($prus) > 0 ){
			$tprm = $repoTprm->find($currentTprm);
			$sexo = ($tprm->getSexo() == 0) ? "Masculino" : "Femenino";
			$nombre = $tprm->getSidTprf()->getNombre().". ".$sexo.", ".$tprm->getEntorno();
			$result[] = array("tprm" => $nombre, "cats" => $cats);
		}
		return $result;
	}
	
	public function obtenerPruebasAction(Request $request){
		$sidCom = $request->query->get('com');
	   $resultados = $this->obtenerPruebas($sidCom);
	   return new JsonResponse([
	   		'result' => $resultados
	   ]);
	}
	
	public function obtenerAtletasInscritosAction(Request $request){
		$repoIns = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Inscripcion');
		$sidPru = $request->query->get('pru');
		$atls = $repoIns->findInsForPru($sidPru);
      return $this->render('EasanlesAtletismoBundle:Intento:sel_atleta.html.twig', array(
      		"atletas" => $atls
      ));
	}
	
	public function obtenerRondasAction(Request $request){
		$sidPru = $request->query->get('pru');
		$idAtl = $request->query->get('atl');
		$repoPru = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Prueba');
		$pru = $repoPru->find($sidPru);
		if ($pru == null) return new Response("No existe la prueba con identificador ".$sidPru);
		else $unidades = $pru->getSidTprm()->getSidTprf()->getUnidades();
		$repoRon = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Ronda');
		$rondas = $repoRon->findAllFor($sidPru);
		$repoInt = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Intento');
		foreach($rondas as &$ron){
			$ron['marca'] = $repoInt->getMarcaFor($idAtl, $ron['sid']);
			$ron['unidades'] = $unidades;
		}
		
      return $this->render('EasanlesAtletismoBundle:Intento:sel_ronda.html.twig', array("rondas" => $rondas, "selAtl" => $idAtl));
	}
	
	public function crearIntentoAction(Request $request){
		$sidRon = $request->query->get('ron');
		$idAtl = $request->query->get('atl');
		$repoRon = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Ronda');
		$ron = $repoRon->find($sidRon);
		if ($ron == null){
			return new JsonResponse([
					'success' => false,
					'message' => "No existe la prueba con identificador ".$sidRon
			]);
		}
		$repoRon = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
		$atl = $repoRon->find($idAtl);
		if ($atl == null){
			return new JsonResponse([
					'success' => false,
					'message' => "No existe el atleta con identificador ".$idAtl
			]);
		}
		$numIntentos = $ron->getSidPru()->getSidTprm()->getSidTprf()->getNumint();
		$arrayForms = array();
		for($i = 1; $i <= $numIntentos; $i++){
			$int = new Intento();
			$int->setSidRon($ron);
			$int->setIdAtl($atl);
			$int->setOrigen("admin"); //TODO: nombre de usuario
			$int->setNum($i);
			$arrayForms[] = $this->createForm(new IntType(), $int);
		}
		
		$form = $this->createForm(new IntTypeGroup($arrayForms));
		$form->handleRequest($request);
		 
		if ($form->isValid()) {
			try {
				
				// ···
				//$em = $this->getDoctrine()->getManager();
				//$em->persist($int);
				//$em->flush();
			} catch (\Exception $e) {
				$exception = $e->getMessage();
				return new JsonResponse([
						'success' => false,
						'message' => $this->render('EasanlesAtletismoBundle:Intento:form_intento.html.twig',
								array('form' => $form->createView(), 'mode' => 'new', 'exception' => $exception))->getContent()
				]);
			}
			return new JsonResponse([
					'success' => true,
					'message' => "OK"
			]);
		}
		return new JsonResponse([
				'success' => false,
				'message' => $this->render('EasanlesAtletismoBundle:Intento:form_intento.html.twig',
						array('form' => $form->createView(), 'mode' => 'new'))->getContent()
		]);
	}
}
