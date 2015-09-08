<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Easanles\AtletismoBundle\Entity\Prueba;
use Symfony\Component\HttpFoundation\Response;

class PruebaController extends Controller
{
    public function listadoPruebasAction($id) {
    	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	$com = $repository->find($id);
    	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Prueba');
    	$listaPru = $repository->findTpr($id);
    	 
    	if ($com != null ){
    	return $this->render('EasanlesAtletismoBundle:Prueba:list_prueba.html.twig',
    	      array('id' => $id,
    	      		'com' => $com,
    	      		'listapru' =>$listaPru
    	      ));
    	} else {
       	$response = new Response('No existe la competicion con el identificador "'.$id.'" <a href="../competiciones">Volver</a>');
       	$response->headers->set('Refresh', '2; url=../competiciones');
       	return $response;
       }
    }
    
}
