<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Easanles\AtletismoBundle\Entity\TipoPruebaFormato;
use Easanles\AtletismoBundle\Form\Type\TprfType;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class TipoPruebaController extends Controller {
    
   public function listadoTipoPruebaFormatoAction() {
   	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:TipoPruebaFormato');
   	$tiposprueba = $repository->findAllOrdered();
   	
   	
      return $this->render('EasanlesAtletismoBundle:TipoPrueba:list_tipopruebaformato.html.twig',
      		array("tiposprueba" => $tiposprueba));		 
   }

   public function crearTipoPruebaFormatoAction(Request $request) {
   	$tprf = new TipoPruebaFormato();
   	$form = $this->createForm(new TprfType(), $tprf);
   	
   	$form->handleRequest($request);
   	 
   	if ($form->isValid()) {
   		try {
   			$nombre = $form->getData()->getNombre();
   			$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:TipoPruebaFormato');
   			$testResult = $repository->checkData($nombre);
   			if ($testResult) throw new Exception("Ya existe un tipo de prueba con el nombre \"".$nombre."\"");
   	
   			$em = $this->getDoctrine()->getManager();
   			$em->persist($tprf);
   	
   			$em->flush();
   		} catch (\Exception $e) {
   			$exception = $e->getMessage();
         	return new JsonResponse([
   		   	'success' => false,
   		   	'message' => $this->render('EasanlesAtletismoBundle:TipoPrueba:form_tipopruebaformato.html.twig',
   		   	array('form' => $form->createView(), 'mode' => "new", 'exception' => $exception))->getContent()
   	      ]);
   		}
   		return new JsonResponse([
   		   	'success' => true,
   		   	'message' => $this->render('EasanlesAtletismoBundle:TipoPrueba:form_tipopruebaformato.html.twig',
   		   	array('form' => $form->createView(), 'mode' => "new"))->getContent()
   	      ]);
   	}
   	
   		return new JsonResponse([
   		   	'success' => false,
   		   	'message' => $this->render('EasanlesAtletismoBundle:TipoPrueba:form_tipopruebaformato.html.twig',
   		   	array('form' => $form->createView(), 'mode' => "new"))->getContent()
   	      ]);
   }
    
}

