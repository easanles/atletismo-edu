<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Easanles\AtletismoBundle\Helpers\Helpers;
use Symfony\Component\HttpFoundation\Response;
use Easanles\AtletismoBundle\Entity\Inscripcion;
use Symfony\Component\HttpFoundation\JsonResponse;
use Easanles\AtletismoBundle\Form\Type\InsType;
use Easanles\AtletismoBundle\Form\Type\InsTypeGroup;
use Doctrine\Common\Collections\ArrayCollection;
use Easanles\AtletismoBundle\Entity\Participacion;

class InscripcionController extends Controller {
	
    public function listadoInscripcionesAction($sidCom, Request $request) {
    	  $repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	  $com = $repoCom->find($sidCom);
    	  if ($com == null){
    	  		$response = new Response('No existe la competicion con el identificador "'.$sidCom.'" <a href="'.$this->generateUrl('listado_competiciones').'">Volver</a>');
    	  		$response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_competiciones'));
    	  		return $response;
    	  }
    	  $entornos = $repoCom->getComEntornos($sidCom);
    	  $parametros = array('com' => $com, 'entornos' => $entornos);
    	  $atletaIds = $repoCom->findAtletasIns($sidCom);
    	  $repoAtl = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
    	  $repoIns = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Inscripcion');
    	  $repoPar = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Participacion');
    	  $repoInt = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Intento');
    	  $atletas = array();
    	  foreach ($atletaIds as $idAtl){
    	  	  $atlData = array('id' => $idAtl['idAtl']);
    	     $atl = $repoAtl->findOneBy(array('id' => $idAtl));
    	     $atlData['lfga'] = $atl->getLfga();
    	     $atlData['apellidos'] = $atl->getApellidos();
    	     $atlData['nombre'] = $atl->getNombre();
    	     $atlData['categoria'] = Helpers::getAtlCurrentCat($this->getDoctrine(), $atl)['nombre'];
    	     $pruebasIns = $repoIns->findForAtl($sidCom, $idAtl);
    	     foreach($pruebasIns as $key => $ins){
    	        $pruebasIns[$key]['numRegistros'] = $repoInt->countEntriesFor($idAtl['idAtl'], $ins['sidPru']);
    	     }
    	     $atlData['pruebas'] = $pruebasIns;
    	     $costeTotal = 0.00;
    	     $estado = 0;
    	     $unPendiente = false;
    	     foreach($pruebasIns as $ins){
    	        $costeTotal += $ins['coste'];
    	        if ($ins['estado'] == "Pagado"){
    	           if ($estado == 0){
    	              if ($unPendiente == true) $estado = 1;
    	              else $estado = 2;
    	           } 
    	        }
    	        if ($ins['estado'] == "Pendiente"){
    	           if ($estado == 2) $estado = 1;
    	           $unPendiente = true;
    	        }
    	     }
    	     $atlData['costetotal'] = $costeTotal;
    	     $atlData['estado'] = $estado;
    	     $par = $repoPar->findOneBy(array('idAtl' => $atl, 'sidCom' => $com));
    	     if ($par == null){
    	        $atlData['dorsal'] = "--";
    	        $atlData['asistencia'] = false;
    	     } else {
    	     	  $atlData['sidPar'] = $par->getSid();
    	     	  $estado = 3;
    	     	  $atlData['dorsal'] = $par->getDorsal();
    	        $atlData['asistencia'] = $par->getAsisten();
    	     }
    	     switch ($estado){
    	     	  case 0: $atlData['estado'] = "Pendiente de pago"; break;
    	     	  case 1: $atlData['estado'] = "Parcialmente pagado"; break;
    	     	  case 2: $atlData['estado'] = "Pagado"; break;
    	     	  case 3: $atlData['estado'] = "Confirmado"; break;
    	     	  default: $atlData['estado'] = "?";
    	     }

    	     $atletas[] = $atlData;
    	  }
    	  $parametros['atletas'] = $atletas;
    	   
        return $this->render('EasanlesAtletismoBundle:Inscripcion:list_inscripcion.html.twig', $parametros);
    }
    
