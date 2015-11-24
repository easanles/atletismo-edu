<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Easanles\AtletismoBundle\Helpers\Helpers;
use Symfony\Component\HttpFoundation\Response;
use Easanles\AtletismoBundle\Entity\Inscripcion;

class InscripcionController extends Controller {
	
    public function listadoInscripcionesAction($sidCom, Request $request) {
    	  $repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	  $com = $repository->find($sidCom);
    	  if ($com == null){
    	  		$response = new Response('No existe la competicion con el identificador "'.$sidCom.'" <a href="'.$this->generateUrl('listado_competiciones').'">Volver</a>');
    	  		$response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_competiciones'));
    	  		return $response;
    	  }
    	  $parametros = array('com' => $com);
    	  $atletaIds = $repository->findAtletasIns($sidCom);
    	  $repoAtl = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
    	  $repoCat = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Categoria');
    	  $vigentes = $repoCat->findAllCurrent();
    	  $fechaRefCat = Helpers::getFechaRefCat($this->getDoctrine());
    	  $repoIns = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Inscripcion');
    	  $repoPar = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Participacion');
    	  $atletas = array();
    	  foreach ($atletaIds as $idAtl){
    	  	  $atlData = array('id' => $idAtl[1]);
    	     $atl = $repoAtl->findOneBy(array('id' => $idAtl));
    	     $atlData['lfga'] = $atl->getLfga();
    	     $atlData['apellidos'] = $atl->getApellidos();
    	     $atlData['nombre'] = $atl->getNombre();
    	     $atlData['categoria'] = Helpers::getCategoria($vigentes, $fechaRefCat, $atl->getFnac())['nombre'];
    	     $pruebasIns = $repoIns->findForAtl($sidCom, $idAtl);
    	     $atlData['pruebas'] = $pruebasIns;
    	     $costeTotal = 0.00;
    	     $estado = 0;
    	     foreach($pruebasIns as $ins){
    	        $costeTotal += $ins['coste'];
    	        if ($ins['estado'] == "Pagado"){
    	           if ($estado == 0) $estado = 2;
    	        }
    	        if ($ins['estado'] == "Pendiente"){
    	           if ($estado == 2) $estado = 1;
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
    	$parametros = array('sidCom' => $sidCom);
    	//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    	//TODO: Codigo copiado de Atleta:listadoAtletas
    	//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    	$cat = $request->query->get('cat');
    	$query = $request->query->get('q');
    	$repoAtl = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
    	$repoCat = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Categoria');
    	
    	if (($cat == null) && ($query == null)){
    		$atletas = $repoAtl->findAllOrdered();
    	} else {
    		$catObj = $repoCat->findOneBy(array("id" => $cat));
    		if ($catObj == null) {
    			$atletas = $repoAtl->searchByParameters(null, null, $query);
    		} else {
    			$fnacIni = Helpers::getCatIniDate($this->getDoctrine(), $catObj);
    			$fnacFin = Helpers::getCatFinDate($this->getDoctrine(), $catObj);
    			$atletas = $repoAtl->searchByParameters($fnacIni, $fnacFin, $query);
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
    	 $repoCat = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Categoria');
    	 $vigentes = $repoCat->findAllCurrent();
    	 $fechaRefCat = Helpers::getFechaRefCat($this->getDoctrine());
       $cat = Helpers::getCategoria($vigentes, $fechaRefCat, $atl->getFnac());
    	 $repoPru = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Prueba');
    	 $listaPru = $repoPru->searchByParameters($sidCom, $cat['id']); // Misma categoria
    	 $listaPruObj = array();
    	 $repoIns = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Inscripcion');
    	 foreach($listaPru as $pruArr){
    	 	 $pruObj = $repoPru->find($pruArr['sid']);
    	 	 if ($pruObj->getSidTprm()->getSexo() != $atl->getSexo()) continue; //Mismo sexo (masculino, femenino)
    	 	 $checkIns = $repoIns->findOneBy(array("idAtl" => $idAtl, "sidPru" => $pruArr['sid']));
    	 	 if ($checkIns != null) continue; // Atleta ya inscrito a esta prueba
    	 	 
    	 	 //TODO: Otras restricciones
    	 	 $pruFeder = $pruObj->getSidCom()->getEsFeder();
    	 	 if (($pruFeder == false) ||  //Pruebas federadas solo para atletas FGA
    	 	 	(($pruFeder == true) && (($atl->getLfga() != null) && ($atl->getLfga() != "")))){
    	 	 	 	 $listaPruObj[] = $pruObj;
    	 	 }
    	  }
    	 
    	 $parametros = array("listaPru" => $listaPruObj);
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
    	 $data = $request->request->get("data");
    	 
    	 $inscripcionGrupal = false;
    	 $temp = null;
    	 $repoAtl = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
    	 $repoPru = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Prueba');
    	 $entradas = array();
    	 foreach($data as $entrada){
    	    $atl = $repoAtl->find($entrada[0]);
    	    if ($atl != null){
    	    	 $pru = $repoPru->find($entrada[1]);
    	    	 if ($pru != null){
    	    	   if ($temp == null) $temp = $entrada[0];
    	    	   else if ($temp != $entrada[0]) $inscripcionGrupal = true;  	    	
    	    	 	$entradas[] = array("atl" => $atl, "pru" => $pru);
    	    	 }
    	    }
    	 }
    	 $parametros = array("entradas" => $entradas);
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
    	 	 if ($atl != null){
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
    	 		 ->setOrigen("admin") //TODO: nombre de usuario
    	 		 ->setFecha(new \DateTime())
    	 		 ->setEstado("Pendiente")
    	 		 ->setCodGrupo($codGrupo);
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
    	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Atleta');
    	$atl = $repository->find($idAtl);
    	if ($atl == null) {
    		$response = new Response('No existe el atleta con identificador "'.$idAtl.'" <a href="'.$this->generateUrl('listado_inscripciones', array('sidCom' => $sidCom)).'">Volver</a>');
    		$response->headers->set('Refresh', '2; url='.$this->generateUrl('listado_inscripciones', array('sidCom' => $sidCom)));
    		return $response;
    	}
    	$repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Inscripcion');
    	// GET INSCRIPCIONES
    	// foreach inscripcion { new form}
    	// ···
    	return new Response("TEST");
    }
}
