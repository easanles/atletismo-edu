<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class UsuarioController extends Controller {
    
   public function listadoUsuarioAction() {
   	$repoUsu = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Usuario');
   	$usuarios = $repoUsu->findAll();
   	   	
      return $this->render('EasanlesAtletismoBundle:Usuario:list_usuario.html.twig',
      		array("usuarios" => $usuarios));
   }

   public function crearUsuarioAction(Request $request) {
      return new Response("Crear usuario");
   }
   
   public function borrarUsuarioAction(Request $request){
      return new Response("Borrar usuario");
   }
    
    
   public function editarUsuarioAction(Request $request, $id){
   	return new Response("Editar usuario");
   }
    
}

