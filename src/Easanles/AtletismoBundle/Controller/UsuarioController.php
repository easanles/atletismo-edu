<?php

# Copyright (c) 2016 Eduardo Alonso Sanlés
# Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Easanles\AtletismoBundle\Entity\Usuario;
use Easanles\AtletismoBundle\Form\Type\UsuType;

class UsuarioController extends Controller {
    
   public function listadoUsuarioAction(Request $request) {
   	$repoUsu = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Usuario');
   	$query = $request->query->get('q');
   	
   	$from = $request->query->get('from');
   	if (($from == null) || ($from == "")) $from = 0;
   	else $from = intval($from);
   	if ($from < 0) $from = 0;
   	$repoCfg = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Config');
   	$numResultados = $repoCfg->findOneBy(array("clave" => "numresultados"))->getValor();
   	
   	if (($query != null) && ($query !== "")){
   		$usuarios = $repoUsu->searchByParameter($query, $from, $numResultados);
   	} else {
   		$usuarios = $repoUsu->findAllOrdered($from, $numResultados);
   	}
   	$repoAtl = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
   	foreach($usuarios as $key => $usu){
   		if ($usu['idAtl'] != null){
   			$usuarios[$key]['atl'] = $repoAtl->find($usu['idAtl']);
   		}
   	}
      return $this->render('EasanlesAtletismoBundle:Usuario:list_usuario.html.twig',
      		array("usuarios" => $usuarios, 'from' => $from, 'numResultados' => $numResultados));
   }

   public function crearUsuarioAction(Request $request) {
   	$usu = new Usuario();
   	$usu->setRol("socio");
   	$form = $this->createForm(new UsuType("new", false), $usu);
   	
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
   	$repoUsu = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Usuario');
   	$usu = $repoUsu->find($nombreUsu);
   	if ($usu == null){
         return new JsonResponse([
   		   'success' => false,
   		   'message' => "No existe el usuario \"".$nombreUsu."\"."
   	   ]);
   	} else {		 
   		try {
   			if (($usu->getRol() === "coordinador") && ($repoUsu->countCoordinadores() == 1)){
   				throw new Exception("No se puede borrar el último coordinador");
   			}
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
   	$prevRol = $usu->getRol();
   	$form = $this->createForm(new UsuType("edit", false), $usu);
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
   			if (($prevRol === "coordinador") && ($usu->getRol() !== "coordinador") && ($repoUsu->countCoordinadores() == 1)){
   				throw new Exception("Debe haber al menos un coordinador");
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

