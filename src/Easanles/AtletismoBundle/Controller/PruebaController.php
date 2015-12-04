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
use Easanles\AtletismoBundle\Entity\Categoria;
use Easanles\AtletismoBundle\Entity\Ronda;
use Doctrine\Common\Collections\ArrayCollection;

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
  	   $repoTprm = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:TipoPruebaModalidad');
  	   $listaCats = $repoPru->findCats($sidCom);
  	   $repoCat = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Categoria');
  	   foreach ($listaCats as &$c){
  	   	$cat = $repoCat->find($c['idCat']);
  	   	$c['nombre'] = $cat->getNombre();
  	   }
     	 
  		$parametros = array('com' => $com, 'categorias' => $listaCats);
  		if ($selcat != null){
  	   	$parametros['selcat'] = $selcat;
  			$pruebas = $repoPru->searchByParameters($sidCom, $selcat);
  		} else {
  			$pruebas = $repoPru->findAllFor($sidCom);
  		}
  		$repoRon = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Ronda');
  		$repoIns = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Inscripcion');
  		foreach($pruebas as &$pru){
  			$rondas = $repoRon->findBy(array("sidPru" => $pru['sid']));
  			$pru['rondas'] = $rondas;
  			$pru['tprm'] = $repoTprm->find($pru['tprm']);
  			$pru['cat'] = $repoCat->find($pru['cat']);
  			$pru['inscritos'] = $repoIns->countInsForPru($pru['sid']);
  		}
  		$parametros['pruebas'] = $pruebas;
  		
  		return $this->render('EasanlesAtletismoBundle:Prueba:list_prueba.html.twig', $parametros);
    }
    
   public function listarInscritosPruebaAction($sidCom, Request $request){
   	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
   	$com = $repository->find($sidCom);
   	if ($com == null){
   		$response = new Response('No existe la competicion con el identificador "'.$sidCom.'" <a href="'.$this->generateUrl('listado_competiciones').'">Volver</a>');
   		$response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_competiciones'));
   		return $response;
   	}
   	$sidPru = $request->query->get('pru');
   	$repoPru = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Prueba');
   	$pru = $repoPru->find($sidPru);
   	if ($pru == null){
   		return new JsonResponse([
   				'success' => false,
   				'message' => "<div class=\"alert alert-danger\" role=\"alert\"><span>No existe la prueba con el identificador \"".$sidPru."\"</span></div>"
   		]);
   	}
   	$repoIns = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Inscripcion');
   	$listaIns = $repoIns->findInsForPru($sidPru);
   	if (sizeof($listaIns) == 0){
   		$responseText = "No hay atletas inscritos";
   	} else {
   		$responseText = "<ul>";
   		foreach($listaIns as $ins){
   			$responseText .= "<li>".$ins['apellidos'].", ".$ins['nombre'];
   			if (($ins['nick'] != null) && ($ins['nick'] != "")){
   				$responseText .= " (".$ins['nick'].")";
   			}
   			$responseText .= "</li>";
   		}
   		$responseText .= "</ul>";
   	}
   	return new JsonResponse([
   			'success' => true,
   			'message' => $responseText
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
   		try {
   			$em->remove($pru);
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
   	$originalRons = new ArrayCollection();
   	foreach ($pru->getRondas() as $ron) {
   		$originalRons->add($ron);
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
   			if ($pru->getRondas()->count() == 0)
   				throw new Exception("Introduce al menos una ronda");
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
   			foreach ($originalRons as $ron) {
   				if (false === $pru->getRondas()->contains($ron)) {
   					$em->remove($ron);
   				}
   			}
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
