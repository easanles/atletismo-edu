<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Easanles\AtletismoBundle\Entity\Competicion;
use Symfony\Component\HttpFoundation\Request;
use Easanles\AtletismoBundle\Form\Type\ComType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Config\Definition\Exception\Exception;


class CompeticionController extends Controller
{
    public function listadoCompeticionesAction() {
    	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	$competiciones = $repository->findAllOrdered();
        return $this->render('EasanlesAtletismoBundle:Competicion:list_competicion.html.twig',
           array('competiciones' => $competiciones));
    }
    
    public function crearCompeticionAction(Request $request) {
    	 $com = new Competicion();
    	 $form = $this->createForm(new ComType(), $com);
    	 
    	 $form->handleRequest($request);
    	     	    	     	 
    	 if ($form->isValid()) {
    	 	try {
    	 	   $nombre = $form->getData()->getNombre();
    	 	   $temp = $form->getData()->getTemp();
    	 	   $repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	 	   $testResult = $repository->checkData($nombre, $temp);
    	 	   if ($testResult) throw new Exception("Ya existe la competición \"".$nombre."\" para la temporada ".$temp);
    	 	 
       	 	$em = $this->getDoctrine()->getManager();
    	 	   $em->persist($com);

    	      $em->flush();
    	   } catch (\Exception $e) {
    	   	$exception = $e->getMessage();
    	   	return $this->render('EasanlesAtletismoBundle:Competicion:form_competicion.html.twig',
    	   			array('form' => $form->createView(), 'mode' => "new", 'exception' => $exception));
    	   }
    	 	return $this->redirect($this->generateUrl('listado_competiciones'));
    	 }
    	 
       return $this->render('EasanlesAtletismoBundle:Competicion:form_competicion.html.twig',
             array('form' => $form->createView(), 'mode' => "new"));
    }
   
    public function borrarCompeticionAction(Request $request){
    	 $id = $request->query->get('i');
    	 
    	 $em = $this->getDoctrine()->getManager();
    	 $repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	 $com = $repository->find($id);
    	 if ($com != null){
    	    $em->remove($com);
    	    try {
    	   	$em->flush();
    	   } catch (\Exception $e) {
    	   	return new Response($e->getMessage());
    	   }
    	    return $this->redirect($this->generateUrl('listado_competiciones'));
    	 } else {
       	$response = new Response('No existe la competicion con el identificador "'.$id.'" <a href="../">Volver</a>');
       	$response->headers->set('Refresh', '2; url=../');
       	return $response;
       }
    }
    
    public function editarCompeticionAction(Request $request, $id){
    	$em = $this->getDoctrine()->getManager();
    	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	$com = $repository->find($id);

    	if ($com != null) {
    	   $form = $this->createForm(new ComType(), $com);
    	
    	   $form->handleRequest($request);
    	
    	   if ($form->isValid()) {
    	   	$repository->checkData($com);
    	   	try {
    	   		$em->flush();
    	   	} catch (\Exception $e) {
    	   	   $exception = $e->getMessage();
    	      	return $this->render('EasanlesAtletismoBundle:Competicion:form_competicion.html.twig',
    	   			   array('form' => $form->createView(), 'mode' => "edit", 'com' => $com, 'exception' => $exception));
    	   	}
    	   	return $this->redirect($this->generateUrl('listado_competiciones'));
       	}
    	
      	return $this->render('EasanlesAtletismoBundle:Competicion:form_competicion.html.twig',
    		   	array('form' => $form->createView(), 'mode' => "edit", 'com' => $com));
       } else {
       	$response = new Response('No existe la competicion con identificador "'.$id.'" <a href="../../">Volver</a>');
       	$response->headers->set('Refresh', '2; url=../../');
       	return $response;
       }
    }
    
    public function verCompeticionAction($id){
    	 $repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	 $com = $repository->find($id);

       if ($com != null) {
          return $this->render('EasanlesAtletismoBundle:Competicion:ver_competicion.html.twig',
    	          array('com' => $com));
    	 } else {
    	 	$response = new Response('No existe la competicion con identificador "'.$id.'" <a href="../">Volver</a>');
    	 	$response->headers->set('Refresh', '2; url=../');
    	 	return $response;
    	 }
    }
}
