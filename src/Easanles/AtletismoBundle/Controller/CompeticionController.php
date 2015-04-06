<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CompeticionController extends Controller
{
    public function listado_competicionesAction()
    {
    	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	$competiciones = $repository->findAll();
        return $this->render('EasanlesAtletismoBundle:Competicion:listado_competiciones.html.twig',
           array('competiciones' => $competiciones));
    }
}