    public function inscribirAtletasAction($sidCom){
    	 $repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	 $com = $repository->find($sidCom);
    	 if ($com == null){
    		 $response = new Response('No existe la competicion con el identificador "'.$sidCom.'" <a href="'.$this->generateUrl('listado_competiciones').'">Volver</a>');
    		 $response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_competiciones'));
    		 return $response;
    	 }
    	 
    	 return $this->render('EasanlesAtletismoBundle:Inscripcion:form_inscripcion.html.twig', array(
    	 		'mode' => 'new', 'com' => $com
    	 ));
    }
    
    public function seleccionAtletasAction($sidCom, Request $request){
    	$repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	$com = $repoCom->find($sidCom);
    	$parametros = array('com' => $com);
    	//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    	//TODO: Codigo copiado de Atleta:listadoAtletas
    	//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    	$cat = $request->query->get('cat');
    	$query = $request->query->get('q');
    	$repoAtl = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
    	$repoCat = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Categoria');
    	
    	if (($cat == null) && ($query == null)){
    		$atletas = $repoAtl->findAllOrdered(true);
    	} else {
    		$catObj = $repoCat->findOneBy(array("id" => $cat));
    		if ($catObj == null) {
    			$atletas = $repoAtl->searchByParameters(null, null, $query, true);
    		} else {
    			$fnacIni = Helpers::getCatIniDate($this->getDoctrine(), $catObj);
    			$fnacFin = Helpers::getCatFinDate($this->getDoctrine(), $catObj);
    			$atletas = $repoAtl->searchByParameters($fnacIni, $fnacFin, true);
    		}
    	}
    	$parametros['atletas'] = $atletas;    	
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
    	return $this->render('EasanlesAtletismoBundle:Inscripcion:sel_atleta.html.twig', $parametros);
    }
    
    public function seleccionPruebasAction($sidCom, Request $request){
    	 $repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	 $com = $repository->find($sidCom);
    	 if ($com == null){
    		 $response = new Response('No existe la competicion con el identificador "'.$sidCom.'" <a href="'.$this->generateUrl('listado_competiciones').'">Volver</a>');
    		 $response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_competiciones'));
    		 return $response;
    	 }
    	 $parametros = array('sidCom' => $sidCom);
    	 $selAtl = $request->request->get('selAtl');
    	 $parametros['selAtl'] = $selAtl;
    	     	 
    	 return $this->render('EasanlesAtletismoBundle:Inscripcion:sel_pruebas.html.twig', $parametros);
    }
    
    public function pruebasDisponiblesAction($sidCom, $idAtl){
    	 $repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	 $com = $repoCom->find($sidCom);
    	 if ($com == null){
    		 $response = new Response('No existe la competicion con el identificador "'.$sidCom.'" <a href="'.$this->generateUrl('listado_competiciones').'">Volver</a>');
    		 $response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_competiciones'));
    		 return $response;
    	 }    	 
    	 $repoAtl = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
    	 $atl = $repoAtl->find($idAtl);    	 
    	 if ($atl == null) {
    	 	$response = new Response('No existe el atleta con identificador "'.$idAtl.'" <a href="'.$this->generateUrl('inscribir_atletas', $sidCom).'">Volver</a>');
    	 	$response->headers->set('Refresh', '2; url='.$this->generateUrl('inscribir_atletas', $sidCom));
    	 	return $response;
    	 }
    	 $entornos = $repoCom->getComEntornos($sidCom);
       $cat = Helpers::getAtlCurrentCat($this->getDoctrine(), $atl);
    	 $repoPru = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Prueba');
    	 $listaPru = $repoPru->searchByParameters($sidCom, $cat['id']); // Misma categoria
    	 $repoCat = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Categoria');
    	 $listaPruTodos = $repoPru->searchByParameters($sidCom, $repoCat->findOneBy(array("esTodos" => true))->getId());
    	 foreach($listaPruTodos as $pru){
    	    $listaPru[] = $pru;
    	 }
    	 $listaPruObj = array();
    	 $repoIns = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Inscripcion');
    	 foreach($listaPru as $pruArr){
    	 	 $pruObj = $repoPru->find($pruArr['sid']);
    	 	 if (($pruObj->getSidTprm()->getSexo() != 2) 
    	 	 		&& ($pruObj->getSidTprm()->getSexo() != $atl->getSexo())) continue; //Mismo sexo (masculino, femenino)
    	 	 $checkIns = $repoIns->findOneBy(array("idAtl" => $idAtl, "sidPru" => $pruArr['sid']));
    	 	 if ($checkIns != null) continue; // Atleta ya inscrito a esta prueba
    	 	 
    	 	 //TODO: Otras restricciones
    	 	 $pruFeder = $pruObj->getSidCom()->getEsFeder();
    	 	 if (($pruFeder == false) ||  //Pruebas federadas solo para atletas FGA
    	 	 	(($pruFeder == true) && (($atl->getLfga() != null) && ($atl->getLfga() != "")))){
    	 	 	 	 $listaPruObj[] = $pruObj;
    	 	 }
    	  }
    	 
    	 $parametros = array("listaPru" => $listaPruObj, "entornos" => $entornos);
    	 return $this->render('EasanlesAtletismoBundle:Inscripcion:sel_pruebas_data.html.twig', $parametros);
    }
    
