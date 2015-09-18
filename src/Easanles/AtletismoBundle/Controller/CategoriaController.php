<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Easanles\AtletismoBundle\Entity\Categoria;
use Symfony\Component\HttpFoundation\JsonResponse;
use Easanles\AtletismoBundle\Form\Type\CatType;
use Symfony\Component\Config\Definition\Exception\Exception;

class CategoriaController extends Controller
{
    public function listadoCategoriasAction(Request $request) {
    	$outdated = $request->query->get('outd');
    	
    	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Categoria');
    	if ($outdated == "false") $categorias = $repository->findAllCurrent();
    	else if ($outdated == "true")$categorias = $repository->findAll();
    	
    	return $this->render('EasanlesAtletismoBundle:Categoria:list_categoria.html.twig',
    			array("categorias" => $categorias));
    }
    
    public function crearCategoriaAction(Request $request) {
    	$cat = new Categoria();
    	$cat->setTIniVal(date("Y"));
    	
    	$form = $this->createForm(new CatType(), $cat);
    	
    	$form->handleRequest($request);
    	
    	if ($form->isValid()) {
    		try {
    			$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Categoria');
    			 
    			if (($cat->getEdadMax() == null) || ($cat->getEdadMax() == "")) {
    				$nullCat = $repository->findCurrentEdadMaxNull();
    				if ($nullCat != null){
    					throw new Exception("Solo puede haber una categoría vigente sin edad máxima");
    				}
    			}
    			/*
    			 * get nombre
    			 * si existe nombre vigente
    			 * avisar por si hay que caducarla
    			 */
    			
    			$em = $this->getDoctrine()->getManager();
    			$em->persist($cat);
    			$em->flush();
    		} catch (\Exception $e) {
    			$exception = $e->getMessage();
    			return new JsonResponse([
    					'success' => false,
    					'message' => $this->render('EasanlesAtletismoBundle:Categoria:form_categoria.html.twig',
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
    			'message' => $this->render('EasanlesAtletismoBundle:Categoria:form_categoria.html.twig',
    					array('form' => $form->createView(), 'mode' => 'new'))->getContent()
    	]);
    	 
    }
}
