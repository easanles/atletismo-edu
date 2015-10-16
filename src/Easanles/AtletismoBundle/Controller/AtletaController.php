<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Easanles\AtletismoBundle\Helpers\Helpers;
use Easanles\AtletismoBundle\Entity\Atleta;
use Easanles\AtletismoBundle\Form\Type\AtlType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Config\Definition\Exception\Exception;
use Easanles\AtletismoBundle\Entity\Categoria;

class AtletaController extends Controller {
	
	public function listadoAtletasAction(Request $request) {
		$cat = $request->query->get('cat');
		$query = $request->query->get('q');
		$repoAtl = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
		$repoCat = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Categoria');
		
		$catObj = new Categoria(); //debug
		if (($cat == null) && ($query == null)){
			$atletas = $repoAtl->findAllOrdered();
		} else {
			$catObj = $repoCat->findOneBy(array("id" => $cat));
			if ($catObj == null) {
				$atletas = $repoAtl->searchByParameters(null, null, $query);
			} else {
				$fnacIni = Helpers::getCatIniDate($repoCat, $catObj);
				$fnacFin = Helpers::getCatFinDate($repoCat, $catObj);
				$atletas = $repoAtl->searchByParameters($fnacIni, $fnacFin, $query);
				//$atletas = $repoAtl->searchByParameters(null, null, $query);
			}
		}
		$parametros = array('atletas' => $atletas);
		//$parametros['dumpv'][0] = Helpers::getCatIniDate($repoCat, $catObj)->format("Y-m-d");
		$parametros['dumpv'][1] = Helpers::getCatFinDate($repoCat, $catObj)->format("Y-m-d");
		
		$vigentes = $repoCat->findAllCurrent();
		$parametros['vigentes'] = $vigentes;
		
		$categorias = array();
		foreach ($atletas as $atl){
			$categorias[] = Helpers::getCategoria($vigentes, Helpers::getEdad($atl['fnac']));
		}
		$parametros['categorias'] = $categorias;
		if ($cat != null) $parametros['cat'] = $cat;
		if ($query != null) $parametros['query'] = $query;
		return $this->render('EasanlesAtletismoBundle:Atleta:list_atleta.html.twig', $parametros);
	}
	
