<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Easanles\AtletismoBundle\Entity\Competicion;
use Symfony\Component\HttpFoundation\Request;
use Easanles\AtletismoBundle\Form\Type\ComType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Config\Definition\Exception\Exception;
use Easanles\AtletismoBundle\Helpers\Helpers;


class CompeticionController extends Controller {
	
    public function listadoCompeticionesAction(Request $request) {
    	$temp = $request->query->get('temp');
    	$query = $request->query->get('q');
    	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	$temporadas = $repository->findTemps();
    	
    	if (($temp == null) && ($query == null)){
    		$competiciones = $repository->findAllOrdered();
    	} else {
    		$competiciones = $repository->searchByParameters($temp, $query);
    	}
    	$parametros = array('competiciones' => $competiciones, 'temporadas' => $temporadas);
    	if ($temp != null) $parametros['temp'] = $temp;
    	if ($query != null) $parametros['query'] = $query;
    	return $this->render('EasanlesAtletismoBundle:Competicion:list_competicion.html.twig', $parametros);
    }
    
    public function crearCompeticionAction(Request $request) {
    	 $com = new Competicion();
    	 $com->setTemp(Helpers::getTempYear($this->getDoctrine(), date('d'), date('m'), date('Y')));
    	 
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
    	   try {
    	      $em->remove($com);
    	    	$em->flush();
    	   } catch (\Exception $e) {
    	   	return new Response($e->getMessage());
    	   }
    	    return $this->redirect($this->generateUrl('listado_competiciones'));
    	 } else {
       	$response = new Response('No existe la competicion con el identificador "'.$id.'" <a href="'.$this->generateUrl('listado_competiciones').'">Volver</a>');
       	$response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_competiciones'));
       	return $response;
       }
    }
    
    public function editarCompeticionAction(Request $request, $id){
    	$em = $this->getDoctrine()->getManager();
    	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	$com = $repository->find($id);

    	if ($com != null) {
    		$prevNombre = $com->getNombre();
    		$prevTemp = $com->getTemp();
    	   $form = $this->createForm(new ComType(), $com);
    	
    	   $form->handleRequest($request);
    	
    	   if ($form->isValid()) {
    	   	try {
    	   		$nombre = $com->getNombre();
    	   		$temp = $com->getTemp();
    	   		$testResult = $repository->checkData($nombre, $temp);
    	      	if ($testResult && !(($prevNombre == $nombre) && ($prevTemp == $temp))) {
    	      		throw new Exception("Ya existe la competición \"".$nombre."\" para la temporada ".$temp);
    	      	}
    	   		$em->flush();
    	   	} catch (\Exception $e) {
    	   	   $exception = $e->getMessage();
    	      	return $this->render('EasanlesAtletismoBundle:Competicion:form_competicion.html.twig',
    	   			   array('form' => $form->createView(), 'mode' => "edit", 'nombre' => $prevNombre, 'temp' => $prevTemp, 'exception' => $exception));
    	   	}
    	   	return $this->redirect($this->generateUrl('listado_competiciones'));
       	}
    	
      	return $this->render('EasanlesAtletismoBundle:Competicion:form_competicion.html.twig',
    		   	array('form' => $form->createView(), 'mode' => "edit", 'nombre' => $prevNombre, 'temp' => $prevTemp));
       } else {
       	$response = new Response('No existe la competicion con identificador "'.$id.'" <a href="'.$this->generateUrl('listado_competiciones').'">Volver</a>');
       	$response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_competiciones'));
       	return $response;
       }
    }
    
    public function verCompeticionAction($id){
    	 $repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	 $com = $repository->find($id);
    	 $numAtletas = count($repository->findAtletasIns($id));

       if ($com != null) {
          return $this->render('EasanlesAtletismoBundle:Competicion:ver_competicion.html.twig',
    	          array('com' => $com, 'numatletas' => $numAtletas));
    	 } else {
    	 	$response = new Response('No existe la competicion con identificador "'.$id.'" <a href="'.$this->generateUrl('listado_competiciones').'">Volver</a>');
    	 	$response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_competiciones'));
    	 	return $response;
    	 }
    }
}
