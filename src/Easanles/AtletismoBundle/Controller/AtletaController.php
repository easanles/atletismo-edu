<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Easanles\AtletismoBundle\Helpers\Helpers;
use Easanles\AtletismoBundle\Entity\Atleta;
use Easanles\AtletismoBundle\Form\Type\AtlType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Config\Definition\Exception\Exception;

class AtletaController extends Controller {
	
	public function listadoAtletasAction(Request $request) {
		$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
		$atletas = $repository->findAllOrdered();
		$parametros = array('atletas' => $atletas);
		
		$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Categoria');
		$current = $repository->findAllCurrent();
		
		$categorias = array();
		foreach ($atletas as $atl){
			$categorias[] = Helpers::getCategoria($current, Helpers::getEdad($atl['fnac']));
		}
		$parametros['categorias'] = $categorias;
		
		
		return $this->render('EasanlesAtletismoBundle:Atleta:list_atleta.html.twig', $parametros);
	}
	
	public function crearAtletaAction(Request $request) {
		$atl = new Atleta();
		$form = $this->createForm(new AtlType(), $atl);
	
		$form->handleRequest($request);
		 
		if ($form->isValid()) {
			try {
				$dni = $atl->getDni();
				if ($dni != null){
					$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
					$query = $repository->findOneBy(array('dni' => $dni));
					if ($query != null){
						$warn_dni = $atl->getWarnDni();
						if (($warn_dni == null) || (strcmp($warn_dni, $dni) != 0)){
							$atl->setWarnDni($dni);
							$form2 = $this->createForm(new AtlType(), $atl);
							return $this->render('EasanlesAtletismoBundle:Atleta:form_atleta.html.twig',
							      array('form' => $form2->createView(), 'mode' => "new",
							      		'warning' => "DNI repetido. Confirme la operación volviendo a enviar el formulario"));
						}
					}
				}
								
				$em = $this->getDoctrine()->getManager();
				$em->persist($atl);
	
				$em->flush();
			} catch (\Exception $e) {
				$exception = $e->getMessage();
				return $this->render('EasanlesAtletismoBundle:Atleta:form_atleta.html.twig',
						array('form' => $form->createView(), 'mode' => "new", 'exception' => $exception));
			}
			return $this->redirect($this->generateUrl('listado_atletas'));
		}
	
		return $this->render('EasanlesAtletismoBundle:Atleta:form_atleta.html.twig',
				array('form' => $form->createView(), 'mode' => "new"));
	}
	
	public function verAtletaAction($id){
		$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
		$atl = $repository->find($id);
		$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Categoria');
		$categoria = $repository->findForEdad(Helpers::getEdad($atl->getFnac()));
	
		if ($atl != null) {
			return $this->render('EasanlesAtletismoBundle:Atleta:ver_atleta.html.twig',
					array('atl' => $atl, 'categoria' => $categoria));
		} else {
			$response = new Response('No existe el atleta con identificador "'.$id.'" <a href="'.$this->generateUrl('listado_atletas').'">Volver</a>');
			$response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_atletas'));
			return $response;
		}
	}
	
	public function borrarAtletaAction(Request $request){
		$id = $request->query->get('i');
	
		$em = $this->getDoctrine()->getManager();
		$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
		$atl = $repository->find($id);
		if ($atl != null){
			try {
				$em->remove($atl);
				$em->flush();
			} catch (\Exception $e) {
				return new Response($e->getMessage());
			}
			return $this->redirect($this->generateUrl('listado_atletas'));
		} else {
			$response = new Response('No existe el atleta con identificador "'.$id.'" <a href="'.$this->generateUrl('listado_atletas').'">Volver</a>');
			$response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_atletas'));
			return $response;
		}
	}
}
