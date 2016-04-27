<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Easanles\AtletismoBundle\Entity\Competicion;
use Symfony\Component\HttpFoundation\Request;
use Easanles\AtletismoBundle\Form\Type\ComType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Config\Definition\Exception\Exception;
use Easanles\AtletismoBundle\Helpers\Helpers;
use Symfony\Component\HttpFoundation\JsonResponse;
use Easanles\AtletismoBundle\Entity\Config;


class CompeticionController extends Controller {
	
    public function listadoCompeticionesAction(Request $request) {
    	$temp = $request->query->get('temp');
    	$query = $request->query->get('q');
    	
    	$from = $request->query->get('from');
    	if (($from == null) || ($from == "")) $from = 0;
      else $from = intval($from);
    	if ($from < 0) $from = 0;
    	$repoCfg = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Config');
    	$numResultados = $repoCfg->findOneBy(array("clave" => "numresultados"))->getValor();
    	
    	$repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	$temporadas = $repoCom->findTemps("admin");
    	if (($temp == null) && ($query == null)){
    		$competiciones = $repoCom->findAllOrdered($from, $numResultados);
    	} else {
    		$competiciones = $repoCom->searchByParameters($temp, $query, $from, $numResultados);
    	}
    	$hoy = new \DateTime();
    	$ayer = (new \DateTime())->sub(new \DateInterval("P1D"));
    	$parametros = array(
    			'competiciones' => $competiciones, 'temporadas' => $temporadas,
    			'hoy' => $hoy, 'ayer' => $ayer, 'from' => $from, 'numResultados' => $numResultados);
    	if ($temp != null) $parametros['temp'] = $temp;
    	if ($query != null) $parametros['query'] = $query;
    	return $this->render('EasanlesAtletismoBundle:Competicion:list_competicion.html.twig', $parametros);
    }
    
    public function crearCompeticionAction(Request $request) {
    	 $com = new Competicion();
    	 $com->setTemp(Helpers::getCurrentTemp($this->getDoctrine()));
    	 
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
    
    public function verCompeticionAction($id, $rol){
    	 $repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	 $com = $repoCom->find($id);
    	 $numAtletas = count($repoCom->findAtletasIns($id));

       if ($com == null) {
       	 if ($rol == "user"){
       	 	$response = new Response('No existe esa competición <a href="'.$this->generateUrl('mis_competiciones').'">Volver</a>');
       	 	$response->headers->set('Refresh', '3; url='.$this->generateUrl('mis_competiciones'));
       	 } else {
       	 	$response = new Response('No existe la competicion con identificador "'.$id.'" <a href="'.$this->generateUrl('listado_competiciones').'">Volver</a>');
       	 	$response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_competiciones'));
       	 }
       	 return $response;
       }
       if (($rol == "user") && ($com->getEsVisible() == false)){
       	 $response = new Response('Esta competición está oculta <a href="'.$this->generateUrl('mis_competiciones').'">Volver</a>');
       	 $response->headers->set('Refresh', '2; url='.$this->generateUrl('mis_competiciones'));
       	 return $response;
       }
       if ($com->getEsCuota() == true){
       	 $response = new Response('El identificador '.$id.' no pertenece a una competición <a href="'.$this->generateUrl('listado_competiciones').'">Volver</a>');
       	 $response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_competiciones'));
       	 return $response;
       }
       $entornos = $repoCom->getComEntornos($id);
       $entorno = "";
       if (count($entornos) > 1) $entorno = "Varios";
       else if (count($entornos) == 1)	$entorno = $entornos[0]['entorno'];
       else $entorno == "";
       return $this->render('EasanlesAtletismoBundle:Competicion:ver_competicion.html.twig',
    	       array('com' => $com, 'numatletas' => $numAtletas, 'rol' => $rol, 'entorno' => $entorno, 'hoy' => new \DateTime()));
    }
    
    public function flagsCompeticionAction(Request $request){
    	 $repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	 $sidCom = $request->query->get('com');
    	 $valor = $request->query->get('v');
    	 $tipo = $request->query->get('t');
    	 if (($sidCom != null) && ($valor != null) && ($tipo != null)){
    	 	 $com = $repoCom->find($sidCom);
    	 	 if ($com != null){
    	 	 	 switch ($tipo){
    	 	 	 	case "vis": {
    	 	 	 		$com->setEsVisible($valor == 1 ? true : false);
    	 	 	 	} break;
    	 	 	 	case "ins": {
    	 	 	 		$com->setEsInscrib($valor == 1 ? true : false);
    	 	 	 	} break;
    	 	 	 	default: return new JsonResponse(['success' => false]);
    	 	 	 }
    	 	 	 try {
    	 	 	 	 $em = $this->getDoctrine()->getManager();
    	 	 	 	 $em->flush();
    	 	 	 } catch (\Exception $e){
    	 	 	 	 return new JsonResponse(['success' => false]);
    	 	 	 }
    	 	 	 return new JsonResponse(['success' => true]);
    	 	 }
    	 }
    	 return new JsonResponse(['success' => false]);	 
    }
    
}
