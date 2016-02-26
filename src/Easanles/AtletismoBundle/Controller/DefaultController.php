<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller{
    public function indexAction($name){
    	 if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
    		 return $this->redirect($this->generateUrl("homepage_admin"));
    	 }
       return $this->render('EasanlesAtletismoBundle:Default:index.html.twig', array('name' => $name));
    }
    
    public function adminIndexAction(){
    	 $repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	 $hoy = new \DateTime();
    	 $ayer = (new \DateTime())->sub(new \DateInterval("P1D"));
    	 $sigComs = $repoCom->findComsBetween($hoy, null);
    	 $hoyComs = $repoCom->findComsBetween($ayer, $hoy);
    	 $prevDia = (new \DateTime())->sub(new \DateInterval("P2M")); //TODO: dar a elegir al usuario cuantos meses mostrar
    	 $prevComs = $repoCom->findComsBetween($prevDia, $ayer);
    	 return $this->render('EasanlesAtletismoBundle:Default:adminIndex.html.twig', array(
    	 		'sigComs' => $sigComs,
    	 		'hoyComs' => $hoyComs,
    	 		'prevComs' => $prevComs
    	 ));
    }
    
    public function loginAction(Request $request){
       $authenticationUtils = $this->get('security.authentication_utils');
       $error = $authenticationUtils->getLastAuthenticationError();
       $lastUsername = $authenticationUtils->getLastUsername();
       return $this->render('EasanlesAtletismoBundle:Default:login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error)
        );
    }

    public function loginCheckAction(){
    }
    
}
