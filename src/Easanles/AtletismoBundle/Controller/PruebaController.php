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
use Easanles\AtletismoBundle\Entity\Ronda;

class PruebaController extends Controller {
	
    public function listadoPruebasAction($sidCom, Request $request) {
    	$selcat = $request->query->get('c');
    	
    	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	$com = $repository->find($sidCom);
    	if ($com == null){
    		$response = new Response('No existe la competicion con el identificador "'.$sidCom.'" <a href="'.$this->generateUrl('listado_competiciones').'">Volver</a>');
    		$response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_competiciones'));
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
  		if ($selcat != null){
  	   	$parametros['selcat'] = $selcat;
  			$pruebas = $repoPru->searchByParameters($sidCom, $selcat);
  		} else {
  			$pruebas = $repoPru->findAllFor($sidCom);
  		}
  		$repoRon = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Ronda');
  		foreach($pruebas as &$pru){
  			$rondas = $repoRon->findBy(array("sidPru" => $pru['sid']));
  			$pru['rondas'] = $rondas;
  		}
  		foreach($pruebas as &$pru){ //etiquetar como unica prueba de una ronda con rondas posteriores (mostrar aviso)
  			//$nextRondas = $repoPru->getNextRondas($sidCom, $pru['tprm'], $pru['cat'], $pru['ronda']);
  		   //$currentRondaCount = 0;
  	      //foreach ($nextRondas as $ronda){
  	         //if ($ronda['ronda'] == $pru['ronda']) $currentRondaCount++;
  	         //if ($currentRondaCount > 1) break;
  	      //}
  	      //$pru['unique'] = (($currentRondaCount == 1) && (count($nextRondas)) > 1);
  	      $pru['tprm'] = $repoTprm->find($pru['tprm']);
  	      $pru['cat'] = $repoCat->find($pru['cat']);
  		}
  		$parametros['pruebas'] = $pruebas;
  		
  		return $this->render('EasanlesAtletismoBundle:Prueba:list_prueba.html.twig', $parametros);
    }
       
    /**
     * @deprecated
     * @param unknown $sidCom
     * @param Request $request
     * @throws Exception
     */
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
    
   /**
    * @deprecated
    * @param unknown $sidCom
    * @param Request $request
    * @param unknown $pruCopia
    * @param unknown $copyMode
    * @return \Symfony\Component\HttpFoundation\JsonResponse
    */ 
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
   		$response = new Response('No existe la competicion con el identificador "'.$sidCom.'" <a href="'.$this->generateUrl('listado_competiciones').'">Volver</a>');
   		$response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_competiciones'));
   		return $response;
   	}
   	$pru = new Prueba();
   	$pru->setSidCom($com);
   	$ron = new Ronda();
   	$ron->setNum(1);
   	$pru->addRonda($ron);
   	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Prueba');
   	$pru->setId($repository->maxId($sidCom) + 1);
   	$doctrine = $this->getDoctrine();
   	$form = $this->createForm(new PruType($doctrine, null), $pru);
   	
   	$form->handleRequest($request);
   	
   	if ($form->isValid()) {
   		try {
   			if ($pru->getSidTprm() == null) {
   				throw new Exception("Selecciona un tipo de prueba y una modalidad");
   				//TODO: Mejorable. Mostrar error en el campo concreto.
   			}
   			if ($pru->getIdCat()->getTFinVal() != null){
   				throw new Exception("Categoría caducada");
   			}
   			
   			// Minimo una modalidad por tipo de prueba
   			if ($pru->getRondas()->count() == 0)
   				throw new Exception("Introduce al menos una ronda");
   			
   		   $rondas = $pru->getRondas();
   		   $rondasUsadas = array();
   		   $rondaFinal = 0;
    			for ($i = 0; $i < count($rondas); $i++){
    				$rondas[$i]->setId($i+1);
    				$rondasUsadas[] = $rondas[$i]->getNum();
    				if ($rondaFinal < $rondas[$i]->getNum()){
    					$rondaFinal = $rondas[$i]->getNum();
    				}
    			}
    			for ($i = 1; $i < $rondaFinal; $i++){
    				if (!in_array($i, $rondasUsadas)){
    					throw new Exception("La número de la ronda final es ".$rondaFinal." y no hay ninguna ronda ".$i);
    				}
    			}    			
   			
   			$em = $this->getDoctrine()->getManager();
   			$em->persist($pru);
   			$em->flush();
   	
   		} catch (\Exception $e) {
   			$exception = $e->getMessage();
   			return new JsonResponse([
   					'success' => false,
   					'message' => $this->render('EasanlesAtletismoBundle:Prueba:form_prueba.html.twig',
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
   			'message' => $this->render('EasanlesAtletismoBundle:Prueba:form_prueba.html.twig',
   					array('form' => $form->createView(), 'sidCom' => $sidCom, 'mode' => 'new'))->getContent()
   	]);
   }
   
   
   public function borrarPruebaAction($sidCom, Request $request){
   	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
   	$com = $repository->find($sidCom);
   	if ($com == null){
   		$response = new Response('No existe la competicion con el identificador "'.$sidCom.'" <a href="'.$this->generateUrl('listado_competiciones').'">Volver</a>');
   		$response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_competiciones'));
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
   		$response = new Response('No existe la competicion con el identificador "'.$sidCom.'" <a href="'.$this->generateUrl('listado_competiciones').'">Volver</a>');
   		$response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_competiciones').'');
   		return $response;
   	}
   	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Prueba');
   	$pru = $repository->find($sidPru);
   	if ($pru == null){
     	   $response = new Response('No existe la prueba con el identificador "'.$sidPru.'" <a href="'.$this->generateUrl('listado_pruebas', array('sidCom' => $sidCom)).'">Volver</a>');
     		$response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_pruebas', array('sidCom' => $sidCom)));
   		return $response;
   	}
   	
      $form = $this->createForm(new PruType($this->getDoctrine(), $pru->getSidTprm()), $pru);
      
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
   			
   			$rondas = $pru->getRondas();
   			$rondasUsadas = array();
   			$rondaFinal = 0;
   			$cuenta = 1;
   			foreach($rondas as $ron){
   				$ron->setId($cuenta++);
   				$rondasUsadas[] = $ron->getNum();
   				if ($rondaFinal < $ron->getNum()){
   					$rondaFinal = $ron->getNum();
   				}
   			}
   			for ($i = 1; $i < $rondaFinal; $i++){
   				if (!in_array($i, $rondasUsadas)){
   					throw new Exception("La número de la ronda final es ".$rondaFinal." y no hay ninguna ronda ".$i);
   				}
   			}
   			
   			$em = $this->getDoctrine()->getManager();
   			$em->persist($pru);
   			$em->flush();
   	
   		} catch (\Exception $e) {
   			$exception = $e->getMessage();
   			return new JsonResponse([
   					'success' => false,
   					'message' => $this->render('EasanlesAtletismoBundle:Prueba:form_prueba.html.twig',
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
   			'message' => $this->render('EasanlesAtletismoBundle:Prueba:form_prueba.html.twig',
   					array('form' => $form->createView(), 'sidCom' => $sidCom,
   							 'mode' => 'edit', 'sidPru' => $sidPru
   					))->getContent()
   	]);
  	}
}
