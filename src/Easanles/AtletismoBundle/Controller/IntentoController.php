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
use Doctrine\Common\Collections\ArrayCollection;

class IntentoController extends Controller {
	
	public function pantallaIntentosAction(Request $request) {
		$parametros = array();
		$sidPru = $request->query->get('pru');
		if (($sidPru != null) && ($sidPru != "")){
			$repoPru = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Prueba');
			$pru = $repoPru->find($sidPru);
			if ($pru == null){
				$response = new Response('No existe la prueba con el identificador "'.$sidPru.'" <a href="'.$this->generateUrl('listado_competiciones').'">Volver</a>');
				$response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_competiciones'));
				return $response;
			} else {
				$sidCom = $pru->getSidCom()->getSid();
				$parametros['selPru'] = $sidPru;
				$idAtl = $request->query->get('atl');
				if (($idAtl != null) && ($idAtl != "")){
					$parametros['selAtl'] = $idAtl;
				}
			}
		} else {
			$sidCom = $request->query->get('com');
		}
		$repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
		if (($sidCom != null) && ($sidCom != "")){
			$com = $repoCom->find($sidCom);
			if ($com == null){
				$response = new Response('No existe la competicion con el identificador "'.$sidCom.'" <a href="'.$this->generateUrl('listado_competiciones').'">Volver</a>');
				$response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_competiciones'));
				return $response;
			}
			else {
				$parametros["selCom"] = $sidCom;
			}
		}
		$temp = Helpers::getTempYear($this->getDoctrine(), date('d'), date('m'), date('Y'));
		$parametros["currentTemp"] = $temp;
		$comDisponibles = $repoCom->findTempComs($temp, "admin");
		$parametros["coms"] = $comDisponibles;
      return $this->render('EasanlesAtletismoBundle:Intento:pant_intento.html.twig', $parametros);
	}
	
