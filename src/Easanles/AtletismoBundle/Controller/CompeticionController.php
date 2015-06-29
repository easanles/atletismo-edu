<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Easanles\AtletismoBundle\Entity\Competicion;
use Symfony\Component\HttpFoundation\Request;
use Easanles\AtletismoBundle\Form\Type\ComType;
use Symfony\Component\HttpFoundation\Response;


class CompeticionController extends Controller
{
    public function listadoCompeticionesAction() {
    	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	$competiciones = $repository->findAll();
        return $this->render('EasanlesAtletismoBundle:Competicion:list_competicion.html.twig',
           array('competiciones' => $competiciones));
    }
    
    public function crearCompeticionAction(Request $request) {
    	 $com = new Competicion();
    	 $form = $this->createForm(new ComType(), $com);
    	 
    	 $form->handleRequest($request);
    	 
    	 if ($form->isValid()) {
    	 	// guardar la tarea en la base de datos
    	 	$em = $this->getDoctrine()->getManager();
    	 	$em->persist($com);
    	   try {
    	      $em->flush();
    	   } catch (\Exception $e) {
    	   	return new Response($e->getMessage());
    	   }
    	 	return $this->redirect($this->generateUrl('listado_competiciones'));
    	 }
    	 
       return $this->render('EasanlesAtletismoBundle:Competicion:form_competicion.html.twig',
             array('form' => $form->createView()));
    }
   
    public function borrarCompeticionAction(Request $request){
    	 $nombre = $request->query->get('n');
    	 $temp = $request->query->get('t');
    	 
    	 $em = $this->getDoctrine()->getManager();
    	 $repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	 $com = $repository->findOneBy(array('nombre' => $nombre, 'temp' => $temp));
    	 if ($com != null){
    	    $em->remove($com);
    	    try {
    	   	$em->flush();
    	   } catch (\Exception $e) {
    	   	return new Response($e->getMessage());
    	   }
    	    return $this->redirect($this->generateUrl('listado_competiciones'));
    	 } else {
       	$response = new Response('No existe la competicion "'.$nombre.'" en la temporada '.$temp.' <a href=".">Volver</a>');
       	$response->headers->set('Refresh', '2; url=.');
       	return $response;
       }
    }
    
    public function editarCompeticionAction(Request $request, $nombre, $temp){
    	$em = $this->getDoctrine()->getManager();
    	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	$com = $repository->findOneBy(array('nombre' => $nombre, 'temp' => $temp));

    	if ($com != null) {
    	   $form = $this->createForm(new ComType(), $com);
    	
    	   $form->handleRequest($request);
    	
    	   if ($form->isValid()) {
    	   	try {
    	   		$em->flush();
    	   	} catch (\Exception $e) {
    	   		return new Response($e->getMessage());
    	   	}
    	   	return $this->redirect($this->generateUrl('listado_competiciones'));
       	}
    	
      	return $this->render('EasanlesAtletismoBundle:Competicion:form_competicion.html.twig',
    		   	array('form' => $form->createView()));
       } else {
       	$response = new Response('No existe la competicion "'.$nombre.'" en la temporada '.$temp.' <a href=".">Volver</a>');
       	$response->headers->set('Refresh', '2; url=.');
       	return $response;
       }
    }
    
    public function verCompeticionAction($nombre, $temp){
    	 $repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	 $com = $repository->findOneBy(array('nombre' => $nombre, 'temp' => $temp));

       if ($com != null) {
          return $this->render('EasanlesAtletismoBundle:Competicion:ver_competicion.html.twig',
    	          array('com' => $com));
    	 } else {
    	 	$response = new Response('No existe la competicion "'.$nombre.'" en la temporada '.$temp.' <a href=".">Volver</a>');
    	 	$response->headers->set('Refresh', '2; url=.');
    	 	return $response;
    	 }
    }
}