    public function pantallaConfirmacionAction($sidCom, Request $request){
    	 $repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	 $com = $repoCom->find($sidCom);
    	 if ($com == null){
    	 	 $response = new Response('No existe la competicion con el identificador "'.$sidCom.'" <a href="'.$this->generateUrl('listado_competiciones').'">Volver</a>');
    		 $response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_competiciones'));
    		 return $response;
    	 }
    	 $entornos = $repoCom->getComEntornos($sidCom);
    	 $data = $request->request->get("data");
    	 $inscripcionGrupal = false;
    	 $temp = null;
    	 $repoAtl = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
    	 $repoPru = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Prueba');
    	 $entradas = array();
    	 foreach($data as $entrada){
    	    $atl = $repoAtl->find($entrada[0]);
    	    if (($atl != null) && ($atl->getEsAlta() == true)){
    	    	 $pru = $repoPru->find($entrada[1]);
    	    	 if ($pru != null){
    	    	   if ($temp == null) $temp = $entrada[0];
    	    	   else if ($temp != $entrada[0]) $inscripcionGrupal = true;  	    	
    	    	 	$entradas[] = array("atl" => $atl, "pru" => $pru);
    	    	 }
    	    }
    	 }
    	 $parametros = array("entradas" => $entradas, "entornos" => $entornos);
    	 if ($inscripcionGrupal == true){
    	 	 $repoIns = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Inscripcion');
    	 	 $codGrupo = $repoIns->maxCodGrupo() + 1;
    	 	 $parametros['codGrupo'] = $codGrupo;
    	 }
    	 
    	 return $this->render('EasanlesAtletismoBundle:Inscripcion:sel_confirmar.html.twig', $parametros);
    }
    
