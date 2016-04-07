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
use Symfony\Component\HttpFoundation\JsonResponse;
use Easanles\AtletismoBundle\Entity\Usuario;
use Easanles\AtletismoBundle\Form\Type\UsuType;

class AtletaController extends Controller {
	
	public function listadoAtletasAction(Request $request, $alta) {
		$cat = $request->query->get('cat');
		$query = $request->query->get('q');
		$repoAtl = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
		$repoCat = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Categoria');
		
		if (($cat == null) && ($query == null)){
			$atletas = $repoAtl->findAllOrdered($alta);
		} else {
			$catObj = $repoCat->findOneBy(array("id" => $cat));
			if ($catObj == null) {
				$atletas = $repoAtl->searchByParameters(null, null, $query, $alta);
			} else {
				$fnacIni = Helpers::getCatIniDate($this->getDoctrine(), $catObj);
				$fnacFin = Helpers::getCatFinDate($this->getDoctrine(), $catObj);
				$atletas = $repoAtl->searchByParameters($fnacIni, $fnacFin, $query, $alta);
			}
		}
		$parametros = array('atletas' => $atletas, 'estadoAlta' => $alta);
		
		$vigentes = $repoCat->findAllCurrent();
		$parametros['vigentes'] = $vigentes;
		
		$categorias = array();
		$fechaRefCat = Helpers::getFechaRefCat($this->getDoctrine());
		foreach ($atletas as $atl){
			$categorias[] = Helpers::getCategoria($vigentes, $fechaRefCat, $atl['fnac']);
		}
		$parametros['categorias'] = $categorias;
		
		if ($cat != null) $parametros['cat'] = $cat;
		if ($query != null) $parametros['query'] = $query;
		return $this->render('EasanlesAtletismoBundle:Atleta:list_atleta.html.twig', $parametros);
	}
	
	public function crearAtletaAction(Request $request) {
		$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
		$listaTipos = $repository->findTipos();
		$tipos = array();
		foreach($listaTipos as $tipo){
			$tipos[] = $tipo['tipo'];
		}
		$atl = new Atleta();
		$form = $this->createForm(new AtlType(), $atl);
	
		$form->handleRequest($request);
		 
		if ($form->isValid()) {
			try {
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
				$parametros = array('mode' => "new", "tipos" => $tipos);
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
				
				//Crear y asignar usuario previamente validado
				if (($form->get('usu_nombre')->getData() != null) && ($form->get('usu_nombre')->getData() !== "")){
					$repoUsu = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Usuario');
					$checkUsu = $repoUsu->find($form->get('usu_nombre')->getData());
					if ($checkUsu == null){
						$usu = new Usuario();
						$usu->setNombre($form->get('usu_nombre')->getData());
						$encoder = $this->container->get('security.password_encoder');
						$encoded = $encoder->encodePassword($usu, $form->get('usu_contra')->getData());
						$usu->setContra($encoded);
						$usu->setRol($form->get('usu_rol')->getData());
						$usu->setIdAtl($atl);
						$em->persist($usu);
						$em->flush();
					}
				}
			} catch (\Exception $e) {
				$exception = $e->getMessage();
				return $this->render('EasanlesAtletismoBundle:Atleta:form_atleta.html.twig',
						array('form' => $form->createView(), 'mode' => "new", 'tipos' => $tipos, 'exception' => $exception));
			}
			return $this->redirect($this->generateUrl('listado_atletas'));
		}
	
		return $this->render('EasanlesAtletismoBundle:Atleta:form_atleta.html.twig',
				array('form' => $form->createView(), 'mode' => "new", 'tipos' => $tipos));
	}
	
	public function verAtletaAction($id){
		$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
		$atl = $repository->find($id);
		if ($atl != null) {
		   $categoria = Helpers::getAtlCurrentCat($this->getDoctrine(), $atl);
			return $this->render('EasanlesAtletismoBundle:Atleta:ver_atleta.html.twig',
					array('atl' => $atl, 'categoria' => $categoria, 'edad' => Helpers::getEdad($atl->getFnac(), null)));
		} else {
			$response = new Response('No existe el atleta con identificador "'.$id.'" <a href="'.$this->generateUrl('listado_atletas').'">Volver</a>');
			$response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_atletas'));
			return $response;
		}
	}
	
