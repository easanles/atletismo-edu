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

class ConfiguracionController extends Controller
{
    public function menu_configuracionAction() {
    	return $this->render('EasanlesAtletismoBundle:Configuracion:menu_configuracion.html.twig');		 
    }
    
    public function poblar_bdAction(){
    	try{
    	   $em = $this->getDoctrine()->getManager();
    	    	
    	   $com = new Competicion();
    	   $com->setNombre("Competicion 1")
    	       ->setTemp(2014);
    	   $em->persist($com);

       	$com = new Competicion();
       	$com->setNombre("Competicion 2")
    	      ->setTemp(2015);
    	   $em->persist($com);
    	 
    	   $tprf = new TipoPruebaFormato();
    	   $tprf->setNombre("50 metros lisos")
    	      ->setUnidades("Segundos")
    	      ->setNumInt(1);
    	   $em->persist($tprf);
    	   $em->flush();
    	   
    	   $tprm = new TipoPruebaModalidad();
    	   $tprm->setSidTprf($tprf)
    	        ->setSexo(0)
    	        ->setEntorno("Campo a través");
    	   $em->persist($tprm);
    	   
    	   $tprm = new TipoPruebaModalidad();
    	   $tprm->setSidTprf($tprf)
    	        ->setSexo(1)
    	        ->setEntorno("Campo a través");
    	   $em->persist($tprm);
    	   
    	   $tprm = new TipoPruebaModalidad();
    	   $tprm->setSidTprf($tprf)
    	        ->setSexo(0)
    	        ->setEntorno("Ruta");
    	   $em->persist($tprm);
    	   
    	   $tprm = new TipoPruebaModalidad();
    	   $tprm->setSidTprf($tprf)
    	        ->setSexo(1)
    	        ->setEntorno("Ruta");
    	   $em->persist($tprm);
    	   
    	   $tprf = new TipoPruebaFormato();
    	   $tprf->setNombre("Salto de altura")
    	      ->setUnidades("Metros")
    	      ->setNumInt("3");
    	   $em->persist($tprf);
    	   
    	   $tprm = new TipoPruebaModalidad();
    	   $tprm->setSidTprf($tprf)
    	   ->setSexo(0)
    	   ->setEntorno("Cubierto");
    	   $em->persist($tprm);
    	   
    	   $tprm = new TipoPruebaModalidad();
    	   $tprm->setSidTprf($tprf)
    	   ->setSexo(1)
    	   ->setEntorno("Cubierto");
    	   $em->persist($tprm);
    	   
    	   $tprf = new TipoPruebaFormato();
    	   $tprf->setNombre("Maratón")
    	      ->setUnidades("Segundos")
    	      ->setNumInt(1);
    	   $em->persist($tprf);
    	   $em->flush();
    	   
    	   $tprm = new TipoPruebaModalidad();
    	   $tprm->setSidTprf($tprf)
    	        ->setSexo(0)
    	        ->setEntorno("Ruta");
    	   $em->persist($tprm);
    	   
    	   $tprm = new TipoPruebaModalidad();
    	   $tprm->setSidTprf($tprf)
    	        ->setSexo(1)
    	        ->setEntorno("Ruta");
    	   $em->persist($tprm);
    	   
    	   $tprf = new TipoPruebaFormato();
    	   $tprf->setNombre("Prueba por puntos")
    	        ->setUnidades("Puntos")
    	        ->setNumInt(1);
    	   $em->persist($tprf);
    	   $em->flush();
    	   
    	   $tprm = new TipoPruebaModalidad();
    	   $tprm->setSidTprf($tprf)
    	        ->setSexo(2)
    	         ->setEntorno("Cubierto");
    	   $em->persist($tprm);
    	   
    	   $em->flush();
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
    	   $sql = 'DELETE FROM atl; DELETE FROM `cat`; DELETE FROM `cfg`; DELETE FROM `com`; DELETE FROM `ins`; DELETE FROM `int`; DELETE FROM `not`; DELETE FROM `par`; DELETE FROM `pru`; DELETE FROM `req`; DELETE FROM `tprm`; DELETE FROM `tprf`; DELETE FROM `vrq`;';
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
