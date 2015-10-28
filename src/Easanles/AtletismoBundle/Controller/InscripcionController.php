<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class InscripcionController extends Controller
{
    public function listadoInscripcionesAction($sidCom, Request $request) {
    	  $repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	  $com = $repository->find($sidCom);
    	  $parametros = array('com' => $com);
    	  
    	  $repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Inscripcion');
    	  $inscripciones = $repository->findFor($sidCom);
    	  $parametros = array('inscripciones' => $inscripciones);
    	  
    	  if ($com->getEsFeder() == true){
    	  }
    	  $atletas = array();
    	  $atletas[] = "test";
    	  $parametros['atletas'] = $atletas;
    	   
        return $this->render('EasanlesAtletismoBundle:Inscripcion:list_inscripcion.html.twig', $parametros);
    }
}
