<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AtletaController extends Controller {
	
	public function listadoAtletasAction(Request $request) {
		return $this->render('EasanlesAtletismoBundle:Atleta:list_atleta.html.twig',
				array());
	}
	
}