    public function crearInscripcionesAction($sidCom, Request $request){
    	 $repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	 $com = $repoCom->find($sidCom);
    	 if ($com == null){
    		 $response = new Response('No existe la competicion con el identificador "'.$sidCom.'" <a href="'.$this->generateUrl('listado_competiciones').'">Volver</a>');
    		 $response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_competiciones'));
    		 return $response;
    	 }
    	 $data = $request->request->get("data");
    	 
    	 $inscripcionGrupal = false;
    	 $temp = null;
    	 $repoAtl = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
    	 $repoPru = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Prueba');
    	 $repoIns = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Inscripcion');
    	 $colaIns = array();
    	 foreach($data as $elem){
    	 	 $idAtl = $elem[0];
    	 	 $sidPru = $elem[1];
    	 	 $coste = $elem[2];
    	 	 $atl = $repoAtl->find($idAtl);
    	 	 if (($atl != null) && ($atl->getEsAlta() == true)){
    	 	 	$pru = $repoPru->find($sidPru);
    	 	 	if ($pru != null){
    	 	 		if ($temp == null) $temp = $idAtl;
    	 	 		else if ($temp != $idAtl) $inscripcionGrupal = true;
    	 	 		$checkIns = $repoIns->findOneBy(array("idAtl" => $idAtl, "sidPru" => $sidPru));
    	 	 		if ($checkIns == null){
    	 	 			$colaIns[] = array("atl" => $atl, "pru" => $pru, "coste" => $coste);
    	 	 		}
    	 	 	}
    	 	 }
    	 }
    	 if ($inscripcionGrupal == true){
    	 	$codGrupo = $repoIns->maxCodGrupo() + 1;
    	 } else $codGrupo = null;
    	 
    	 $em = $this->getDoctrine()->getManager();
    	 try {
    	 	 foreach ($colaIns as $elem){
    	 	    $ins = new Inscripcion();
    	 		 $ins->setIdAtl($elem['atl'])
    	 		 ->setSidPru($elem['pru'])
    	 		 ->setCoste($elem['coste'])
    	 		 ->setOrigen($this->getUser()->getNombre())
    	 		 ->setFecha(new \DateTime())
    	 		 ->setCodGrupo($codGrupo);
    	 		 if ($elem['coste'] == 0){
    	 		 	$ins->setEstado("Pagado");
    	 		 } else $ins->setEstado("Pendiente");
    	 		 $em->persist($ins);
    	 	 }
    	 	 $em->flush();
    	 } catch (\Exception $e) {
				$exception = $e->getMessage();
				return new Response($exception);
	    }
    	 return new Response("OK");
    }
    
    public function editarInscripcionAction($sidCom, Request $request){
    	$repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	$com = $repoCom->find($sidCom);
    	if ($com == null){
    		$response = new Response('No existe la competicion con el identificador "'.$sidCom.'" <a href="'.$this->generateUrl('listado_competiciones').'">Volver</a>');
    		$response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_competiciones'));
    		return $response;
    	}
    	$idAtl = $request->query->get("atl");
    	$repoAtl = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
    	$atl = $repoAtl->find($idAtl);
    	if ($atl == null) {
    		$response = new Response('No existe el atleta con identificador "'.$idAtl.'" <a href="'.$this->generateUrl('listado_inscripciones', array('sidCom' => $sidCom)).'">Volver</a>');
    		$response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_inscripciones', array('sidCom' => $sidCom)));
    		return $response;
    	}
    	$entornos = $repoCom->getComEntornos($sidCom);
    	$repoIns = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Inscripcion');
    	$listaIns = $repoIns->findForAtl($sidCom, $idAtl);
    	$inscripciones = array();
    	foreach ($listaIns as $insArr){
    		$ins = $repoIns->findOneBy(array('sid' => $insArr['sid']));
    		$inscripciones[] = $ins;
    	}
    	 
    	$form = $this->createForm(new InsTypeGroup($inscripciones));
    	$form->handleRequest($request);
    	
    	if ($form->isValid()) {
    		try {
    			$em = $this->getDoctrine()->getManager();
    			$em->flush();
    		} catch (\Exception $e) {
    			$exception = $e->getMessage();
    			return new JsonResponse([
    					'success' => false,
    					'message' => $this->render('EasanlesAtletismoBundle:TipoPrueba:edit_inscripcion.html.twig',
    							array('form' => $form->createView(),
    									'sidCom' => $sidCom,
    									'idAtl' => $idAtl,
    									'entornos' => $entornos,
    									'exception' => $exception))->getContent()
    			]);
    		}
    		return new JsonResponse([
    				'success' => true,
    				'message' => "OK"
    		]);
    	}
    	
    	return new JsonResponse([
    			'success' => false,
    			'message' => $this->render('EasanlesAtletismoBundle:Inscripcion:edit_inscripcion.html.twig',
    					array('form' => $form->createView(), 'sidCom' => $sidCom, 'idAtl' => $idAtl, 'entornos' => $entornos))->getContent()
    	]);
    }
    
