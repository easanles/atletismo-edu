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
use Easanles\AtletismoBundle\Form\Type\PruCopyType;
use Easanles\AtletismoBundle\Entity\Categoria;

class PruebaController extends Controller {
	
    public function listadoPruebasAction($sidCom, Request $request) {
    	$seltpr = $request->query->get('tpr');
    	$selcat = $request->query->get('c');
    	
    	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	$com = $repository->find($sidCom);
    	if ($com == null){
    		$response = new Response('No existe la competicion con el identificador "'.$sidCom.'" <a href="../competiciones">Volver</a>');
    		$response->headers->set('Refresh', '2; url=../competiciones');
    		return $response;
    	}
  	   $repoPru = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Prueba');
  	   $listaTprs = $repoPru->findTprs($sidCom);
  	   $repoTprm = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:TipoPruebaModalidad');
  	   foreach ($listaTprs as &$tpr){
     		$tprm = $repoTprm->find($tpr['sidTprm']);
  		   $tpr['sexo'] = $tprm->getSexo();
  		   $tpr['entorno'] = $tprm->getEntorno();
  		   $tpr['nombre'] = $tprm->getSidTprf()->getNombre();
  	   }
  	   $listaCats = $repoPru->findCats($sidCom);
  	   $repoCat = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Categoria');
  	   foreach ($listaCats as &$c){
  	   	$cat = $repoCat->find($c['idCat']);
  	   	$c['nombre'] = $cat->getNombre();
  	   }
     	 
     	$parametros = array('com' => $com, 'tipospruebas' => $listaTprs, 'categorias' => $listaCats);
  		if ($seltpr != null) $parametros['seltpr'] = $seltpr;
  	   if ($selcat != null) $parametros['selcat'] = $selcat;
  		if (($seltpr != null) || ($selcat != null)){
  			$pruebas = $repoPru->searchByParameters($sidCom, $seltpr, $selcat);
  		} else {
  			$pruebas = $repoPru->findAllFor($sidCom);
  		}
  		foreach($pruebas as &$pru){ //etiquetar como unica prueba de una ronda con rondas posteriores (mostrar aviso)
  			$nextRondas = $repoPru->getNextRondas($sidCom, $pru['tprm'], $pru['cat'], $pru['ronda']);
  		   $currentRondaCount = 0;
  	      foreach ($nextRondas as $ronda){
  	         if ($ronda['ronda'] == $pru['ronda']) $currentRondaCount++;
  	         if ($currentRondaCount > 1) break;
  	      }
  	      $pru['unique'] = (($currentRondaCount == 1) && (count($nextRondas)) > 1);
  	      $pru['tprm'] = $repoTprm->find($pru['tprm']);
  	      $pru['cat'] = $repoCat->find($pru['cat']);
  		}
  		$parametros['pruebas'] = $pruebas;
  		return $this->render('EasanlesAtletismoBundle:Prueba:list_prueba.html.twig', $parametros);
    }
    
    
    private function crearPrimeraRonda($sidCom, Request $request) {
    	$pru = new Prueba();
    	$pru->setRonda(1);
    	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	$com = $repository->find($sidCom);
    	$pru->setSidCom($com);
    	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Prueba');
    	$pru->setId($repository->maxId($sidCom) + 1);
    	$doctrine = $this->getDoctrine();
    	$form = $this->createForm(new PruType($doctrine, null), $pru);
    
    	$form->handleRequest($request);
    
    	if ($form->isValid()) {
    		try {
    			if ($pru->getSidTprm() == null) {
    				throw new Exception("Selecciona un tipo de prueba y una modalidad");
    				//Mejorable. Mostrar error en el campo concreto.
    			}
    			if ($pru->getIdCat()->getTFinVal() != null){
    				throw new Exception("Categoría caducada");
    			}
    			$em = $this->getDoctrine()->getManager();
    			$em->persist($pru);
    			$em->flush();
    
    		} catch (\Exception $e) {
    			$exception = $e->getMessage();
    			return new JsonResponse([
    					'success' => false,
    					'message' => $this->render('EasanlesAtletismoBundle:Prueba:form_new_prueba.html.twig',
    							array('form' => $form->createView(), 'sidCom' => $sidCom, 'mode' => 'new',
    									'exception' => $exception))->getContent()
    			]);
    		}
    		return new JsonResponse([
    				'success' => true,
    				'message' => "OK"
    		]);
    	}
    
    	return new JsonResponse([
    			'success' => false,
    			'message' => $this->render('EasanlesAtletismoBundle:Prueba:form_new_prueba.html.twig',
    					array('form' => $form->createView(), 'sidCom' => $sidCom, 'mode' => 'new'))->getContent()
    	]);
    } 
    
    
   private function crearCopia($sidCom, Request $request, $pruCopia, $copyMode){
   	$pru = new Prueba();
   	
   	$pru->setRonda($pruCopia->getRonda() + (($copyMode == "ronda") ? 1 : 0));
   	$pru->setSidTprm($pruCopia->getSidTprm());
   	$pru->setIdCat($pruCopia->getIdCat());
   	if ($copyMode == "dupl") $pru->setNombre($pruCopia->getNombre());
   	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
   	$com = $repository->find($sidCom);
   	$pru->setSidCom($com);
   	$repoPru = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Prueba');
   	$pru->setId($repoPru->maxId($sidCom) + 1);
   	
   	$nombreTpr = $pruCopia->getSidTprm()->getSidTprf()->getNombre().". ";
   	if ($pruCopia->getSidTprm()->getSexo() == 0) $nombreTpr = $nombreTpr."Masculino, ";
   	else if ($pruCopia->getSidTprm()->getSexo() == 1) $nombreTpr = $nombreTpr."Femenino, ";
   	$nombreTpr = $nombreTpr.$pruCopia->getSidTprm()->getEntorno();
   	$nombreCat = $pruCopia->getIdCat()->getNombre()." (".$pruCopia->getIdCat()->getEdadMax().")";
   	
   	$form = $this->createForm(new PruCopyType($nombreTpr, $nombreCat), $pru);
   	
   	$form->handleRequest($request);
   	$parametros = array('form' => $form->createView(),
   			 'sidCom' => $sidCom,
   			 'sidPruCopia' => $pruCopia->getSid(),
   			 'mode' => 'new',
   			 'copyMode' => $copyMode
   	);
   	
   	if ($form->isValid()) {
   		try {
   			$em = $this->getDoctrine()->getManager();
   			$em->persist($pru);
   			$em->flush();
   		} catch (\Exception $e) {
   			$parametros['exception'] = $e->getMessage();
   			return new JsonResponse([
   					'success' => false,
   					'message' => $this->render('EasanlesAtletismoBundle:Prueba:form_copy_prueba.html.twig',
   							 $parametros)->getContent()
   			]);
   		}
   		return new JsonResponse([
   				'success' => true,
   				'message' => "OK"
   		]);
   	}
   	
   	return new JsonResponse([
   			'success' => false,
   			'message' => $this->render('EasanlesAtletismoBundle:Prueba:form_copy_prueba.html.twig',
   					 $parametros)->getContent()
   	]);
   }
    
   
   public function crearPruebaAction($sidCom, Request $request) {
   	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
   	$com = $repository->find($sidCom);
   	if ($com == null){
   		$response = new Response('No existe la competicion con el identificador "'.$sidCom.'" <a href="../competiciones">Volver</a>');
   		$response->headers->set('Refresh', '2; url=../competiciones');
   		return $response;
   	}
      $sidPru = $request->query->get('pru');
      if ($sidPru != null) {
     		$repoPru = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Prueba');
   	   $pruCopia = $repoPru->find($sidPru);
   	   if ($pruCopia == null) {
     			$response = new Response('No existe la prueba con el identificador "'.$sidPru.'" <a href="../'.$sidCom.'">Volver</a>');
     			$response->headers->set('Refresh', '2; url=../'.$sidCom);
   		   return $response;
   	   } else {
     			$copyMode = $request->query->get('mod');
   		   return $this->crearCopia($sidCom, $request, $pruCopia, $copyMode);
   	   }
      } else return $this->crearPrimeraRonda($sidCom, $request);
   }
   
   
   public function borrarPruebaAction($sidCom, Request $request){
   	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
   	$com = $repository->find($sidCom);
   	if ($com == null){
   		$response = new Response('No existe la competicion con el identificador "'.$sidCom.'" <a href="../competiciones">Volver</a>');
   		$response->headers->set('Refresh', '2; url=../competiciones');
   		return $response;
   	}
   	$idpru = $request->query->get('i');
   
   	$em = $this->getDoctrine()->getManager();
   	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Prueba');
   	$pru = $repository->find($idpru);
   	if ($pru != null){
   		$nextRondas = $repository->getNextRondas($sidCom, $pru->getSidTprm(), $pru->getIdCat(), $pru->getRonda());
   		$currentRondaCount = 0;
   		foreach ($nextRondas as $ronda){
   		   if ($ronda['ronda'] == $pru->getRonda()) $currentRondaCount++;
   		   if ($currentRondaCount > 1) break;
   		}
   		if ($currentRondaCount > 1){
   		   $em->remove($pru);
   		} else foreach ($nextRondas as $ronda){
   			$em->remove($repository->find($ronda['sid']));
   		}
   		try {
   			$em->flush();
   		} catch (\Exception $e) {
   			return new Response($e->getMessage());
   		}
   		return $this->redirect($this->generateUrl('listado_pruebas', ['sidCom' => $sidCom]));
   	}
   }
   
   
   public function editarPruebaAction($sidCom, $sidPru, Request $request){
   	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
   	$com = $repository->find($sidCom);
   	if ($com == null){
   		$response = new Response('No existe la competicion con el identificador "'.$sidCom.'" <a href="../../../competiciones">Volver</a>');
   		$response->headers->set('Refresh', '2; url=../../../competiciones');
   		return $response;
   	}
   	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Prueba');
   	$pru = $repository->find($sidPru);
   	if ($pru == null){
     	   $response = new Response('No existe la prueba con el identificador "'.$sidPru.'" <a href="../../'.$sidCom.'">Volver</a>');
     		$response->headers->set('Refresh', '2; url=../../'.$sidCom);
   		return $response;
   	}
   	
   	if ($pru->getRonda() == 1){
   		$template = 'EasanlesAtletismoBundle:Prueba:form_new_prueba.html.twig';
   		$form = $this->createForm(new PruType($this->getDoctrine(), $pru->getSidTprm()), $pru);
   	} else {
   		$template = 'EasanlesAtletismoBundle:Prueba:form_copy_prueba.html.twig';
   		$nombreTpr = $pru->getSidTprm()->getSidTprf()->getNombre().". ";
   		if ($pru->getSidTprm()->getSexo() == 0) $nombreTpr = $nombreTpr."Masculino, ";
   		else if ($pru->getSidTprm()->getSexo() == 1) $nombreTpr = $nombreTpr."Femenino, ";
   		$nombreTpr = $nombreTpr.$pru->getSidTprm()->getEntorno();
   		$nombreCat = $pru->getIdCat()->getNombre()." (".$pru->getIdCat()->getEdadMax().")";
   		 
   		$form = $this->createForm(new PruCopyType($nombreTpr, $nombreCat), $pru);
   	}
   	$form->handleRequest($request);
   	
   	if ($form->isValid()) {
   		try {
   			if ($pru->getSidTprm() == null) {
   				throw new Exception("Selecciona un tipo de prueba y una modalidad");
   				//Mejorable. Mostrar error en el campo concreto.
   			}
   			if ($pru->getIdCat()->getTFinVal() != null){
   				throw new Exception("Categoría caducada");
   			}
   			$em = $this->getDoctrine()->getManager();
   			$em->persist($pru);
   			$em->flush();
   	
   		} catch (\Exception $e) {
   			$exception = $e->getMessage();
   			return new JsonResponse([
   					'success' => false,
   					'message' => $this->render($template,
   							array('form' => $form->createView(), 'sidCom' => $sidCom,
   									'mode' => 'edit', 'sidPru' => $sidPru,
   									'exception' => $exception))->getContent()
   			]);
   		}
   		return new JsonResponse([
   				'success' => true,
   				'message' => "OK"
   		]);
   	}
   	return new JsonResponse([
   			'success' => false,
   			'message' => $this->render($template,
   					array('form' => $form->createView(), 'sidCom' => $sidCom,
   							 'mode' => 'edit', 'sidPru' => $sidPru
   					))->getContent()
   	]);
  	}
}
