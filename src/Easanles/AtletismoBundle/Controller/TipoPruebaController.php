<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TipoPruebaController extends Controller {
    
   public function listadoTipoPruebaFormatoAction() {
   	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:TipoPruebaFormato');
   	$tiposprueba = $repository->findAllOrdered();
   	
   	
      return $this->render('EasanlesAtletismoBundle:TipoPrueba:list_tipopruebaformato.html.twig',
      		array("tiposprueba" => $tiposprueba));		 
   }
}
