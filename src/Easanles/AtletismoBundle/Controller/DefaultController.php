<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Easanles\AtletismoBundle\Helpers\Helpers;
use Easanles\AtletismoBundle\Form\Type\UserUsuType;

class DefaultController extends Controller{
    public function indexAction(){
    	 if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
    		 return $this->redirect($this->generateUrl("homepage_admin"));
    	 } else if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
    	 	 return $this->redirect($this->generateUrl("mis_competiciones"));
    	 }
    	 $parametros = array();
    	 $repoCfg = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Config');
    	 $parametros['jumbotron'] = $repoCfg->find("jumbotron")->getValor();
    	 $parametros['jumbolin1'] = $repoCfg->find("jumbolin1")->getValor();
    	 $parametros['jumbolin2'] = $repoCfg->find("jumbolin2")->getValor();
    	 $parametros['bienvenida'] = $repoCfg->find("bienvenida")->getValor();
    	 //$parametros['nombreapp'] = $repoCfg->find("nombreapp")->getValor();
    	 $authenticationUtils = $this->get('security.authentication_utils');
    	 $parametros['error'] = $authenticationUtils->getLastAuthenticationError();
    	 $parametros['last_username'] = $authenticationUtils->getLastUsername();
       return $this->render('EasanlesAtletismoBundle:Default:index.html.twig', $parametros);
    }
    
    public function adminIndexAction(){
    	 $repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	 $sigComs = $repoCom->findComsTimedList("sig", null);
    	 $hoyComs = $repoCom->findComsTimedList("hoy", null);
    	 $repoCfg = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Config');
    	 $verMeses = $repoCfg->find("vermeses")->getValor();
    	 $prevDia = (new \DateTime())->sub(new \DateInterval("P".$verMeses."M"));
    	 $prevComs = $repoCom->findComsTimedList("pre", $prevDia);
    	 return $this->render('EasanlesAtletismoBundle:Default:adminIndex.html.twig', array(
    	 		'sigComs' => $sigComs,
    	 		'hoyComs' => $hoyComs,
    	 		'prevComs' => $prevComs
    	 ));
    }
    
    public function pantallaCuentaAction(Request $request){
    	$user = $this->getUser();
    	if ($user == null){
    		return $this->redirect($this->generateUrl("login"));
    	}
    	$atl = $user->getIdAtl();
    	$parametros = array();
    	if ($atl != null){
    		$parametros['categoria'] = Helpers::getAtlCurrentCat($this->getDoctrine(), $atl)['nombre'];
    		$parametros['edad'] = Helpers::getEdad($atl->getFnac(), null);
    	}
    	$form = $this->createForm(new UserUsuType(), $user);
    	
    	$form->handleRequest($request);
    	
    	if ($form->isValid()) {
    		try {
    			$contra = $form->get("contra")->getData();
    			if (($contra != null) && ($contra !== "")) {
    				$encoder = $this->container->get('security.password_encoder');
    				$encoded = $encoder->encodePassword($user, $contra);
    				$user->setContra($encoded);
    			}
    			$em = $this->getDoctrine()->getManager();
    			$em->persist($user);
    			$em->flush();
    			$parametros['ok'] = true;
    		} catch (\Exception $e) {
    			$parametros['exception'] = $e->getMessage();
    			$parametros['form'] = $form->createView();
    		}
    	}
    	$parametros['form'] = $form->createView();
    	return $this->render('EasanlesAtletismoBundle:Default:pant_cuenta.html.twig', $parametros);
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
