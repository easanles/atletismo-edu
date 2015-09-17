<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CategoriaController extends Controller
{
    public function listadoCategoriasAction()
    {
    	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Categoria');
    	$categorias = $repository->findAll();
    	
    	return $this->render('EasanlesAtletismoBundle:Categoria:list_categoria.html.twig',
    			array("categorias" => $categorias));
    }
}
