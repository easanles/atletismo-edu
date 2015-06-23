<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Easanles\AtletismoBundle\Entity\Competicion;
use Symfony\Component\HttpFoundation\Request;

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
    	 $form = $this->createFormBuilder($com)
    	    ->add('nombre', 'text')
    	    ->add('temp', 'integer')
    	    ->add('ubicacion', 'text')
    	    ->add('sede', 'text')
    	    ->add('fecha', 'date')
    	    ->add('desc', 'textarea')
    	    ->add('nivel', 'text')
    	    ->add('feder', 'text')
    	    ->add('web', 'url')
    	    ->add('email', 'email')
    	    ->add('cartel', 'file')
    	    ->add('esfeder', 'checkbox')
    	    ->add('esoficial', 'checkbox')
    	    ->add('enviar', 'submit')
    	    ->getForm();
    	 
    	 $form->handleRequest($request);
    	 
    	 if ($form->isValid()) {
    	 	// guardar la tarea en la base de datos
    	 
    	 	return $this->redirect($this->generateUrl('listado_competiciones'));
    	 }
    	 
       return $this->render('EasanlesAtletismoBundle:Competicion:formulario_competiciones.html.twig',
             array('form' => $form->createView()));
    }
}
