<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\VarDumper;
use Easanles\AtletismoBundle\Entity\Competicion;
use Easanles\AtletismoBundle\Entity\TipoPruebaFormato;
use Symfony\Component\HttpFoundation\JsonResponse;
use Easanles\AtletismoBundle\Entity\TipoPruebaModalidad;
use Easanles\AtletismoBundle\Entity\Prueba;
use Easanles\AtletismoBundle\Helpers\Helpers;
use Symfony\Component\HttpFoundation\Request;

class ConfiguracionController extends Controller {
	
    public function menu_configuracionAction() {
    	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Config');
    	$ajContent = $this->render('EasanlesAtletismoBundle:Configuracion:form_ajustes.html.twig', array(
    			"fIniTemp" => $repository->findOneBy(array("clave" => "fIniTemp"))->getValor()
    	))->getContent();
    	
    	return $this->render('EasanlesAtletismoBundle:Configuracion:menu_configuracion.html.twig', array(
    			"ajustesContent" => $ajContent
    	));		 
    }
    
    public function cambiarAjustesAction(Request $request){
    	 $parametros = array();
    	 $repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Config');
    	 $em = $this->getDoctrine()->getManager();
    	 
    	 //DIA Y MES DE INICIO DE LAS TEMPORADAS
    	 $fIniTempObj = $repository->findOneBy(array("clave" => "fIniTemp"));
    	 $fIniTempString = $request->request->get('fIniTemp');
    	 if ($fIniTempString == null){
    	 	 $parametros["fIniTemp"] = $fIniTempObj->getValor();
    	 } else {
    	 	  if (!Helpers::checkDayMonth($fIniTempString)){
    	 	  	  $parametros["fIniTemp"] = $fIniTempString;
    	 	  	  $parametros["errFIniTemp"] = "Formato de fecha: dd/mm (dd = dia, mm = mes)";
    	 	  } else {
    	 	  	  $datos = explode("/", $fIniTempString);
    	 	  	  if (!checkdate(trim($datos[1]), trim($datos[0]), 2015)){ //Cualquier año no bisiesto
    	 	  		  $parametros["fIniTemp"] = $fIniTempString;
    	 	  		  $parametros["errFIniTemp"] = "Fecha no valida";
    	 	  	  } else {
    	 	  	  	  $prevString = $fIniTempObj->getValor();
    	 	  	  	  if (!strcmp($prevString, $fIniTempString)) { //Comprobar cambios
    	 	  	  	     $parametros["okFIniTemp"] = true;
    	 	  		     $fIniTempObj->setValor(trim($datos[0])."/".trim($datos[1]));
    	 	  		     $parametros["fIniTemp"] = $fIniTempObj->getValor();
    	 	  	  	  }
    	 	  	  }
    	 	  }
    	 }

    	 $em->flush();
    	 return new JsonResponse([
       	'success' => true,
       	'message' => $this->render('EasanlesAtletismoBundle:Configuracion:form_ajustes.html.twig', $parametros)->getContent()
       ]);
    }
    
    public function poblar_bdAction(){
    	try{
    	   $em = $this->getDoctrine()->getManager();
    	   
    	   Helpers::poblarBD($em);
    	   
       	$response = new JsonResponse([
       			'success' => true,
       			'message' => 'Base de datos poblada con datos de prueba',
       	]);
    	} catch(\Doctrine\ORM\ORMException $e) {
    		$response = new JsonResponse([
    				'success' => false,
    				'message' => $this->get('logger')->error($e->getMessage()),
    		]);
    	} catch(\Exception $e){
    		$response = new JsonResponse([
    				'success' => false,
    				'message' => $e->getMessage(),
    		]);
      }
    	
    	return $response;	 
    }
    
    public function borrar_bdAction(){
    	try{
    	   $em = $this->getDoctrine()->getManager();
    	   $sql = 'DELETE FROM atl; DELETE FROM `ins`; DELETE FROM `int`; DELETE FROM `not`; DELETE FROM `par`; DELETE FROM `pru`; DELETE FROM `req`; DELETE FROM `tprm`; DELETE FROM `tprf`; DELETE FROM `vrq`; DELETE FROM `com`; DELETE FROM `cat`';
    	   $connection = $em->getConnection();
    	   $stmt = $connection->prepare($sql);
    	   $stmt->execute();
    	   $stmt->closeCursor();
       	$response = new JsonResponse([
       			'success' => true,
       			'message' => 'Datos borrados de la base de datos',
       	]);
    	   } catch(\Doctrine\ORM\ORMException $e) {
       		$response = new JsonResponse([
    				'success' => false,
    				'message' => $this->get('logger')->error($e->getMessage()),
    		]);
       	} catch(\Exception $e){
    	   	$response = new JsonResponse([
    				'success' => false,
    				'message' => $e->getMessage(),
       		]);
         }
    	return $response;
    }
    
    public function rehacer_bdAction(){
    	try{
       	$kernel = $this->get('kernel');
    	   $application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
    	   $application->setAutoExit(false);
    	   $options = array('command' => 'doctrine:database:drop',"--force" => true);
    	   $application->run(new ArrayInput($options))." ";
    	
    	   $this->getDoctrine()->getManager()->getConnection()->close();
    	   $options = array('command' => 'doctrine:database:create');
    	   $application->run(new ArrayInput($options))." ";
       	$options = array('command' => 'doctrine:schema:update',"--force" => true);
       	$application->run(new ArrayInput($options))." ";
    	
    	   $options = array('command' => 'doctrine:fixtures:load','--append' => true);
    	   $application->run(new ArrayInput($options));
    	   
    	   $em = $this->getDoctrine()->getManager();
    	   Helpers::defaultBDValues($em);
    	
    	   $response = new JsonResponse([
       			'success' => true,
       			'message' => 'Base de datos reiniciada',
       	]);
 	   } catch(\Doctrine\ORM\ORMException $e) {
    		$response = new JsonResponse([
    				'success' => false,
    				'message' => $this->get('logger')->error($e->getMessage()),
    		]);
    	} catch(\Exception $e){
    		$response = new JsonResponse([
    				'success' => false,
    				'message' => $e->getMessage(),
    		]);
      }
    	return $response;
    }
    
    public function limpiar_cacheAction(){
    	try{
    	   $kernel = $this->get('kernel');
    	   $application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
    	   $application->setAutoExit(false);
    	 
       	$options = array('command' => 'cache:clear');
    	   $application->run(new ArrayInput($options));
    	   $options = array('command' => 'cache:clear','--env=prod' => true);
    	   $application->run(new ArrayInput($options));
    	 
    	   $response = new JsonResponse([
       			'success' => true,
       			'message' => 'Cache limpia',
       	]);
    	} catch(\Exception $e){
    		$response = new JsonResponse([
    				'success' => false,
    				'message' => $e->getMessage(),
    		]);
      }
    	return $response;
    }
    
    public function assetic_dumpAction(){
    	try{
    	   $kernel = $this->get('kernel');
    	   $application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
    	   $application->setAutoExit(false);
    
       	$options = array('command' => 'assetic:dump');
    	   $application->run(new ArrayInput($options));
    
       	$response = new JsonResponse([
       			'success' => true,
       			'message' => 'Assetic dump OK',
       	]);
    	} catch(\Exception $e){
    		$response = new JsonResponse([
    				'success' => false,
    				'message' => $e->getMessage(),
    		]);
      }
    	return $response;
    }
    
}