    public function borrarInscripcionAction($sidCom, Request $request){
    	$repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	$com = $repoCom->find($sidCom);
    	if ($com == null){
    		$response = new Response('No existe la competicion con el identificador "'.$sidCom.'" <a href="'.$this->generateUrl('listado_competiciones').'">Volver</a>');
    		$response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_competiciones'));
    		return $response;
    	}
    	$idAtl = $request->query->get("atl");
    	$repoAtl = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
    	$atl = $repoAtl->find($idAtl);
    	if ($atl == null) {
    		$response = new Response('No existe el atleta con identificador "'.$idAtl.'" <a href="'.$this->generateUrl('listado_inscripciones', array('sidCom' => $sidCom)).'">Volver</a>');
    		$response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_inscripciones', array('sidCom' => $sidCom)));
    		return $response;
    	}
    	$entornos = $repoCom->getComEntornos($sidCom);
    	$repoIns = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Inscripcion');
    	$listaIns = $repoIns->findForAtl($sidCom, $idAtl);
    	$inscripciones = array();
    	foreach ($listaIns as $insArr){
    		$ins = $repoIns->findOneBy(array('sid' => $insArr['sid']));
    		$inscripciones[] = $ins;
    	}
    	
      $data = $request->request->get("data");
      $em = $this->getDoctrine()->getManager();
      if ($data != null){
      	try {
      		if (sizeof($listaIns) == sizeof($data)){
      			$repoPar = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Participacion');
      			$par = $repoPar->findOneBy(array("idAtl" => $idAtl, "sidCom" => $sidCom));
      			if ($par != null){
      				$em->remove($par);
      			}
      		}
      		foreach($data as $sidIns){
      			$ins = $repoIns->find($sidIns);
      			$em->remove($ins);
      		}
      		$em->flush();
      	} catch (\Exception $e) {
      		$exception = $e->getMessage();
      		return new JsonResponse([
      				'success' => false,
      				'message' => $this->render('EasanlesAtletismoBundle:Inscripcion:borr_inscripcion.html.twig',
      						array('inscripciones' => $inscripciones,
      								'sidCom' => $sidCom,
      								'idAtl' => $idAtl,
      								'entornos' => $entornos,
      								'exception' => $exception))->getContent()
      		]);
      	}
      	return new JsonResponse([
      			'success' => true,
      			'message' => "OK"
      	]);
      	
      } else {
      	return new JsonResponse([
      			'success' => false,
      			'message' => $this->render('EasanlesAtletismoBundle:Inscripcion:borr_inscripcion.html.twig',
      					array('inscripciones' => $inscripciones, 'sidCom' => $sidCom, 'idAtl' => $idAtl, 'entornos' => $entornos))->getContent()
      	]);
      }
    }
    
    public function verInscripcionGrupalAction(Request $request, $sidCom){
    	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	$com = $repository->find($sidCom);
    	if ($com == null){
    		$response = new Response('No existe la competicion con el identificador "'.$sidCom.'" <a href="'.$this->generateUrl('listado_competiciones').'">Volver</a>');
    		$response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_competiciones'));
    		return $response;
    	}
    	$codigo = $request->query->get('cod');
    	$repoIns = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Inscripcion');
    	$listaIns = $repoIns->findInsGrupal($codigo);
    	if (sizeof($listaIns) == 0){
    		$responseText = "No hay atletas inscritos con ese código de grupo";
    	} else {
    		$responseText = "<ul>";
    		foreach($listaIns as $ins){
    			$responseText .= "<li>".$ins['apellidos'].", ".$ins['nombre'];
    			if (($ins['nick'] != null) && ($ins['nick'] != "")){
    				$responseText .= " (".$ins['nick'].")";
    			}
    			$responseText .= "</li>";
    		}
    		$responseText .= "</ul>";
    	}
    	return new JsonResponse([
    			'success' => true,
    			'message' => $responseText
    	]);
    }
}
