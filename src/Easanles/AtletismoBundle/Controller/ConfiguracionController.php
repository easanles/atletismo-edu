<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
    	 
    }
    
    public function borrar_bdAction(){
    	
    }
}
