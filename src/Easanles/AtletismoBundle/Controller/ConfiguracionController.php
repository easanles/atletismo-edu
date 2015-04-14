<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Easanles\AtletismoBundle\Entity\Competicion;
use Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\VarDumper;

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
    	 
    	$comp = new Competicion();
        $comp->setNombre("Competición 1")
             ->setTemporada("2012/2013")
             ->setTipo("Campo a través")
             ->setProvincia("A Coruña")
             ->setEsOficial(true)
             ->setEsFederada(true);
    	$em->persist($comp);
    	
    	$comp = new Competicion();
    	$comp->setNombre("Competición 2")
    	     ->setTemporada("2013/2014")
    	     ->setTipo("Ruta")
    	     ->setProvincia("A Coruña")
    	     ->setEsOficial(true)
    	     ->setEsFederada(false);
    	$em->persist($comp);
    	
    	$comp = new Competicion();
    	$comp->setNombre("Competición 3")
    	     ->setTemporada("2013/2014")
    	     ->setTipo("Ruta")
    	     ->setProvincia("Ourense")
    	     ->setEsOficial(false)
    	     ->setEsFederada(false);
    	$em->persist($comp);
    	
    	$em->flush();
    	
    	$response = new Response('Base de datos poblada con datos de prueba <a href="..">Volver</a>');
    	$response->headers->set('Refresh', '2; url=..');
    	return $response;	 
    }
    
    public function borrar_bdAction(){
    	$em = $this->getDoctrine()->getManager();
    	$sql = 'DELETE FROM Competicion;';
    	$connection = $em->getConnection();
    	$stmt = $connection->prepare($sql);
    	$stmt->execute();
    	$stmt->closeCursor();
    	$response = new Response('Datos borrados <a href="..">Volver</a>');
    	$response->headers->set('Refresh', '2; url=..');
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
    	
    	$response = new Response('Base de datos reiniciada <a href="..">Volver</a>');
    	$response->headers->set('Refresh', '2; url=..');
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
    	 
    	$response = new Response('Cache limpia <a href="..">Volver</a>');
    	$response->headers->set('Refresh', '2; url=..');
    	return $response;
    }
    
    public function assetic_dumpAction(){
    	$kernel = $this->get('kernel');
    	$application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
    	$application->setAutoExit(false);
    
    	$options = array('command' => 'assetic:dump');
    	$application->run(new ArrayInput($options));
    
    	$response = new Response('Assetic dump OK <a href="..">Volver</a>');
    	$response->headers->set('Refresh', '2; url=..');
    	return $response;
    }
    
}
