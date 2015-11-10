<?php

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
    	  
    	  $repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	  $com = $repository->find($sidCom);
    	  if ($com == null) {
    	  	$response = new Response('No existe la competicion con el identificador "'.$sidCom.'" <a href="'.$this->generateUrl('listado_competiciones').'">Volver</a>');
    	  	$response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_competiciones'));
    	  	return $response;
    	  }
    	  $repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
    	  $atl = $repository->find($idAtl);
    	  if ($atl == null) {
    	  	  $response = new Response('No existe el atleta con identificador "'.$idAtl.'" <a href="'.$this->generateUrl('listado_inscripciones', array('sidCom' => $sidCom)).'">Volver</a>');
    	  	  $response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_inscripciones', array('sidCom' => $sidCom)));
    	  	  return $response;
    	  }
    	  $em = $this->getDoctrine()->getManager();
    	  $repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Participacion');
    	  $par = $repository->findOneBy(array("sidCom" => $sidCom, 'idAtl' => $idAtl));
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
    
}
