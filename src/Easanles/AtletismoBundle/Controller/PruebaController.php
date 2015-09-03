<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Easanles\AtletismoBundle\Entity\Prueba;

class PruebaController extends Controller
{
    public function listadoPruebasAction($id) {
    	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	$com = $repository->find($id);
    	//$nombreCom = $com->getNombre();
    	//$tempCom = $com->getTemp();
    	//$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Prueba');
    	//$pruebas = $repository->findFor($id);    	 
    	 
    	return $this->render('EasanlesAtletismoBundle:Prueba:list_prueba.html.twig',
    	      array('id' => $id,
    	      		'com' => $com,
    	      ));
    }
}