	public function borrarAtletaAction(Request $request){
		$id = $request->query->get('i');
	
		$em = $this->getDoctrine()->getManager();
		$repoAtl = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
		$atl = $repoAtl->find($id);
		if ($atl != null){
			try {
				$em->remove($atl);
				$cascade = $request->query->get('cascade');
				if (($cascade != null) && ($cascade == "true")){
					if ($atl->getNombreUsu() != null){
						$repoUsu = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Usuario');
						if (($atl->getNombreUsu()->getRol() !== "coordinador") //Al menos un coordinador
								 || (($atl->getNombreUsu()->getRol() === "coordinador") && ($repoUsu->countCoordinadores() > 1))){					
							$em->remove($atl->getNombreUsu());
						}
					}
				}				
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
	   $listaTipos = $repository->findTipos();
		$tipos = array();
		foreach($listaTipos as $tipos){
			$tipos[] = $tipos['tipo'];
		}
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
			if ($atl->getNombreUsu() != null){
				$form->get("usu_nombre")->setData($atl->getNombreUsu()->getNombre());
				$form->get("usu_rol")->setData($atl->getNombreUsu()->getRol());
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
				$parametros = array('mode' => "edit", "editando" => $editando, "tipos" => $tipos);
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
				array('form' => $form->createView(), 'mode' => "edit", "editando" => $editando, "tipos" => $tipos));
	}
	
	public function buscarIdAction(Request $request){
		$repoAtl = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
		$idAtl = $request->query->get('id');
		if (($idAtl == null) || ($idAtl == "")){
			return new JsonResponse([
					'success' => false,
					'atl' => "No se ha recibido el parámetro necesario"
			]);
		}
		$atl = $repoAtl->find($idAtl);
		if ($atl != null){
	      return new JsonResponse([
    			'success' => true,
    			'atl' => $atl->getApellidos().", ".$atl->getNombre()
    	   ]);
		} else {
			return new JsonResponse([
					'success' => false,
					'atl' => "No encontrado"
			]);
		}
	}
	
	public function asignarUsuarioAction(Request $request){
	   $usu = new Usuario();
   	$usu->setRol("socio");
   	$form = $this->createForm(new UsuType("new", true), $usu);
   	
   	$form->handleRequest($request);
   	 
   	if ($form->isValid()) {
   		try {
   			$repoUsu = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Usuario');
   			$checkUsu = $repoUsu->find($usu->getNombre());
   			if ($checkUsu != null){
   				throw new Exception("Ya existe el usuario con el nombre \"".$usu->getNombre()."\"");
   			}
   		} catch (\Exception $e) {
   			$exception = $e->getMessage();
         	return new JsonResponse([
   		   	'success' => false,
   		   	'message' => $this->render('EasanlesAtletismoBundle:Atleta:form_usuario.html.twig',
   		   	      array('form' => $form->createView(), 'mode' => 'new', 'exception' => $exception))->getContent()
   	      ]);
   		}
   		return new JsonResponse([
   		   	'success' => true,
   		   	'message' => array(
   		   			"nombre" => $usu->getNombre(),
   		   			"contra" => $usu->getContra(),
   		   			"rol" => $usu->getRol()
   		   	)
   	   ]);
   	}
      return new JsonResponse([
   	   	'success' => false,
   	   	'message' => $this->render('EasanlesAtletismoBundle:Atleta:form_usuario.html.twig',
   	            array('form' => $form->createView(), 'mode' => 'new'))->getContent()
      ]);
   }
   
   public function comprobarUsuarioAction($id){
   	$repoAtl = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
   	$atl = $repoAtl->find($id);
   	$arrayUsu = null;
   	if ($atl != null){
   		if ($atl->getNombreUsu() != null){
   			$arrayUsu = array("nombre" => $atl->getNombreUsu()->getNombre(),
   					            "rol" => $atl->getNombreUsu()->getRol()
   			);
   		}
   		return new JsonResponse([
   				'success' => true,
   				'usu' => $arrayUsu
   		]);
   	} else
      return new JsonResponse([
   	   	'success' => false
      ]);
   }
   
   private function makeHistoricArray($listaIns, $idAtl){
   	$repoInt = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Intento');
   	$arrayEntornos = array();
   	$orden = 0;
   	$currentEntorno = null;
   	$arrayComs = array();
   	$currentSidCom = null;
   	$arrayPrus = array();
   	foreach($listaIns as  $key => $ins){
   		if (($currentEntorno == null) || ($ins['entorno'] != $currentEntorno)){
   			$currentEntorno = $ins['entorno'];
   			if (count($arrayPrus) > 0){
   				$arrayComs[] = array(
   						"sid" => $listaIns[$key-1]['sidCom'],
   						"nombre" => $listaIns[$key-1]['nombreCom'],
   						"temp" => $listaIns[$key-1]['temp'],
   						"fecha" => $listaIns[$key-1]['fecha'],
   						"sede" => $listaIns[$key-1]['sede'],
   						"prus" => $arrayPrus
   				);
   				$arrayPrus = array();
   			}
   			if (count($arrayComs) > 0){
   				$orden = $orden + 1;
   				$arrayEntornos[] = array(
   						"orden" => $orden,
   						"entorno" => $listaIns[$key-1]['entorno'],
   						"coms" => $arrayComs
   				);
   				$arrayComs = array();
   			}
   		}
   		if (($currentSidCom == null) || ($ins['sidCom'] != $currentSidCom)){
   			$currentSidCom = $ins['sidCom'];
   			if (count($arrayPrus) > 0){
   				$arrayComs[] = array(
   						"sid" => $listaIns[$key-1]['sidCom'],
   						"nombre" => $listaIns[$key-1]['nombreCom'],
   						"temp" => $listaIns[$key-1]['temp'],
   						"fecha" => $listaIns[$key-1]['fecha'],
   						"sede" => $listaIns[$key-1]['sede'],
   						"prus" => $arrayPrus
   				);
   				$arrayPrus = array();
   			}
   		}
   		$lastMarca = $repoInt->findLastMarcaFor($idAtl, $ins['sidPru']);
   		if (count($lastMarca) > 0){
   			$marca = $lastMarca[0]['marca'];
   			$premios = $lastMarca[0]['premios'];
   		} else {
   			$marca = "";
   			$premios = "";
   		}
   		$arrayPrus[] = array(
   				"sid" => $ins['sidPru'],
   				"nombre" => $ins['nombreTprf'],
   				"categoria" => $ins['nombreCat'],
   				"marca" => $marca,
   				"premios" => $premios,
   				"unidades" => $ins['unidades']
   		);
   	}
   	if (count($arrayPrus) > 0){
   		$arrayComs[] = array(
   				"sid" => $listaIns[count($listaIns)-1]['sidCom'],
   		   	"nombre" => $listaIns[count($listaIns)-1]['nombreCom'],
   				"temp" => $listaIns[count($listaIns)-1]['temp'],
   				"fecha" => $listaIns[count($listaIns)-1]['fecha'],
   				"sede" => $listaIns[count($listaIns)-1]['sede'],
					"prus" => $arrayPrus
   		);
   	}
   	if (count($arrayComs) > 0){
   		$orden = $orden + 1;
   	   $arrayEntornos[] = array(
   	   	"orden" => $orden,
   			"entorno" => $listaIns[count($listaIns)-1]['entorno'],
   			"coms" => $arrayComs
   		);
   	}
   	return $arrayEntornos;
   }
   
   public function historialAtletaAction(Request $request, $id){
   	$repoAtl = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
   	$atl = $repoAtl->find($id);
   	if ($atl == null) {
   		$response = new Response('No existe el atleta con identificador "'.$id.'" <a href="'.$this->generateUrl('listado_atletas').'">Volver</a>');
   		$response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_atletas'));
   		return $response;
   	}
   	$selTemp = $request->query->get('t');
   	$cat = Helpers::getAtlCurrentCat($this->getDoctrine(), $atl);
   	$repoIns = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Inscripcion');
   	$listaTemps = $repoIns->findAtlTemps($id);
   	$parametros = array("atl" => $atl, "cat" => $cat, "selTemp" => $selTemp, "temps" => $listaTemps);
   	$listaIns = $repoIns->findAtlHistoric($id, $selTemp);
      $parametros['entornos'] = $this->makeHistoricArray($listaIns, $id);
   	return $this->render('EasanlesAtletismoBundle:Atleta:hist_atleta.html.twig', $parametros);
   }
   
   public function cambiarEstadoAtletaAction(Request $request){
   	$idAtl = $request->query->get('id');
   	$operacion = $request->query->get('op');
   	if (($idAtl == null) || ($idAtl == "") || ($operacion == null) || (($operacion != "alta") && ($operacion != "baja"))){
   		return new JsonResponse([
   				'success' => false,
   				'message' => "No se ha recibido el parámetro necesario"
   		]);
   	}
   	$repoAtl = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
   	$atl = $repoAtl->find($idAtl);
   	if ($atl == null){
   		return new JsonResponse([
   				'success' => false,
   				'message' => "No hay ningún atleta con el identificador ".$idAtl
   		]);
   	}
   	if ($operacion == "baja") $nuevoEstado = false;
   	else if ($operacion == "alta") $nuevoEstado = true;
   	$atl->setEsAlta($nuevoEstado);
   	$em = $this->getDoctrine()->getManager();
   	$em->flush();
   	return new JsonResponse([
   			'success' => true,
   			'message' => "OK"
   	]);
   }
   
}
