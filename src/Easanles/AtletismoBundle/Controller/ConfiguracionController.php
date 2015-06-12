<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\VarDumper;
use Easanles\AtletismoBundle\Entity\Prueba;
use Easanles\AtletismoBundle\Entity\Competicion;

class ConfiguracionController extends Controller
{
    public function menu_configuracionAction()
    {
    	#$dato = $this->getDoctrine()
    	#->getRepository('EasanlesAtletismoBundle:Competicion')
    	#->find($id);
    	
    	#if (!$dato) {
    	#	throw $this->createNotFoundException('No existe el miembro con id '.$id);
    	#}
    	
    	#return $this->render('EasanlesAtletismoBundle:Miembro:miembro.html.twig',
    	#		array('id' => $id, 'miembro' => $dato));
    	
    	return $this->render('EasanlesAtletismoBundle:Configuracion:menu_configuracion.html.twig');		 
    }
    
    public function poblar_bdAction(){
    	$em = $this->getDoctrine()->getManager();
    	    	
    	$obj = new Competicion();
    	$obj->setNombre("Competicion 1")
    	    ->setTemp(2015);
    	$em->persist($obj);

    	$obj = new Competicion();
    	$obj->setNombre("Competicion 2")
    	    ->setTemp(2014);
    	$em->persist($obj);
    	 
    	$obj = new Prueba();
    	$obj->setId(1234)
    	    ->setNombreCom("Competicion 1")
    	    ->setTempCom(2015)
    	    ->setRonda(1)
    	    ->setIdCat(1)
    	    ->setNombreTpr("100 metros lisos")
    	    ->setSexoTpr(0)
    	    ->setEntornoTpr("Pista cubierta");
    	$em->persist($obj);
    	
    	$em->flush();
    	
    	$response = new Response('Base de datos poblada con datos de prueba');
    	return $response;	 
    }
    
    public function borrar_bdAction(){
    	$em = $this->getDoctrine()->getManager();
    	$sql = 'DELETE FROM com; DELETE FROM pru';
    	$connection = $em->getConnection();
    	$stmt = $connection->prepare($sql);
    	$stmt->execute();
    	$stmt->closeCursor();
    	$response = new Response('Datos borrados');
    	return $response;
    }
    
    public function rehacer_bdAction(){
    	$kernel = $this->get('kernel');
    	$application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
    	$application->setAutoExit(false);
    	//Drop tables
    	$options = array('command' => 'doctrine:database:drop',"--force" => true);
    	$application->run(new ArrayInput($options))." ";
    	
    	$this->getDoctrine()->getManager()->getConnection()->close();
    	//Create database
    	$options = array('command' => 'doctrine:database:create');
    	$application->run(new ArrayInput($options))." ";
    	//Schema update
    	$options = array('command' => 'doctrine:schema:update',"--force" => true);
    	$application->run(new ArrayInput($options))." ";
    	
    	$options = array('command' => 'doctrine:fixtures:load','--append' => true);
    	$application->run(new ArrayInput($options));
    	
    	$response = new Response('Base de datos reiniciada');
    	return $response;
    }
    
    public function limpiar_cacheAction(){
    	$kernel = $this->get('kernel');
    	$application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
    	$application->setAutoExit(false);
    	 
    	$options = array('command' => 'cache:clear');
    	$application->run(new ArrayInput($options));
    	$options = array('command' => 'cache:clear','--env=prod' => true);
    	$application->run(new ArrayInput($options));
    	 
    	$response = new Response('Cache limpia');
    	return $response;
    }
    
    public function assetic_dumpAction(){
    	$kernel = $this->get('kernel');
    	$application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
    	$application->setAutoExit(false);
    
    	$options = array('command' => 'assetic:dump');
    	$application->run(new ArrayInput($options));
    
    	$response = new Response('Assetic dump OK');
    	return $response;
    }
    
}
