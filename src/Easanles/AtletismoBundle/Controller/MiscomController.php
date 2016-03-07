<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Easanles\AtletismoBundle\Helpers\Helpers;

class MiscomController extends Controller{
    public function portadaAction(Request $request){
    	 $repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	 $temp = $request->query->get('temp');    	 
    	 $temp = (int)$temp;    	 
    	 if (($temp == null) || ($temp == "") || (!is_int($temp))){
    	    $temp = Helpers::getCurrentTemp($this->getDoctrine());
    	 }    	 
    	 $user = $this->getUser();
    	 if (($user == null) || ($user->getIdAtl()->getId() == null)){
    	 	$response = new Response('El usuario no tiene un atleta asociado');
    	 	$response->headers->set('Refresh', '2; url='.$this->generateUrl('homepage'));
    	 	return $response;
    	 }
    	 $temps = $repoCom->findTemps("user");
    	 
    	 $tempComs = $repoCom->findTempComs($temp, "user");
    	 $listaComInscritos = $repoCom->findAtlComs($user->getIdAtl()->getId(), $temp);
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
    	 $parametros = array("temp" => $temp, "temporadas" => $temps, "coms" => $listaComs, "hoy" => $hoy);
    	
       return $this->render('EasanlesAtletismoBundle:Miscom:portada_miscom.html.twig', $parametros);
    }
    
}
