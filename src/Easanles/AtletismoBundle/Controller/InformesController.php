<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Config\Definition\Exception\Exception;

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
	
}