	public function crearAtletaAction(Request $request) {
		$atl = new Atleta();
		$form = $this->createForm(new AtlType(), $atl);
	
		$form->handleRequest($request);
		 
		if ($form->isValid()) {
			try {
				$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
					
				$query = $repository->findOneBy(array(
						'apellidos' => $atl->getApellidos(),
				      'nombre' => $atl->getNombre()
				));
				if ($query != null){
					throw new Exception("Ya existe un atleta llamado ".$atl->getApellidos().", ".$atl->getNombre());
				}
				if ($atl->getLfga() != null){
					$query = $repository->findOneBy(array('lfga' => $atl->getLfga()));
					if ($query != null){
						throw new Exception("Ya existe un atleta con este código de licencia FGA");
					}
				}
				if ($atl->getLxogade() != null){
					$query = $repository->findOneBy(array('lxogade' => $atl->getLxogade()));
					if ($query != null){
						throw new Exception("Ya existe un atleta con este código de licencia XOGADE");
					}
				}
				
				$mensaje = "";
				$parametros = array('mode' => "new");
				$doWarn = false;
				$dni = $atl->getDni();
				if ($dni != null){
					$query = $repository->findOneBy(array('dni' => $dni));
					if ($query != null){
						$warn_dni = $atl->getWarnDni();
						if (($warn_dni == null) || (strcmp($warn_dni, $dni) != 0)){
							$atl->setWarnDni($dni);
							$doWarn = true;
							$parametros['warnAtDni'] = true;
							$mensaje = $mensaje."DNI repetido. ";
						}
					}
				} else $atl->setWarnDni($dni);
				$nick = $atl->getNick();
				if ($nick != null){
					$query = $repository->findOneBy(array('nick' => $nick));
					if ($query != null){
						$warn_nick = $atl->getWarnNick();
						if (($warn_nick == null) || (strcmp($warn_nick, $nick) != 0)){
							$doWarn = true;
							$atl->setWarnNick($nick);
							$parametros['warnAtNick'] = true;
							$mensaje = $mensaje."Nick repetido. ";
						}
					}
				} else $atl->setWarnNick($nick);
				if ($doWarn){
					$form2 = $this->createForm(new AtlType(), $atl);
					$parametros['form'] = $form2->createView();
					$parametros['warning'] = $mensaje."Confirme la operación volviendo a enviar el formulario";
					return $this->render('EasanlesAtletismoBundle:Atleta:form_atleta.html.twig', $parametros);
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
	
	public function editarAtletaAction(Request $request, $id){
		$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
		$atl = $repository->find($id);
	
		if ($atl != null) {
			$prevApellidos = $atl->getApellidos();
			$prevNombre = $atl->getNombre();
			$prevLfga = $atl->getLfga();
			$prevLxogade = $atl->getLxogade();
			$prevDni = $atl->getDni();
			$prevNick = $atl->getNick();
			if ($atl->getNick() != null){
				$editando = $atl->getNick();
			} else $editando = $atl->getApellidos().", ".$atl->getNombre();
			$form = $this->createForm(new AtlType(), $atl);
			if ($atl->getSexo() == false){
				$form->get("sexo")->setData("0");
			}
			 
			$form->handleRequest($request);
			 
			if ($form->isValid()) {
				try {
					$apellidos = $atl->getApellidos();
					$nombre = $atl->getNombre();
					$query = $repository->findOneBy(array(
							'apellidos' => $atl->getApellidos(),
							'nombre' => $atl->getNombre()
					));
					if (($query != null) && !(($prevApellidos == $apellidos) && ($prevNombre == $nombre))){
						throw new Exception("Ya existe un atleta llamado ".$atl->getApellidos().", ".$atl->getNombre());
		         }
		         
		         $lfga = $atl->getLfga();
		         if ($atl->getLfga() != null){
		         	$query = $repository->findOneBy(array('lfga' => $atl->getLfga()));
		         	if (($query != null) && !($prevLfga == $lfga)){
		         		throw new Exception("Ya existe un atleta con este código de licencia FGA");
		         	}
		         }
		         $lxogade = $atl->getLxogade();
		         if ($atl->getLxogade() != null){
		         	$query = $repository->findOneBy(array('lxogade' => $atl->getLxogade()));
		         	if (($query != null) && !($prevLxogade == $lxogade)){
		         	   throw new Exception("Ya existe un atleta con este código de licencia XOGADE");
		         	}
		         }
		         
				$mensaje = "";
				$parametros = array('mode' => "edit", "editando" => $editando);
				$doWarn = false;
				$dni = $atl->getDni();
				if (($dni != null) && !($prevDni == $dni)){
					$query = $repository->findOneBy(array('dni' => $dni));
					if ($query != null){
						$warn_dni = $atl->getWarnDni();
						if (($warn_dni == null) || (strcmp($warn_dni, $dni) != 0)){
							$atl->setWarnDni($dni);
							$doWarn = true;
							$parametros['warnAtDni'] = true;
							$mensaje = $mensaje."DNI repetido. ";
						}
					}
				} else $atl->setWarnDni($dni);
				$nick = $atl->getNick();
				if (($nick != null) && !($prevNick == $nick)){
					$query = $repository->findOneBy(array('nick' => $nick));
					if ($query != null){
						$warn_nick = $atl->getWarnNick();
						if (($warn_nick == null) || (strcmp($warn_nick, $nick) != 0)){
							$doWarn = true;
							$atl->setWarnNick($nick);
							$parametros['warnAtNick'] = true;
							$mensaje = $mensaje."Nick repetido. ";
						}
					}
				} else $atl->setWarnNick($nick);
				if ($doWarn){
					$form2 = $this->createForm(new AtlType(), $atl);
					$parametros['form'] = $form2->createView();
					$parametros['warning'] = $mensaje."Confirme la operación volviendo a enviar el formulario";
					return $this->render('EasanlesAtletismoBundle:Atleta:form_atleta.html.twig', $parametros);
				}
		         
		         $em = $this->getDoctrine()->getManager();
			      $em->persist($atl);
	
				   $em->flush();
			   } catch (\Exception $e) {
				   $exception = $e->getMessage();
				   return $this->render('EasanlesAtletismoBundle:Atleta:form_atleta.html.twig',
						   array('form' => $form->createView(), 'mode' => "edit", "editando" => $editando, 'exception' => $exception));
			   }
			   return $this->redirect($this->generateUrl('listado_atletas'));
		   }
		}
		return $this->render('EasanlesAtletismoBundle:Atleta:form_atleta.html.twig',
				array('form' => $form->createView(), 'mode' => "edit", "editando" => $editando));
	}
}
