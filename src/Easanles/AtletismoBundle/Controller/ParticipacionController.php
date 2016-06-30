<?php

# Copyright (c) 2016 Eduardo Alonso Sanlés
# Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Easanles\AtletismoBundle\Helpers\Helpers;
use Symfony\Component\HttpFoundation\Response;
use Easanles\AtletismoBundle\Entity\Participacion;
use Easanles\AtletismoBundle\Form\Type\ParType;
use Symfony\Component\HttpFoundation\JsonResponse;

class ParticipacionController extends Controller {
	
    public function confirmarParticipacionAction($sidCom, Request $request) {
    	  $idAtl = $request->query->get('atl');
    	  
    	  $repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	  $com = $repoCom->find($sidCom);
    	  if ($com == null) {
    	     return new JsonResponse([
    	  			'success' => false,
    	  			'message' => 'No existe la competición con el identificador "'.$sidCom.'"'
    	     ]);
    	  }
    	  if ($com->getEsCuota() == true){
    	  	  return new JsonResponse([
    	  			'success' => false,
    	  			'message' => 'El identificador '.$sidCom.' no corresponde a una competición'
    	  	  ]);
    	  }
    	  $repoAtl = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
    	  $atl = $repoAtl->find($idAtl);
    	  if ($atl == null) {
    	     return new JsonResponse([
    	  			'success' => false,
    	  			'message' => 'No existe el atleta con identificador "'.$idAtl.'"'
    	  	  ]);
    	  }
    	  $em = $this->getDoctrine()->getManager();
    	  $repoPar = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Participacion');
    	  $par = $repoPar->findOneBy(array("sidCom" => $sidCom, 'idAtl' => $idAtl));
    	  if ($par == null){
    	  	  $par = new Participacion();
    	  	  $par->setSidCom($com);
    	  	  $par->setIdAtl($atl);
    	  }
    	  
    	  $form = $this->createForm(new ParType(), $par);
    	  
    	  $form->handleRequest($request);
    	  
    	  if ($form->isValid()) {
    	     try {
    	        $em->persist($par);
    	        $em->flush();
    	     } catch (\Exception $e) {
    	        $exception = $e->getMessage();
    	  	     return new JsonResponse([
    	  	           'success' => false,
       	  			  'message' => $this->render('EasanlesAtletismoBundle:Participacion:form_participacion.html.twig',
       	  			        array('form' => $form->createView(), "sidCom" => $sidCom, "idAtl" => $idAtl, 'exception' => $exception))->getContent()
    	     	  ]);
    	  	  }
    	     return new JsonResponse([
    	  	     'success' => true,
    	  		  'message' => "OK"
    	  	  ]);
    	  }

    	  return new JsonResponse([
    	        'success' => false,
    	        'message' => $this->render('EasanlesAtletismoBundle:Participacion:form_participacion.html.twig',
    	              array('form' => $form->createView(), "sidCom" => $sidCom, "idAtl" => $idAtl))->getContent()
        ]);   	   
    }
    
    public function marcarAsistenciaAction(Request $request){
    	 $sidPar = $request->request->get('par');
    	 $value = $request->request->get('val');
    	 $repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Participacion');
    	 $par = $repository->findOneBy(array("sid" => $sidPar));
    	 if ($par == null){
    	    return new Response("No existe la participacion con el identificador ". $sidPar);
    	 } else if (($value != true) && ($value != false)) {
    	 	 return new Response("Valor recibido no valido");
    	 } else {
    	 	 $par->setAsisten($value == "true" ? 1 : 0);
    	 	 $em = $this->getDoctrine()->getManager();
    	 	 $em->flush();
    	 	 return new Response("OK");
    	 }
    	
    }
    
}
