<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Easanles\AtletismoBundle\Entity\Prueba;
use Symfony\Component\HttpFoundation\Response;
use Easanles\AtletismoBundle\Entity\TipoPruebaModalidad;
use Symfony\Component\HttpFoundation\Request;
use Easanles\AtletismoBundle\Form\Type\PruType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Config\Definition\Exception\Exception;

//$id representa el identificador de la competicion

class PruebaController extends Controller {
	
    public function listadoPruebasAction($id, Request $request) {
    	$seltpr = $request->query->get('tpr');
    	$selcat = $request->query->get('c');
    	
    	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	$com = $repository->find($id);
    	$repoPru = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Prueba');
    	$listaTprs = $repoPru->findTprs($id);
    	$repoTprm = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:TipoPruebaModalidad');
    	foreach ($listaTprs as &$tpr){
    		$tprm = $repoTprm->find($tpr['sidTprm']);
    		$tpr['sexo'] = $tprm->getSexo();
    		$tpr['entorno'] = $tprm->getEntorno();
    		$tpr['nombre'] = $tprm->getSidTprf()->getNombre();
    	}
    	 
    	$parametros = array('com' => $com, 'tipospruebas' => $listaTprs);
    	if ($com != null){
    		if ($seltpr != null) $parametros['seltpr'] = $seltpr;
    	   if ($selcat != null) $parametros['selcat'] = $selcat;
    		if (($seltpr != null) || ($selcat != null)){
    			$pruebas = $repoPru->searchByParameters($id, $seltpr, $selcat);
    		} else {
    			$pruebas = $repoPru->findAllFor($id);
    		}
    		foreach($pruebas as &$pru){
    			$pru['tprm'] = $repoTprm->find($pru['tprm']);
    		}
    		$parametros['pruebas'] = $pruebas;
    		return $this->render('EasanlesAtletismoBundle:Prueba:list_prueba.html.twig', $parametros);
       } else {
       	$response = new Response('No existe la competicion con el identificador "'.$id.'" <a href="../competiciones">Volver</a>');
       	$response->headers->set('Refresh', '2; url=../competiciones');
       	return $response;
       }
    }
    
   public function crearPruebaAction($id, Request $request) {
   	$pru = new Prueba();
   	$pru->setRonda(1);
   	$pru->setIdCat(1);
    	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	$com = $repository->find($id);
   	$pru->setSidCom($com);
   	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Prueba');
   	$pru->setId($repository->maxId($id) + 1);
   	$doctrine = $this->getDoctrine();
   	$form = $this->createForm(new PruType($doctrine), $pru);
   	
   	$form->handleRequest($request);
   	
   	if ($form->isValid()) {
   		try {
   			if ($pru->getSidTprm() == null) {
   				throw new Exception("Selecciona un tipo de prueba y una modalidad");
   				//Mejorable. Mostrar error en el campo concreto.
   			}
   			$em = $this->getDoctrine()->getManager();
            $em->persist($pru);
   			$em->flush();
   			
   		} catch (\Exception $e) {
   			$exception = $e->getMessage();
         	return new JsonResponse([
   		   	'success' => false,
   		   	'message' => $this->render('EasanlesAtletismoBundle:Prueba:form_prueba.html.twig',
   		   	array('form' => $form->createView(), 'sidCom' => $id,
   		   			 'mode' => 'new', 'exception' => $exception))->getContent()
   	      ]);
   		}
         return new JsonResponse([
   			'success' => true,
   			'message' => "OK"
   	   ]);
   	}
   	
   	return new JsonResponse([
   			'success' => false,
   			'message' => $this->render('EasanlesAtletismoBundle:Prueba:form_prueba.html.twig',
   					array('form' => $form->createView(), 'sidCom' => $id,
   							 'mode' => 'new'))->getContent()
   	]);
   }
   
   public function borrarPruebaAction($id, Request $request){
   	$idpru = $request->query->get('i');
   
   	$em = $this->getDoctrine()->getManager();
   	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Prueba');
   	$pru = $repository->find($idpru);
   	if ($pru != null){
   		$em->remove($pru);
   		try {
   			$em->flush();
   		} catch (\Exception $e) {
   			return new Response($e->getMessage());
   		}
   		return $this->redirect($this->generateUrl('listado_pruebas', ['id' => $id]));
   	} else {
   		$response = new Response('No existe la prueba con el identificador "'.$idpru.'" <a href="../competiciones">Volver</a>');
   		//$response->headers->set('Refresh', '2; url=../competiciones');
   		return $response;
   	}
   }
}