	public function obtenerPruebasAction(Request $request){
		$sidCom = $request->query->get('com');
	   $resultados = Helpers::obtenerPruebas($this->getDoctrine(), $sidCom);
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
	
	public function obtenerRondasAction(Request $request, $rol){
		$sidPru = $request->query->get('pru');
		$repoPru = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Prueba');
		$pru = $repoPru->find($sidPru);
		if ($pru == null) return new Response("No existe la prueba con identificador ".$sidPru);
		if ($rol == "user"){
			$atl = $this->getUser()->getIdAtl();
			if ($atl == null) return new Response("No tienes un atleta asociado a tu cuenta");
			if ($pru->getSidCom()->getEsVisible() == false) return new Response("La competición de esta prueba está ahora oculta");
			$idAtl = $atl->getId();
		} else {
			$idAtl = $request->query->get('atl');
		}
		$unidades = $pru->getSidTprm()->getSidTprf()->getUnidades();
		$numIntentos = $pru->getSidTprm()->getSidTprf()->getNumint();
		$repoRon = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Ronda');
		$rondas = $repoRon->findAllFor($sidPru);
		$repoInt = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Intento');
		foreach($rondas as $key => $ron){
			$rondas[$key]['marca'] = $repoInt->getMarcaFor($idAtl, $ron['sid']);
			$rondas[$key]['unidades'] = $unidades;
			$rondas[$key]['numIntentos'] = $numIntentos;
		}
		
      return $this->render('EasanlesAtletismoBundle:Intento:sel_ronda.html.twig', array(
      		"rondas" => $rondas, "selAtl" => $idAtl, "rol" => $rol));
	}
	
	public function crearIntentoAction(Request $request, $rol){
		$sidRon = $request->query->get('ron');
		$repoRon = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Ronda');
		$ron = $repoRon->find($sidRon);
		if ($ron == null){
			return new JsonResponse([
					'success' => false,
					'message' => "No existe la ronda con identificador ".$sidRon
			]);
		}
		if ($rol == "user"){
			$atl = $this->getUser()->getIdAtl();
			if ($atl == null){
				return new JsonResponse([
						'success' => false,
						'message' => "No tienes un atleta asociado a tu cuenta"
				]);
			}
			if ($atl->getEsAlta() == false){
				return new JsonResponse([
						'success' => false,
						'message' => "No estás dado de alta en el club"
				]);
			}
			if ($ron->getSidPru()->getSidCom()->getEsVisible() == false){
				return new JsonResponse([
						'success' => false,
						'message' => "La competición de esta prueba está ahora oculta"
				]);
			}
			$idAtl = $atl->getId();
		} else {
			$idAtl = $request->query->get('atl');
			$repoAtl = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
			$atl = $repoAtl->find($idAtl);
			if ($atl == null){
				return new JsonResponse([
						'success' => false,
						'message' => "No existe el atleta con identificador ".$idAtl
				]);
			}	
		}
		$parametros = array('rol' => $rol, 'sidRon' => $sidRon, 'idAtl' => $idAtl, 'atleta' => $atl->getApellidos().", ".$atl->getNombre());
		$sexo = "";
		if ($ron->getSidPru()->getSidTprm()->getSexo() == 0) $sexo = ", masculino";
	   else if ($ron->getSidPru()->getSidTprm()->getSexo() == 1) $sexo = ", femenino";
		$parametros['ronda'] =
	         $ron->getSidPru()->getSidTprm()->getSidTprf()->getNombre()
	         .$sexo.". "
	         .$ron->getSidPru()->getIdCat()->getNombre().". "
		      .((($ron->getNombre() == null) || ($ron->getNombre() == "")) ? "Ronda ".$ron->getNum() : $ron->getNombre());
		$unidades = $ron->getSidPru()->getSidTprm()->getSidTprf()->getUnidades();
		$parametros['unidades'] = $unidades;
		$numIntentos = $ron->getSidPru()->getSidTprm()->getSidTprf()->getNumint();
		$parametros['numIntentos'] = $numIntentos;
		$repoInt = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Intento');
		$prevData = $repoInt->findOrderedBy($idAtl, $sidRon);
		$arrayInts = array();
		if (count($prevData) > 0){
			foreach ($prevData as $intData){
				$arrayInts[] = $repoInt->find($intData['sid']);
			}
		} else {
			$int = new Intento();
			$int->setSidRon($ron);
			$int->setIdAtl($atl);
			$int->setOrigen($this->getUser()->getNombre());
			$int->setNum(1);
			if ($numIntentos == 1) $int->setValidez(true);
			$arrayInts[] = $int;
		}
		
		$form = $this->createForm(new IntTypeGroup($arrayInts, $unidades));
		$form->handleRequest($request);
		 
		if ($form->isValid()) {
			try {
				$em = $this->getDoctrine()->getManager();
				$data = $form->get('intentos')->getData();
				//Validacion
				if ($numIntentos > 1){
					$countInvalidos = 0;
				   foreach($data as $int){
				   	if ($countInvalidos >= $numIntentos){
				   		throw new Exception("El número máximo de intentos inválidos para fijar marca es ".$numIntentos.".");
				   	}
				   	if ($int->getValidez() == false){
				   		$countInvalidos++;
				   	} else $countInvalidos = 0;
				   }
				} else if (count($data) > 0){
					$data[0]->setValidez(true);
				}
				//Cambios
				if (count($data) < count($arrayInts)){
					foreach($arrayInts as $prevInt){
						if(!\in_array($prevInt, $data)){
							$em->remove($prevInt);
						}
					}
				}
				if ($numIntentos == 1){
					if (count($data) > 0){
						$data[0]->setOrigen($this->getUser()->getNombre());
						$data[0]->setNum(1);
						$em->persist($data[0]);
					}
				} else {
					$count = 1;
				   foreach($data as $int){
   					$int->setSidRon($ron);
	   				$int->setIdAtl($atl);
		   			$int->setOrigen($this->getUser()->getNombre());
			   		$int->setNum($count);
			   		$count++;
				   	$em->persist($int);
				   }
				}
				$em->flush();
			} catch (\Exception $e) {
				$parametros['exception'] = $e->getMessage();
				$parametros['form'] = $form->createView();
				return new JsonResponse([
						'success' => false,
						'message' => $this->render('EasanlesAtletismoBundle:Intento:form_intento.html.twig', $parametros)->getContent()
				]);
			}
			return new JsonResponse([
					'success' => true,
					'message' => "OK"
			]);
		}
		$parametros['form'] = $form->createView();
		return new JsonResponse([
				'success' => false,
				'message' => $this->render('EasanlesAtletismoBundle:Intento:form_intento.html.twig', $parametros)->getContent()
		]);
	}
}
