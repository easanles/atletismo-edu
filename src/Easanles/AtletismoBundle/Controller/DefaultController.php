<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller{
    public function indexAction($name){
        return $this->render('EasanlesAtletismoBundle:Default:index.html.twig', array('name' => $name));
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
