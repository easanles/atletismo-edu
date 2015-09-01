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
   		   	array('form' => $form->createView(), 'mode' => 'new', 'exception' => $exception))->getContent()
   	      ]);
   		}
   		return new JsonResponse([
   		   	'success' => true,
   		   	'message' => $this->render('EasanlesAtletismoBundle:TipoPrueba:form_tipopruebaformato.html.twig',
   		   	array('form' => $form->createView(), 'mode' => 'new'))->getContent()
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
    				$nombre = $tprf->getNombre();
    				$testResult = $repository->checkData($nombre);
    				if ($testResult && !($prevNombre == $nombre)) {
    					throw new Exception("Ya existe el tipo de prueba \"".$nombre."\"");
    				}
    				
    				// remove the relationship between the tag and the Task
    				foreach ($originalTags as $tprm) {
    					if (false === $tprf->getModalidades()->contains($tprm)) {
    						// remove the Task from the Tag
    						// $tprm->getTasks()->removeElement($task);
    						// if it was a many-to-one relationship, remove the relationship like this
    						// $tprm->setTask(null);
    						//$em->persist($tag);
    						// if you wanted to delete the Tag entirely, you can also do that
    						$em->remove($tprm);
    					}
    				}
    				
    				//$em->persist($tprf);
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
    					'message' => $this->render('EasanlesAtletismoBundle:TipoPrueba:form_tipopruebaformato.html.twig',
    							array('form' => $form->createView(), 'mode' => 'edit', 'id' => $id))->getContent()
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
    
}

