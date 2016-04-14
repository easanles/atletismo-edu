<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Easanles\AtletismoBundle\Helpers\Helpers;
use Easanles\AtletismoBundle\Form\Type\UserUsuType;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Console\Input\ArrayInput;

class DefaultController extends Controller{
	 private function checkDatabase(){
	 	$schemaManager = $this->getDoctrine()->getConnection()->getSchemaManager();
	 	if ($schemaManager->tablesExist(
	 	      array('atl','cat','cfg','com','ins','int_','par','pru','ron','tprf','tprm','usu')) == false) {
	 		return $this->redirect($this->generateUrl("instalar"));
	 	}
	 	$repoCfg = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Config');
	 	$checkConfig = $repoCfg->findAll();
	 	if (count($checkConfig) != 9){
	 		return $this->redirect($this->generateUrl("instalar"));
	 	}
	 	$repoCat = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Categoria');
	 	$checkCat = $repoCat->findBy(array("esTodos" => true));
	 	if (count($checkCat) != 1){
	 		return $this->redirect($this->generateUrl("instalar"));
	 	}
	 	$repoUsu = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Usuario');
	 	$checkUsu = $repoUsu->findBy(array("rol" => "coordinador"));
	 	if (count($checkUsu) == 0){
	 		return $this->redirect($this->generateUrl("instalar"));
	 	}
	 	return true;
	 }
	 
    public function indexAction(){
    	 if (in_array($this->get('kernel')->getEnvironment(), array('test', 'dev'))) {
    	    $checks = $this->checkDatabase();
    	    if ($checks !== true) return $checks;
    	 }
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
       if (in_array($this->get('kernel')->getEnvironment(), array('test', 'dev'))) {
    	    $checks = $this->checkDatabase();
    	    if ($checks !== true) return $checks;
    	 }
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
    
    public function instalarAction(){
    	 if (!in_array($this->get('kernel')->getEnvironment(), array('test', 'dev'))) {
    	 	 throw new AccessDeniedHttpException();
    	 }
    	 $schemaManager = $this->getDoctrine()->getConnection()->getSchemaManager();
    	 $nombreTablas = array('atl','cat','cfg','com','ins','int_','par','pru','ron','tprf','tprm','usu');
    	 $todoOk = true;
    	 $tablasPerdidas = array();
    	 $rehacerConfig = false;
    	 $rehacerCatTodos = false;
    	 $crearUsuAdmin = false;
    	 foreach ($nombreTablas as $tabla){
    	 	 if ($schemaManager->tablesExist(array($tabla)) == false) {
    	 	    $tablasPerdidas[] = $tabla;
    	 	    $todoOk = false;
    	 	 }
    	 }
    	 if (count($tablasPerdidas) == 12){
    	 	 $rehacerBDEntera = true;
    	 	 $todoOk = false;
    	 }
    	 else $rehacerBDEntera = false;
    	 if (!in_array('cfg', $tablasPerdidas)){
    	 	 $repoCfg = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Config');
    	 	 $checkConfig = $repoCfg->findAll();
    	 	 if (count($checkConfig) != 9) {
    	 	    $rehacerConfig = true;
    	 		 $todoOk = false;
    	 	 }
    	 }
    	 if (!in_array('cat', $tablasPerdidas)){
    	    $repoCat = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Categoria');
    	    $checkCat = $repoCat->findBy(array("esTodos" => true));
    	    if (count($checkCat) == 0){
    	 	    $rehacerCatTodos = true;
    	 	    $todoOk = false;
    	    }
    	 }
    	 if (!in_array('usu', $tablasPerdidas)){
    	    $repoUsu = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Usuario');
    	    $checkUsu = $repoUsu->findBy(array("rol" => "coordinador"));
    	    if (count($checkUsu) == 0) {
       	 	 $crearUsuAdmin = true;
    	 	    $todoOk = false;
    	    }
    	 }
    	 $parametros = array(
    	 		"todoOK" => $todoOk,
    	 		"tablasPerdidas" => $tablasPerdidas,
    	 		"rehacerBDEntera" => $rehacerBDEntera,
    	 		"rehacerConfig" => $rehacerConfig,
    	 		"rehacerCatTodos" => $rehacerCatTodos,
    	 		"crearUsuAdmin" => $crearUsuAdmin
    	 );
    	
    	 return $this->render('EasanlesAtletismoBundle:Default:pant_instalar.html.twig', $parametros);
    }
    
    public function crearBDAction(Request $request){
    	if (!in_array($this->get('kernel')->getEnvironment(), array('test', 'dev'))) {
    		return new JsonResponse([
    				'success' => false,
    				'message' => "Acceso denegado",
    		]);
    	}
    	try{
    		$kernel = $this->get('kernel');
    		$application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
    		$application->setAutoExit(false);
    		$options = array('command' => 'doctrine:database:create');
    		$application->run(new ArrayInput($options))." ";
    		$options = array('command' => 'doctrine:schema:update',"--force" => true);
    		$application->run(new ArrayInput($options))." ";
    		$options = array('command' => 'doctrine:fixtures:load','--append' => true);
    		$application->run(new ArrayInput($options));
    	   
    		Helpers::defaultBDValues($this->getDoctrine());
    		 
    		$response = new JsonResponse([
    				'success' => true,
    				'message' => 'Base de datos creada',
    		]);
    	} catch(\Doctrine\ORM\ORMException $e) {
    		$response = new JsonResponse([
    				'success' => false,
    				'message' => $this->get('logger')->error($e->getMessage()),
    		]);
    	} catch(\Exception $e){
    		$response = new JsonResponse([
    				'success' => false,
    				'message' => $e->getMessage(),
    		]);
    	}
    	return $response;
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
