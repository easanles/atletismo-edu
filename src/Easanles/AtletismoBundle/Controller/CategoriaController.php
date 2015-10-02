<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Easanles\AtletismoBundle\Entity\Categoria;
use Symfony\Component\HttpFoundation\JsonResponse;
use Easanles\AtletismoBundle\Form\Type\CatType;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
use Easanles\AtletismoBundle\Helpers\Helpers;

class CategoriaController extends Controller
{
    public function listadoCategoriasAction(Request $request) {
    	$outdated = $request->query->get('outd');
    	
    	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Categoria');
    	if ($outdated == "false") $categorias = $repository->findAllCurrent();
    	else if ($outdated == "true")$categorias = $repository->findAll();
    	
    	return $this->render('EasanlesAtletismoBundle:Categoria:list_categoria.html.twig',
    			array("categorias" => $categorias, "outdated" => $outdated));
    }
    
    public function crearCategoriaAction(Request $request) {
      $em = $this->getDoctrine()->getManager();
    	$cat = new Categoria();    	 
    	$cat->setTIniVal(Helpers::getTempYear($this->getDoctrine(), date('d'), date('m'), date('Y')));
    	
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
    			$nombre = $cat->getNombre();
    			$prevCat = $repository->findOneCurrent($nombre);
    			if ($prevCat != null) {
    				throw new Exception("Ya existe una categoría vigente con el nombre \"".$nombre."\"");
    			}
    			
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
    
    public function editarCategoriaAction(Request $request, $id) {
    	$em = $this->getDoctrine()->getManager();
    	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Categoria');
    	$cat = $repository->find($id);
    	 
    	if ($cat == null){
    		$response = new Response('No existe la categoria con el identificador "'.$id.'" <a href="'.$this->generateUrl('configuracion').'">Volver</a>');
    		$response->headers->set('Refresh', '2; url='.$this->generateUrl('configuracion'));
    		return $response;
    	}
    	
    	$prevNombre = $cat->getNombre();
    	$prevEdadMax = $cat->getEdadMax();
    	
    	$form = $this->createForm(new CatType(), $cat);
    
    	$form->handleRequest($request);
    	 
    	if ($form->isValid()) {
    		try {
    			$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Categoria');
    
    			if (($prevEdadMax != null) && (($cat->getEdadMax() == null) || ($cat->getEdadMax() == ""))) {
    				$nullCat = $repository->findCurrentEdadMaxNull();
    				if ($nullCat != null){
    					throw new Exception("Solo puede haber una categoría vigente sin edad máxima");
    				}
    			}
    			$nombre = $cat->getNombre();
    			if ($prevNombre != $nombre){
    				$prevCat = $repository->findOneCurrent($nombre);
    				if ($prevCat != null) {
    					throw new Exception("Ya existe una categoría vigente con el nombre \"".$nombre."\"");
    				}
    			}
    			 
    			$em = $this->getDoctrine()->getManager();
    			$em->persist($cat);
    			$em->flush();
    		} catch (\Exception $e) {
    			$exception = $e->getMessage();
    			return new JsonResponse([
    					'success' => false,
    					'message' => $this->render('EasanlesAtletismoBundle:Categoria:form_categoria.html.twig',
    							array('form' => $form->createView(), 'mode' => 'edit', 'id' => $id, 'exception' => $exception))->getContent()
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
    					array('form' => $form->createView(), 'mode' => 'edit', 'id' => $id))->getContent()
    	]);
    }
    
    public function caducarCategoriaAction(Request $request){
    	 $idCat = $request->query->get('i');
    	 
    	 $em = $this->getDoctrine()->getManager();
    	 $repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Categoria');
    	 $cat = $repository->find($idCat);
    	 
    	 if ($cat == null){
    	 	 $response = new Response('No existe la categoria con el identificador "'.$idCat.'" <a href="'.$this->generateUrl('configuracion').'">Volver</a>');
    	 	 $response->headers->set('Refresh', '2; url='.$this->generateUrl('configuracion'));
    	 	 return $response;
    	 } else {
    	 	 $cat->setTFinVal(Helpers::getTempYear($this->getDoctrine(), date('d'), date('m'), date('Y')));
    	 	 try {
    	 	 	$em->flush();
    	 	 } catch (\Exception $e) {
    	 	 	return new Response($e->getMessage());
    	 	 }
    	 	 return $this->redirect($this->generateUrl('configuracion'));
    	 }
    } 

}
