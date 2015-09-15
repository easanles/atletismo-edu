<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Easanles\AtletismoBundle\Entity\TipoPruebaFormato;
use Easanles\AtletismoBundle\Form\Type\TprfType;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\Length;
use Easanles\AtletismoBundle\Entity\TipoPruebaModalidad;
use Doctrine\Common\Collections\ArrayCollection;

class TipoPruebaController extends Controller {
    
   public function listadoTipoPruebaFormatoAction() {
   	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:TipoPruebaFormato');
   	$tiposprueba = $repository->findAll();
   	   	
      return $this->render('EasanlesAtletismoBundle:TipoPrueba:list_tipopruebaformato.html.twig',
      		array("tiposprueba" => $tiposprueba));
   }

   public function crearTipoPruebaFormatoAction(Request $request) {
   	$tprf = new TipoPruebaFormato();
   	$tprm = new TipoPruebaModalidad();
   	$tprf->addModalidad($tprm);
   	$form = $this->createForm(new TprfType(), $tprf);
   	
   	$form->handleRequest($request);
   	 
   	if ($form->isValid()) {
   		try {
   			// Comprobar nombre repetido
   			$nombre = $form->getData()->getNombre();
   			$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:TipoPruebaFormato');
   			$testResult = $repository->checkData($nombre);
   			if ($testResult) throw new Exception("Ya existe un tipo de prueba con el nombre \"".$nombre."\"");

   			// Minimo una modalidad por tipo de prueba
   			if ($tprf->getModalidades()->count() == 0)
   		         throw new Exception("Introduce al menos una modalidad para este tipo de prueba");
   			
   			// Restricciones en modalidades
   			$this->checkModalidades($tprf);
   	
   			$em = $this->getDoctrine()->getManager();
            $em->persist($tprf);
   			$em->flush();
   		} catch (\Exception $e) {
   			$exception = $e->getMessage();
         	return new JsonResponse([
   		   	'success' => false,
   		   	'message' => $this->render('EasanlesAtletismoBundle:TipoPrueba:form_tipopruebaformato.html.twig',
   		   	array('form' => $form->createView(), 'mode' => 'new', 'exception' => $exception))->getContent()
   	      ]);
   		}
   		return new JsonResponse([
   		   	'success' => true,
   		   	'message' => "OK"
   	   ]);
   	}
   	
      return new JsonResponse([
   	   	'success' => false,
   	   	'message' => $this->render('EasanlesAtletismoBundle:TipoPrueba:form_tipopruebaformato.html.twig',
   	  	array('form' => $form->createView(), 'mode' => 'new'))->getContent()
      ]);
   }
   
   public function borrarTipoPruebaFormatoAction(Request $request){
    	 $id = $request->query->get('i');
    	 
    	 $em = $this->getDoctrine()->getManager();
    	 $repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:TipoPruebaFormato');
    	 $tprf = $repository->find($id);
    	 if ($tprf != null){
    	    $em->remove($tprf);
    	    try {
    	       $em->flush();
    	    } catch (\Exception $e) {
    	       return new Response($e->getMessage());
    	    }
    	    return $this->redirect($this->generateUrl('configuracion'));
    	 } else {
       	 $response = new Response('No existe el tipo de prueba con el identificador "'.$id.'" <a href="../">Volver</a>');
       	 $response->headers->set('Refresh', '2; url=../');
          return $response;
       }
    }
    
    public function editarTipoPruebaFormatoAction(Request $request, $id){
    	$em = $this->getDoctrine()->getManager();
    	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:TipoPruebaFormato');
    	$tprf = $repository->find($id);
    
    	if ($tprf != null) {
    		$prevNombre = $tprf->getNombre();
    		
    		$originalTags = new ArrayCollection();
    		foreach ($tprf->getModalidades() as $tprm) {
    			$originalTags->add($tprm);
    		}
    		
    		$form = $this->createForm(new TprfType(), $tprf);
    		$form->handleRequest($request);
    		 
    		if ($form->isValid()) {
    			try {
    				// Comprobar nombre repetido
    				$nombre = $tprf->getNombre();
    				$testResult = $repository->checkData($nombre);
    				if ($testResult && !($prevNombre == $nombre)) {
    					throw new Exception("Ya existe el tipo de prueba \"".$nombre."\"");
    				}
    				
    				// Minimo una modalidad por tipo de prueba
    				if ($tprf->getModalidades()->count() == 0)
    					throw new Exception("Introduce al menos una modalidad para este tipo de prueba");
    				
    				// Restricciones en modalidades
    				$this->checkModalidades($tprf);
    				
    				
    				foreach ($originalTags as $tprm) {
    					if (false === $tprf->getModalidades()->contains($tprm)) {
    						// $tprm->getTasks()->removeElement($task);
    						// $tprm->setTask(null);
    						//$em->persist($tag);
    						$em->remove($tprm);
    					}
    				}
    				
    				$em->flush();
    			} catch (\Exception $e) {
    				$exception = $e->getMessage();
    				return new JsonResponse([
    						'success' => false,
    						'message' => $this->render('EasanlesAtletismoBundle:TipoPrueba:form_tipopruebaformato.html.twig',
    								array('form' => $form->createView(), 'mode' => 'edit', 'id' => $id, 'exception' => $exception))->getContent()
    				]);
    			}
    			return new JsonResponse([
    					'success' => true,
    					'message' => "OK"
    			]);
    		}

    		return new JsonResponse([
    				'success' => false,
    				'message' => $this->render('EasanlesAtletismoBundle:TipoPrueba:form_tipopruebaformato.html.twig',
    						array('form' => $form->createView(), 'mode' => 'edit', 'id' => $id))->getContent()
    		]);
    	} else {
    		$message = "No existe el tipo de prueba con identificador ".$id;
    		return new JsonResponse([
    				'success' => false,
    				'message' => $this->render('EasanlesAtletismoBundle:TipoPrueba:form_tipopruebaformato.html.twig',
    						array('form' => $form->createView(), 'mode' => 'edit', 'id' => $id, 'exception' => $message))->getContent()
    		]);
    	}
    }
    
    private function checkModalidades($tprf){
    	$modArray = $tprf->getModalidades()->toArray();
    	$ambosArray = array();
    	$masArray = array();
    	$femArray = array();
    	foreach ($modArray as $mod){
    		switch ($mod->getSexo()){
    			case 2: { //Ambos
    				if ((in_array($mod->getEntorno(), $ambosArray))
    						|| (in_array($mod->getEntorno(), $masArray))
    						|| (in_array($mod->getEntorno(), $femArray))) {
    				   throw new Exception("Existe una modalidad repetida");
    				}
    				else $ambosArray[] = $mod->getEntorno();
    			} break;
    			case 0: { //Masculino
    				if ((in_array($mod->getEntorno(), $ambosArray))
    						|| (in_array($mod->getEntorno(), $masArray))) {
    				   throw new Exception("Existe una modalidad repetida");
    				}
    				else $masArray[] = $mod->getEntorno();
    			} break;
    			case 1: { //Femenino
    				if ((in_array($mod->getEntorno(), $ambosArray))
    						|| (in_array($mod->getEntorno(), $femArray))) {
    				   throw new Exception("Existe una modalidad repetida");
    				}
    				else $femArray[] = $mod->getEntorno();
    			} break;
    			default: {
    				throw new Exception("Error en los datos recibidos");
    			} break;
    		}
    	}
    }
    
}

