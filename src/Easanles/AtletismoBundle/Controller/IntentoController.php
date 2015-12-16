<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Easanles\AtletismoBundle\Helpers\Helpers;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Config\Definition\Exception\Exception;

class IntentoController extends Controller {
	
	public function pantallaIntentosAction(Request $request) {
		$sidCom = $request->query->get('com');
		$repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
		$parametros = array();
		
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
		$comDisponibles = $repoCom->findTempComs($temp);
		$parametros["coms"] = $comDisponibles;
      return $this->render('EasanlesAtletismoBundle:Intento:pant_intento.html.twig', $parametros);
	}
	
	public function obtenerPruebasAction(Request $request){
		$sidCom = $request->query->get('com');
		
		$repoPru = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Prueba');
		$prus = $repoPru->findAllFor($sidCom);
		exit(dump($prus));
	}
	
	public function obtenerCategoriasAction(Request $request){
		
	}
	
	public function obtenerAtletasInscritosAction(Request $request){
		
	}
	
	public function obtenerRondasAction(Request $request){
	
	}
	
	public function crearIntentoAction(Request $request){
		
	}
}
