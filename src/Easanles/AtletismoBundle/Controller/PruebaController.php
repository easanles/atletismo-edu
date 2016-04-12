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
use Easanles\AtletismoBundle\Form\Type\PruNewType;
use Easanles\AtletismoBundle\Helpers\Helpers;

class PruebaController extends Controller {
	
    public function listadoPruebasAction($sidCom, Request $request) {
    	$selcat = $request->query->get('cat');
    	
    	$from = $request->query->get('from');
    	if (($from == null) || ($from == "")) $from = 0;
    	else $from = intval($from);
    	if ($from < 0) $from = 0;
    	$repoCfg = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Config');
    	$numResultados = $repoCfg->findOneBy(array("clave" => "numresultados"))->getValor();
    	 
    	$repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	$com = $repoCom->find($sidCom);
    	if ($com == null){
    		$response = new Response('No existe la competicion con el identificador "'.$sidCom.'" <a href="'.$this->generateUrl('listado_competiciones').'">Volver</a>');
    		$response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_competiciones'));
    		return $response;
    	}
  	   $repoPru = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Prueba');
  	   $repoTprm = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:TipoPruebaModalidad');
  	   $listaCats = $repoPru->findCats($sidCom);
  	   $repoCat = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Categoria');
  	   foreach ($listaCats as $key => $c){
  	   	$cat = $repoCat->find($c['idCat']);
  	   	$listaCats[$key]['nombre'] = $cat->getNombre();
  	   }
  	   $entornos = $repoCom->getComEntornos($sidCom);
  		$parametros = array('com' => $com, 'categorias' => $listaCats, 'entornos' => $entornos, 'from' => $from, 'numResultados' => $numResultados);
  		if ($selcat != null){
  	   	$parametros['selcat'] = $selcat;
  			$pruebas = $repoPru->searchByParameters($sidCom, $selcat, $from, $numResultados);
  		} else {
  			$pruebas = $repoPru->findAllFor($sidCom, $from, $numResultados);
  		}
  		$repoRon = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Ronda');
  		$repoIns = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Inscripcion');
  		$repoInt = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Intento');
  		foreach($pruebas as $key => $pru){
  			$rondas = $repoRon->findBy(array("sidPru" => $pru['sid']));
  			$numAtletas = array();
  			$count = 0;
  			foreach($rondas as $ron){
  				$numAtletas[$count] = $repoInt->countAtlsFor($ron->getSid());
  				$count++;
  			}
  			$pruebas[$key]['rondas'] = $rondas;
  			$pruebas[$key]['numAtletas'] = $numAtletas;
  			$pruebas[$key]['tprm'] = $repoTprm->find($pru['tprm']);
  			$pruebas[$key]['cat'] = $repoCat->find($pru['cat']);
  			$pruebas[$key]['inscritos'] = $repoIns->countInsForPru($pru['sid']);
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
   	$repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
   	$com = $repoCom->find($sidCom);
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
   	$repoPru = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Prueba');
   	$pru->setId($repoPru->maxId($sidCom) + 1);
   	$repoCat = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Categoria');
   	$pru->setIdCat($repoCat->findOneBy(array("esTodos" => true)));
   	$form = $this->createForm(new PruNewType($this->getDoctrine(), null), $pru);
   	
   	$form->handleRequest($request);
   	
   	if ($form->isValid()) {
   		try {
   			if ($pru->getSidTprm() == null) {
   				throw new Exception("Selecciona un tipo de prueba y una modalidad");
   			}
   			if ($pru->getRondas()->count() == 0) throw new Exception("Introduce al menos una ronda");
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
    			$listaCats = $form->get("listaCat")->getData();
    			if (count($listaCats) == 0){
    				throw new Exception("Seleccione al menos una categoría");
    			}
    			$idCount = $pru->getId();
   			$em = $this->getDoctrine()->getManager();
    			foreach($listaCats as $cat){
    				if (($cat->getTFinVal() != null)
    						&& ($cat->getTFinVal() < Helpers::getCurrentTemp($this->getDoctrine()))){
    					throw new Exception("Categoría \"".$cat->getNombre()."\" caducada");
    				}
    				$checkPru = $repoPru->findOneBy(array("sidCom" => $com, "sidTprm" => $pru->getSidTprm(), "idCat" => $cat));
    				if (count($checkPru) != 0){
    					throw new Exception("Ya existe esta prueba para la categoría ".$cat->getNombre());
    				}
    				if ($idCount == $pru->getId()){
    					$pru->setIdCat($cat);
    					$em->persist($pru);
    				} else {
    					$clonePru = new Prueba();
    					$clonePru
    					      ->setId($idCount)
    					      ->setSidCom($com)
    					      ->setCoste($pru->getCoste())
    					      ->setIdCat($cat)
    					      ->setSidTprm($pru->getSidTprm());
    				   foreach($rondas as $ron){
       					$cloneRon = new Ronda();
    					   $cloneRon
     					         ->setId($ron->getId())
        					      ->setSidPru($ron->getSidPru())
     					         ->setNum($ron->getNum())
    					         ->setNombre($ron->getNombre());
    					   $em->persist($cloneRon);
    					   $clonePru->addRonda($ron);
    				   }
    				   $em->persist($clonePru);
    				}
    				$idCount = $idCount + 1;
    			}
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
   	$repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
   	$com = $repoCom->find($sidCom);
   	if ($com == null){
   		$response = new Response('No existe la competicion con el identificador "'.$sidCom.'" <a href="'.$this->generateUrl('listado_competiciones').'">Volver</a>');
   		$response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_competiciones'));
   		return $response;
   	}
   	$idpru = $request->query->get('i');
   
   	$em = $this->getDoctrine()->getManager();
   	$repoPru = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Prueba');
   	$pru = $repoPru->find($idpru);
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
   	$repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
   	$com = $repoCom->find($sidCom);
   	if ($com == null){
   		$response = new Response('No existe la competicion con el identificador "'.$sidCom.'" <a href="'.$this->generateUrl('listado_competiciones').'">Volver</a>');
   		$response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_competiciones').'');
   		return $response;
   	}
   	$repoPru = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Prueba');
   	$pru = $repoPru->find($sidPru);
   	if ($pru == null){
     	   $response = new Response('No existe la prueba con el identificador "'.$sidPru.'" <a href="'.$this->generateUrl('listado_pruebas', array('sidCom' => $sidCom)).'">Volver</a>');
     		$response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_pruebas', array('sidCom' => $sidCom)));
   		return $response;
   	}
   	$originalRons = new ArrayCollection();
   	foreach ($pru->getRondas() as $ron) {
   		$originalRons->add($ron);
   	}
   	$prevSidTprm = $pru->getSidTprm();
   	$prevCat = $pru->getIdCat();
      $form = $this->createForm(new PruType($this->getDoctrine(), $pru->getSidTprm()), $pru);
     
   	$form->handleRequest($request);

   	if ($form->isValid()) {
   		try {
   			if ($pru->getSidTprm() == null) {
   				throw new Exception("Selecciona un tipo de prueba y una modalidad");
   			}
   			if ($pru->getIdCat()->getTFinVal() != null 
   					&& ($pru->getIdCat()->getTFinVal() < Helpers::getCurrentTemp($this->getDoctrine()))){
   				throw new Exception("Categoría caducada");
   			}
   			if ($pru->getRondas()->count() == 0)
   				throw new Exception("Introduce al menos una ronda");
   			if (($pru->getIdCat() != $prevCat) || ($pru->getSidTprm() != $prevSidTprm)){
   			   $checkPru = $repoPru->findOneBy(array("sidCom" => $com, "sidTprm" => $pru->getSidTprm(), "idCat" => $pru->getIdCat()));
   			   if (count($checkPru) != 0){
   				   throw new Exception("Ya existe esta prueba para la categoría ".$pru->getIdCat()->getNombre());
   			   }   				
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
