<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Easanles\AtletismoBundle\Entity\Usuario;
use Easanles\AtletismoBundle\Form\Type\UsuType;

class UsuarioController extends Controller {
    
   public function listadoUsuarioAction() {
   	$repoUsu = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Usuario');
   	$usuarios = $repoUsu->findAll();
   	   	
      return $this->render('EasanlesAtletismoBundle:Usuario:list_usuario.html.twig',
      		array("usuarios" => $usuarios));
   }

   public function crearUsuarioAction(Request $request) {
   	$usu = new Usuario();
   	$usu->setRol("socio");
   	$form = $this->createForm(new UsuType(), $usu);
   	
   	$form->handleRequest($request);
   	 
   	if ($form->isValid()) {
   		try {
   			$idAtl = $form->get("idAtl")->getData();
   			if (($idAtl != null) && ($idAtl !== "")){
   				$repoAtl = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
   				$atl = $repoAtl->find();
   				if ($atl == null){
   					throw new Exception("No existe el atleta con el identificador ".$form->get("idAtl")->getData());
   				}
   			}
   			$repoUsu = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Usuario');
   			$checkUsu = $repoUsu->find($usu->getNombre());
   			if ($checkUsu != null){
   				throw new Exception("Ya existe el usuario con el nombre \"".$usu->getNombre()."\"");
   			}
   			$encoder = $this->container->get('security.password_encoder');
   			$encoded = $encoder->encodePassword($usu, $usu->getContra());
   			$usu->setContra($encoded);
   			$em = $this->getDoctrine()->getManager();
            $em->persist($usu);
   			$em->flush();
   		} catch (\Exception $e) {
   			$exception = $e->getMessage();
         	return new JsonResponse([
   		   	'success' => false,
   		   	'message' => $this->render('EasanlesAtletismoBundle:Usuario:form_usuario.html.twig',
   		   	      array('form' => $form->createView(), 'mode' => 'new', 'exception' => $exception))->getContent()
   	      ]);
   		}
   		return new JsonResponse([
   		   	'success' => true,
   		   	'message' => "OK"
   	   ]);
   	}
   	
      return new JsonResponse([
   	   	'success' => false,
   	   	'message' => $this->render('EasanlesAtletismoBundle:Usuario:form_usuario.html.twig',
   	            array('form' => $form->createView(), 'mode' => 'new'))->getContent()
      ]);
   }
   
   public function borrarUsuarioAction(Request $request){
      return new Response("Borrar usuario");
   }
    
    
   public function editarUsuarioAction(Request $request, $id){
   	return new Response("Editar usuario");
   }
    
}

