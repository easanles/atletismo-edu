<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Easanles\AtletismoBundle\Entity\Miembro;

class MiembroController extends Controller
{
    public function mostrarAction($id)
    {
    	#$miembro = new Miembro();
    	#$miembro->setDni('38562840G');
    	#$miembro->setNombre('Pablito');
    	
    	#$em = $this->getDoctrine()->getManager();
    	#$em->persist($miembro);
    	#$em->flush();
    	
    	$dato = $this->getDoctrine()
           ->getRepository('EasanlesAtletismoBundle:Miembro')
           ->find($id);
    	
    	if (!$dato) {
    		throw $this->createNotFoundException('No existe el miembro con id '.$id);
    	}
    	
        return $this->render('EasanlesAtletismoBundle:Miembro:miembro.html.twig',
           array('id' => $id, 'miembro' => $dato));
    }
}
