<?php

# Copyright (c) 2016 Eduardo Alonso Sanlés
# Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php

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
use Symfony\Component\Console\Output\BufferedOutput;

class ConfiguracionController extends Controller {
	
    public function menu_configuracionAction() {
    	if (in_array($this->get('kernel')->getEnvironment(), array('test', 'dev'))) {
    		$accDebug = true;
    	} else $accDebug = false;
    	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Config');
    	$fIniTempObj = $repository->findOneBy(array("clave" => "fIniTemp"));
    	if ($fIniTempObj == null) $fIniTempVal = "";
      else $fIniTempVal = $fIniTempObj->getValor();
      $catAsig = $repository->findOneBy(array("clave" => "catAsig"));
      $leyenda = $repository->findOneBy(array("clave" => "leyenda"));
      $numResultados = $repository->findOneBy(array("clave" => "numresultados"));
      $noOficiales = $repository->findOneBy(array("clave" => "nooficiales"));
      $jumbotron = $repository->findOneBy(array("clave" => "jumbotron"));
      $jumboLinea1 = $repository->findOneBy(array("clave" => "jumbolin1"));
      $jumboLinea2 = $repository->findOneBy(array("clave" => "jumbolin2"));
      $bienvenida = $repository->findOneBy(array("clave" => "bienvenida"));
      $verMeses = $repository->findOneBy(array("clave" => "vermeses"));
      $ajContent = $this->render('EasanlesAtletismoBundle:Configuracion:form_ajustes.html.twig', array(
    			"fIniTemp" => $fIniTempVal,
    			"catAsig" => $catAsig->getValor(),
    			"leyenda" => $leyenda->getValor(),
      		"numResultados" => $numResultados->getValor(),
      		"noOficiales" => $noOficiales->getValor(),
      		"jumbotron" => intval($jumbotron->getValor()),
      		"jumboLinea1" => $jumboLinea1->getValor(),
      		"jumboLinea2" => $jumboLinea2->getValor(),
      		"bienvenida" => $bienvenida->getValor(),
      		"verMeses" => $verMeses->getValor()
    	))->getContent();
    	
    	return $this->render('EasanlesAtletismoBundle:Configuracion:menu_configuracion.html.twig', array(
    			"ajustesContent" => $ajContent, "accDebug" => $accDebug
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
    	 	 $parametros["fIniTemp"] = $fIniTempString;
    	 	  if (!Helpers::checkDayMonth($fIniTempString)){
    	 	  	  $parametros["errFIniTemp"] = "Formato de fecha: dd/mm (dd = dia, mm = mes)";
    	 	  } else {
    	 	  	  $datos = explode("/", $fIniTempString);
    	 	  	  if (!checkdate(trim($datos[1]), trim($datos[0]), 2015)){ //Cualquier año no bisiesto
    	 	  		  $parametros["errFIniTemp"] = "Fecha no valida";
    	 	  	  } else {
    	 	  	  	  $prevString = $fIniTempObj->getValor();  	 	  	  	   
    	 	  	  	  if (!(strcmp($prevString, $fIniTempString) == 0)) { //Comprobar si hay cambios
    	 	  	  	     $parametros["okFIniTemp"] = true;
    	 	  		     $fIniTempObj->setValor(trim($datos[0])."/".trim($datos[1]));
    	 	  	  	  }
    	 	  	  }
    	 	  }
    	 }
    	 
    	 //FECHA DE ASIGNACION DE CATEGORÍAS
    	 $catAsigObj = $repository->findOneBy(array("clave" => "catAsig"));
    	 $catAsigString = $request->request->get('catAsig');
    	 if (($catAsigString == "temp") || ($catAsigString == "year") || ($catAsigString == "daily")){
    	 	 if (!(strcmp($catAsigString, $catAsigObj->getValor()) == 0)){
    	 	    $parametros["okCatAsig"] = true;
    	 	 	 $catAsigObj->setValor($catAsigString);
    	 	 }
    	 	 $parametros["catAsig"] = $catAsigString;
    	 } else {
    	    $parametros["errCatAsig"] = "ERROR";
    	 	 $parametros["catAsig"] = $catAsigObj->getValor();
    	 }
    	 
    	 //LEYENDA DE NOTAS EN RESULTADOS
    	 $leyendaObj = $repository->findOneBy(array("clave" => "leyenda"));
    	 $leyendaString = $request->request->get('leyenda');
    	 if (!(strcmp($leyendaString, $leyendaObj->getValor()) == 0)){
    	 	 $parametros["okLeyenda"] = true;
    	 	 $leyendaObj->setValor($leyendaString);
    	 	 $parametros["leyenda"] = $leyendaString;
    	 } else {
    	    $parametros["leyenda"] = $leyendaObj->getValor();
    	 }
    	   	 
    	 //NUMERO DE RESULTADOS POR PAGINA
    	 $numResultadosObj = $repository->findOneBy(array("clave" => "numresultados"));
    	 $numResultadosString = $request->request->get('numresultados');
    	 if ((is_numeric($numResultadosString)) && (intval($numResultadosString) >= 1)){
    	 	if (intval($numResultadosString) != $numResultadosObj->getValor()){
    	 		$parametros["okNumResultados"] = true;
    	 		$numResultadosObj->setValor($numResultadosString);
    	 	}
    	 } else {
    	 	$parametros["errNumResultados"] = "Introduzca un valor entero mayor o igual que 1";
    	 }
    	 $parametros["numResultados"] = $numResultadosString;
    	 
    	 //MOSTRAR MARCAS NO OFICIALES EN LAS TABLAS DE RECORDS
    	 $noOficialesObj = $repository->findOneBy(array("clave" => "nooficiales"));
    	 $noOficialesString = $request->request->get('nooficiales');
       if (($noOficialesString == "on"))
    	 	    $noOficialesVal = 1;
    	 else $noOficialesVal = 0;
    	 if ($noOficialesVal != $noOficialesObj->getValor()){
    	 	$parametros["okNoOficiales"] = true;
    	 	$noOficialesObj->setValor($noOficialesVal);
    	 	$parametros["noOficiales"] = $noOficialesVal;
    	 } else {
    	 	$parametros["noOficiales"] = $noOficialesObj->getValor();
    	 }
    	 
    	 //ACTIVAR JUMBOTRON
    	 $jumbotronObj = $repository->findOneBy(array("clave" => "jumbotron"));
    	 $jumbotronString = $request->request->get('jumbotron');
    	 if (($jumbotronString == "on"))
    	 	    $jumbotronVal = 1;
    	 else $jumbotronVal = 0;
    	 if ($jumbotronVal != $jumbotronObj->getValor()){
    	 	$parametros["okJumbotron"] = true;
    	 	$jumbotronObj->setValor($jumbotronVal);
    	 	$parametros["jumbotron"] = $jumbotronVal;
    	 } else {
    	 	$parametros["jumbotron"] = $jumbotronObj->getValor();
    	 }
    	 
    	 //JUMBOTRON LINEA 1
    	 $jumboLinea1Obj = $repository->findOneBy(array("clave" => "jumbolin1"));
    	 $jumboLinea1String = $request->request->get('jumbolin1');
    	 if (!(strcmp($jumboLinea1String, $jumboLinea1Obj->getValor()) == 0)){
    	 	$parametros["okJumboLinea1"] = true;
    	 	$jumboLinea1Obj->setValor($jumboLinea1String);
    	 	$parametros["jumboLinea1"] = $jumboLinea1String;
    	 } else {
    	 	$parametros["jumboLinea1"] = $jumboLinea1Obj->getValor();
    	 }
    	 
       //JUMBOTRON LINEA 2
    	 $jumboLinea2Obj = $repository->findOneBy(array("clave" => "jumbolin2"));
    	 $jumboLinea2String = $request->request->get('jumbolin2');
    	 if (!(strcmp($jumboLinea2String, $jumboLinea2Obj->getValor()) == 0)){
    	 	$parametros["okJumboLinea2"] = true;
    	 	$jumboLinea2Obj->setValor($jumboLinea2String);
    	 	$parametros["jumboLinea2"] = $jumboLinea2String;
    	 } else {
    	 	$parametros["jumboLinea2"] = $jumboLinea2Obj->getValor();
    	 }
    	     	 
    	 //TEXTO DE BIENVENIDA
    	 $bienvenidaObj = $repository->findOneBy(array("clave" => "bienvenida"));
    	 $bienvenidaString = $request->request->get('bienvenida');
    	 if (!(strcmp($bienvenidaString, $bienvenidaObj->getValor()) == 0)){
    	 	$parametros["okBienvenida"] = true;
    	 	$bienvenidaObj->setValor($bienvenidaString);
    	 	$parametros["bienvenida"] = $bienvenidaString;
    	 } else {
    	 	$parametros["bienvenida"] = $bienvenidaObj->getValor();
    	 }
    	 
    	 //VER MESES PREVIOS EN RESULTADOS RECIENTES
    	 $verMesesObj = $repository->findOneBy(array("clave" => "vermeses"));
    	 $verMesesString = $request->request->get('vermeses');
    	 if ((is_numeric($verMesesString)) && (intval($verMesesString) >= 1)){
    	 	if (intval($verMesesString) != $verMesesObj->getValor()){
    	 		$parametros["okVerMeses"] = true;
    	 		$verMesesObj->setValor($verMesesString);
    	 	}
    	 } else {
    	 	$parametros["errVerMeses"] = "Introduzca un valor entero mayor o igual que 1";
    	 }
    	 $parametros["verMeses"] = $verMesesString;
    	 
    	 $em->flush();
    	 return new JsonResponse([
       	'success' => true,
       	'message' => $this->render('EasanlesAtletismoBundle:Configuracion:form_ajustes.html.twig', $parametros)->getContent()
       ]);
    }
    
    public function poblar_bdAction(){
      if (!in_array($this->get('kernel')->getEnvironment(), array('test', 'dev'))) {
    		return new JsonResponse([
    				'success' => false,
    				'message' => "Comando disponible solo en modo debug",
    		]);
    	}
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
    	   $sql = '
    	   		DELETE FROM `par`;
    	   		DELETE FROM `ins`;
    	   		DELETE FROM `int_`;
    	   		DELETE FROM `usu`;
    	   		DELETE FROM `atl`;
    	   		DELETE FROM `ron`;
    	   		DELETE FROM `pru`;
    	   		DELETE FROM `cat`;
    	   		DELETE FROM `tprm`;
    	   		DELETE FROM `tprf`;
    	   		DELETE FROM `com`;
    	   		DELETE FROM `cfg`;
    	   ';
    	   $connection = $em->getConnection();
    	   $stmt = $connection->prepare($sql);
    	   $stmt->execute();
    	   $stmt->closeCursor();
    	   Helpers::defaultBDValues($this->getDoctrine());
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
    	if (!in_array($this->get('kernel')->getEnvironment(), array('test', 'dev'))) {
    		return new JsonResponse([
    				'success' => false,
    				'message' => "Comando disponible solo en modo debug",
    		]);
    	}
    	try{
       	$kernel = $this->get('kernel');
    	   $application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
    	   $application->setAutoExit(false);
    	   $options = array('command' => 'doctrine:database:drop',"--force" => true);
    	   $application->run(new ArrayInput($options));
    	
    	   $this->getDoctrine()->getManager()->getConnection()->close();
    	   $options = array('command' => 'doctrine:database:create');
    	   $application->run(new ArrayInput($options));
       	$options = array('command' => 'doctrine:schema:update',"--force" => true);
       	$application->run(new ArrayInput($options));
    	
    	   $options = array('command' => 'doctrine:fixtures:load','--append' => true);
    	   $application->run(new ArrayInput($options));
    	   
    	   Helpers::defaultBDValues($this->getDoctrine());
    	
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
    	 
    	   $output = new BufferedOutput();
       	$options = array('command' => 'cache:clear');
    	   $code = $application->run(new ArrayInput($options), $output);
    	   //$options = array('command' => 'cache:clear','--env' => "prod");
    	   //$application->run(new ArrayInput($options), $output);
    	   $content = $output->fetch();
    	   
    	   if ($code == 0){
    	      $response = new JsonResponse([
       		   	'success' => true,
       			   'message' => 'Cache limpia.<br><strong>Consola</strong>: '.$content,
       	   ]);
    	   } else {
    	   	$response = new JsonResponse([
    	   			'success' => false,
    	   			'message' => $content,
    	   	]);
    	   }
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
    
       	$options = array('command' => 'assetic:dump', '--env' => "prod", '--no-debug');
       	$output = new BufferedOutput();
       	
    	   $code = $application->run(new ArrayInput($options), $output);
    	   $content = $output->fetch();
         
    	   if ($code == 0){
    	   	$response = new JsonResponse([
    	   			'success' => true,
    	   			'message' => 'Assetic dump OK. Código: '.$code."<br><strong>Consola</strong>: ".$content,
    	   	]);
    	   } else {
       	   $response = new JsonResponse([
       			   'success' => false,
       			   'message' => $content,
       	   ]);
    	   }
    	} catch(\Exception $e){
    		$response = new JsonResponse([
    				'success' => false,
    				'message' => $e->getMessage(),
    		]);
      }
    	return $response;
    }
    
}
