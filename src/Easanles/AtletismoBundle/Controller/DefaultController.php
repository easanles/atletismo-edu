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
    	 $ayer = new \DateTime();
    	 $ayer = $ayer->sub(new \DateInterval("P1D"));
    	 $sigComs = $repoCom->findNextComs($ayer);
    	 foreach ($sigComs as $key => $com){
    	    $sigComs[$key]['numInscritos'] = count($repoCom->findAtletasIns($com['sid']));
    	 }
       return $this->render('EasanlesAtletismoBundle:Default:adminIndex.html.twig', array('sigComs' => $sigComs));
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
