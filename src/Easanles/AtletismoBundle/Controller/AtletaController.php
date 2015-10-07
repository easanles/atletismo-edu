<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Easanles\AtletismoBundle\Helpers\Helpers;

class AtletaController extends Controller {
	
	public function listadoAtletasAction(Request $request) {
		$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
		$atletas = $repository->findAllOrdered();
		$parametros = array('atletas' => $atletas);
		
		$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Categoria');
		$categorias = array();
		foreach ($atletas as $atl){
			$categorias[] = $repository->findForEdad(Helpers::getEdad($atl['fnac']));
		}
		$parametros['categorias'] = $categorias;
		
		
		return $this->render('EasanlesAtletismoBundle:Atleta:list_atleta.html.twig', $parametros);
	}
	
}
