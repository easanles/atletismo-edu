<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Easanles\AtletismoBundle\Entity\Competicion;
use Symfony\Component\HttpFoundation\Request;
use Easanles\AtletismoBundle\Form\Type\ComType;


class CompeticionController extends Controller
{
    public function listadoCompeticionesAction() {
    	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	$competiciones = $repository->findAll();
        return $this->render('EasanlesAtletismoBundle:Competicion:listado_competiciones.html.twig',
           array('competiciones' => $competiciones));
    }
    
    public function crearCompeticionAction(Request $request) {
    	 $com = new Competicion();
    	 $form = $this->createForm(new ComType(), $com);
    	 
    	 $form->handleRequest($request);
    	 
    	 if ($form->isValid()) {
    	 	// guardar la tarea en la base de datos
    	 	$em = $this->getDoctrine()->getManager();
    	 	$em->persist($com);
    	 	$em->flush();
    	 	return $this->redirect($this->generateUrl('listado_competiciones'));
    	 }
    	 
       return $this->render('EasanlesAtletismoBundle:Competicion:formulario_competiciones.html.twig',
             array('form' => $form->createView()));
    }
}
