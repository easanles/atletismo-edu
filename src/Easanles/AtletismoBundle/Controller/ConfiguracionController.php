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

class ConfiguracionController extends Controller {
	
    public function menu_configuracionAction() {
    	return $this->render('EasanlesAtletismoBundle:Configuracion:menu_configuracion.html.twig');		 
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
    	   $sql = 'DELETE FROM atl; DELETE FROM `cat`; DELETE FROM `cfg`; DELETE FROM `ins`; DELETE FROM `int`; DELETE FROM `not`; DELETE FROM `par`; DELETE FROM `pru`; DELETE FROM `req`; DELETE FROM `tprm`; DELETE FROM `tprf`; DELETE FROM `vrq`; DELETE FROM `com`;';
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
