<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Easanles\AtletismoBundle\Helpers\Helpers;

class MiscomController extends Controller{
    public function portadaAction(){
    	 $repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	 $user = $this->getUser();
    	 if (($user == null) || ($user->getIdAtl()->getId() == null)){
    	 	$response = new Response('El usuario no tiene un atleta asociado');
    	 	$response->headers->set('Refresh', '2; url='.$this->generateUrl('homepage'));
    	 	return $response;
    	 }
    	 $tempComs = $repoCom->findTempComs(2015, "user");
    	 $listaComInscritos = $repoCom->findAtlComs($user->getIdAtl()->getId(), 2015);
    	 $listaComs = array();
    	 $hoy = new \DateTime();
    	 foreach ($tempComs as $com){
    	    if (in_array($com['sid'], $listaComInscritos)){
    	    	$com['inscrito'] = true;
    	    } else {
    	    	$com['inscrito'] = false;
    	    }
    	    if (($com['fecha'] >= $hoy) || ($com['inscrito'] == true)) {
    	    	$listaComs[] = $com;
    	    }
    	 }
    	 $parametros = array("coms" => $listaComs, "hoy" => $hoy);
    	
       return $this->render('EasanlesAtletismoBundle:Miscom:portada_miscom.html.twig', $parametros);
    }
    
}
