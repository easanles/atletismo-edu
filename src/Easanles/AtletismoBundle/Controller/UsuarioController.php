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
   	$usuarios = $repoUsu->findAllOrdered();
   	$repoAtl = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
   	foreach($usuarios as $key => $usu){
   		if ($usu['idAtl'] != null){
   			$usuarios[$key]['atl'] = $repoAtl->find($usu['idAtl']);
   		}
   	}
   	   	
      return $this->render('EasanlesAtletismoBundle:Usuario:list_usuario.html.twig',
      		array("usuarios" => $usuarios));
   }

   public function crearUsuarioAction(Request $request) {
   	$usu = new Usuario();
   	$usu->setRol("socio");
   	$form = $this->createForm(new UsuType("new"), $usu);
   	
   	$form->handleRequest($request);
   	 
   	if ($form->isValid()) {
   		try {
   			$idAtl = $form->get("idAtl")->getData();
   			if (($idAtl != null) && ($idAtl !== "")){
   				$repoAtl = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
   				$atl = $repoAtl->find($idAtl);
   				if ($atl == null){
   					throw new Exception("No existe el atleta con el identificador \"".$form->get("idAtl")->getData()."\".");
   				}
   				if ($atl->getNombreUsu() != null){
   					throw new Exception("Este atleta ya tiene otro usuario asociado (".$atl->getNombreUsu().").");
   				}
   				$usu->setIdAtl($atl);
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
   	$nombreUsu = $request->query->get('usu');
   	if(($nombreUsu == null) || ($nombreUsu === "")){
   		return new JsonResponse([
   				'success' => false,
   				'message' => "No se ha recibido el parámetro necesario."
   		]);
   	}
   	$em = $this->getDoctrine()->getManager();
   	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Usuario');
   	$usu = $repository->find($nombreUsu);
   	if ($usu == null){
         return new JsonResponse([
   		   'success' => false,
   		   'message' => "No existe el usuario \"".$nombreUsu."\"."
   	   ]);
   	} else {		 
   		try {
   		   $em->remove($usu);
   			$em->flush();
   		} catch (\Exception $e) {
   			return new JsonResponse([
   					'success' => false,
   					'message' => $e->getMessage()
   			]);
   		}
   		return new JsonResponse([
   		   'success' => true,
   		   'message' => "OK"
   	   ]);
   	}
      
   }
    
    
   public function editarUsuarioAction(Request $request, $nombre){
   	$em = $this->getDoctrine()->getManager();
   	$repoUsu = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Usuario');
   	$usu = $repoUsu->find($nombre);
   	if ($usu == null){
   		return new JsonResponse([
   				'success' => false,
   				'message' => "No existe el usuario con nombre \"".$nombre."\"."
   		]);
   	}
   	$prevNombre = $usu->getNombre();
   	$prevAtl = $usu->getIdAtl();
   	$form = $this->createForm(new UsuType("edit"), $usu);
   	$atl = $usu->getIdAtl();
   	if ($atl != null){
   		$form->get('idAtl')->setData($atl->getId());
   	}
   	
   	$form->handleRequest($request);
   	
   	if ($form->isValid()) {
   		try {
   			$nombreAhora = $usu->getNombre();
   			if ($prevNombre !== $nombreAhora){
   				$prevUsu = $repoUsu->find($nombreAhora);
   				if ($prevUsu != null) {
   					throw new Exception("Ya existe un usuario con el nombre \"".$nombreAhora."\"");
   				}
   			}
   			$idAtl = $form->get("idAtl")->getData();
   			if (($idAtl != null) && ($idAtl !== "")){
   				if (($prevAtl == null) || ($prevAtl->getId() != $idAtl)){
   					$repoAtl = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
   					$atl = $repoAtl->find($idAtl);
   					if ($atl == null){
   						throw new Exception("No existe el atleta con el identificador \"".$form->get("idAtl")->getData()."\".");
   					}
   					if ($atl->getNombreUsu() != null){
   						throw new Exception("Este atleta ya tiene otro usuario asociado (".$atl->getNombreUsu()->getNombre().").");
   					}
   				}
   				$usu->setIdAtl($atl);
   			} else {
   				$usu->setIdAtl(null);
   			}
   			$contra = $form->get("contra")->getData();
   			if (($contra != null) && ($contra !== "")) {
   				$encoder = $this->container->get('security.password_encoder');
   				$encoded = $encoder->encodePassword($usu, $contra);
   				$usu->setContra($encoded);
   			}
   			$em = $this->getDoctrine()->getManager();
   			$em->persist($usu);
   			$em->flush();
   		} catch (\Exception $e) {
   			$exception = $e->getMessage();
         	return new JsonResponse([
   		   	'success' => false,
   		   	'message' => $this->render('EasanlesAtletismoBundle:Usuario:form_usuario.html.twig',
   		   	      array('form' => $form->createView(), 'mode' => 'edit', 'nombre' => $nombre, 'exception' => $exception))->getContent()
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
   	            array('form' => $form->createView(), 'mode' => 'edit', 'nombre' => $nombre))->getContent()
      ]);
   }
    
}

