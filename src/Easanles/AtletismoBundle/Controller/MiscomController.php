<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Easanles\AtletismoBundle\Helpers\Helpers;
use Symfony\Component\HttpFoundation\JsonResponse;
use Easanles\AtletismoBundle\Entity\Inscripcion;
use Easanles\AtletismoBundle\Entity\TipoPruebaModalidad;

class MiscomController extends Controller{
    public function portadaAction(Request $request){
    	 $repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	 $temp = $request->query->get('temp');    	 
    	 $temp = (int)$temp;    	 
    	 if (($temp == null) || ($temp == "") || (!is_int($temp))){
    	    $temp = Helpers::getCurrentTemp($this->getDoctrine());
    	 }    	 
    	 $user = $this->getUser();
    	 if ($user == null){
    	    return $this->redirect($this->generateUrl("login"));
    	 }
    	 if ($user->getIdAtl() == null){
    	 	$response = new Response('El usuario no tiene un atleta asociado <a href="'.$this->generateUrl('homepage').'">Volver</a>');
    	 	$response->headers->set('Refresh', '2; url='.$this->generateUrl('homepage'));
    	 	return $response;
    	 }
    	 $temps = $repoCom->findTemps("user");
    	 
    	 $tempComs = $repoCom->findTempComs($temp, "user");
    	 $listaComInscritos = $repoCom->findAtlComs($user->getIdAtl()->getId(), $temp);
    	 $listaComs = array();
    	 $ayer = (new \DateTime())->sub(new \DateInterval("P1D"));
    	 $hoy = new \DateTime();
    	 $repoIns = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Inscripcion');
    	 foreach ($tempComs as $com){
    	 	 $com['eshoy'] = (($com['fecha'] > $ayer) && ($com['fecha'] < $hoy));
    	    if (in_array($com['sid'], $listaComInscritos)){
    	    	$com['inscrito'] = true;
    	    } else {
    	    	$com['inscrito'] = false;
    	    }
    	    $com['pagoPendiente'] = false;
    	    if (($com['inscrito'] == true) && ($com['esOficial'] == true)){
    	       $inss = $repoIns->findForAtl($com['sid'], $user->getIdAtl()->getId());
    	       foreach ($inss as $ins){
    	          if ($ins['estado'] != "Pagado"){
    	          	 $com['pagoPendiente'] = true;
    	          	 break;
    	          }
    	       }
    	    }
    	    if ((($user->getIdAtl()->getEsAlta() == true) && ($com['fecha'] >= $ayer)) || ($com['inscrito'] == true)) {
    	    	if ($com['numpruebas'] == 1){
    	    		$comObj = $repoCom->find($com['sid']);
    	    		$pru = $comObj->getPruebas()->first();
    	    		if (($pru->getSidTprm()->getSexo() != 2)
    	    				&& ($pru->getSidTprm()->getSexo() != $user->getIdAtl()->getSexo())) continue;
    	    		$cat = Helpers::getAtlCurrentCat($this->getDoctrine(), $user->getIdAtl());
    	    		if ($pru->getIdCat()->getId() != $cat['id']) continue;
    	    	}
    	    	if (($com['esFeder'] == true)
    	    		   && (($user->getIdAtl()->getLfga() == null) || ($user->getIdAtl()->getLfga() == ""))) continue;
    	    	$listaComs[] = $com;
    	    }
    	 }
    	 $parametros = array("temp" => $temp, "temporadas" => $temps, "coms" => $listaComs, "ayer" => $ayer, "hoy" => $hoy);

       return $this->render('EasanlesAtletismoBundle:Miscom:portada_miscom.html.twig', $parametros);
    }
    
   public function operacionesInscripcionAction(Request $request, $comando){
   	$sidCom = $request->query->get('com');
   	if (($sidCom != null) && ($sidCom != "")){
   		$pruebaUnica = true;
   	} else {
   		$sidPru = $request->query->get('pru');
   		if (($sidPru != null) && ($sidPru != "")){
   			$pruebaUnica = false;
   		} else {
   			return new JsonResponse([
   					'success' => false,
   					'message' => "No se han recibido parámetros"
   			]);
   		}
   	}
   	$repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
   	$repoPru = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Prueba');
   	if ($pruebaUnica){
   		$com = $repoCom->find($sidCom);
   		if ($com == null){
   			return new JsonResponse([
   					'success' => false,
   					'message' => "No existe esa competición"
   			]);
   		}
   	} else {
   		$pru = $repoPru->find($sidPru);
   		if ($pru == null){
   			return new JsonResponse([
   					'success' => false,
   					'message' => "No existe esa prueba"
   			]);
   		}
   		$com = $pru->getSidCom();
   	}
   	$atl = $this->getUser()->getIdAtl();
   	if ($atl == null){
   		return new JsonResponse([
   				'success' => false,
   				'message' => "No tienes un atleta asociado a tu cuenta"
   		]);
   	}
   	if ($atl->getEsAlta() == false){
   		return new JsonResponse([
   				'success' => false,
   				'message' => "No estás dado de alta en el club"
   		]);
   	}
   	if ($com->getEsVisible() == false){
   		return new JsonResponse([
   				'success' => false,
   				'message' => "Esta competición está oculta"
   		]);
   	}
   	if ($com->getEsOficial() == true){
   		return new JsonResponse([
   				'success' => false,
   				'message' => "Esta es una competición oficial del club. Consulta al coordinador del club"
   		]);
   	}
   	$hoy = (new \DateTime())->sub(new \DateInterval("P1D"));
   	if ($com->getFecha() < $hoy){
   		return new JsonResponse([
   				'success' => false,
   				'message' => "Esta competición ya ha terminado"
   		]);
   	}
   	if ($com->getEsInscrib() == false){
   		return new JsonResponse([
   				'success' => false,
   				'message' => "Se han cerrado las inscripciones para esta competición"
   		]);
   	}
   	if ($pruebaUnica){
   		$numpruebas = count($com->getPruebas());
   		if ($numpruebas > 1){
   			return new JsonResponse([
   					'success' => false,
   					'message' => "Esta competición tiene más de una prueba. Recarga la página"
   			]);
   		}
   		$pru = $com->getPruebas()->first();
   		if ($pru == null){
   			return new JsonResponse([
   					'success' => false,
   					'message' => "Esta competición no tiene ninguna prueba"
   			]);
   		}
   	}
   	$repoIns = $repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Inscripcion');
   	$ins = $repoIns->findOneBy(array("idAtl" => $atl->getId(), "sidPru" => $pru->getSid()));
   	switch ($comando){
   		case ("inscrib"): {
   			if ($ins != null){
   				if ($pruebaUnica) $message = "Ya estabas inscrito a esta competición";
   				else $message = "Ya estabas inscrito a esta prueba";
   				return new JsonResponse([
   						'success' => false,
   						'message' => $message
   				]);
   			}
   			$cat = Helpers::getAtlCurrentCat($this->getDoctrine(), $atl);
   			if ($pru->getIdCat()->getId() != $cat['id']){
   				if ($pruebaUnica) $message = "Esta competición solo tiene una prueba para atletas de categoría ".$cat['nombre'];
   				else $message = "Esta prueba es para atletas de categoría ".$cat['nombre'];
   				return new JsonResponse([
   						'success' => false,
   						'message' => $message
   				]);
   			}

   			if (($pru->getSidTprm()->getSexo() != 2)
   					&& ($pru->getSidTprm()->getSexo() != $atl->getSexo())){
   				if ($pruebaUnica) $message = "Esta competición solo tiene una prueba de modalidad ".($pru->getSidTprm()->getSexo() == 1? "femenina" : "masculina");
   				else $message = "Esta prueba es de modalidad ".($pru->getSidTprm()->getSexo() == 1? "femenina" : "masculina");
   				return new JsonResponse([
   						'success' => false,
   						'message' => $message
   				]);
   			}
   		} break;
   		case ("desinscrib"): {
   			if ($ins == null){
   				if ($pruebaUnica) $message = "No estabas inscrito a esta competición";
   				else $message = "No estabas inscrito a esta prueba";
   				return new JsonResponse([
   						'success' => false,
   						'message' => $message
   				]);
   			}
   		} break;
   	}
   	try {
   		$em = $this->getDoctrine()->getManager();
   		switch ($comando){
   			case ("inscrib"): {
   				$ins = new Inscripcion();
   				$ins->setIdAtl($atl)
   				->setSidPru($pru)
   				->setCoste(0)
   				->setOrigen($this->getUser()->getNombre())
   				->setFecha(new \DateTime())
   				->setEstado("Pagado")
   				->setCodGrupo(null);
   				$em->persist($ins);
   			} break;
   			case ("desinscrib"): {
   				$em->remove($ins);
   			} break;
   		}
   		$em->flush();
   		return new JsonResponse([
   				'success' => true,
   				'message' => "OK"
   		]);
   	} catch (\Exception $e) {
   		$exception = $e->getMessage();
   		return new JsonResponse([
   				'success' => false,
   				'message' => $exception
   		]);
   	}
   }
   
   public function pantallaInscripcionAction($sidCom){
   	$repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
   	$com = $repoCom->find($sidCom);   	
   	if ($com == null) {
   		$response = new Response('No existe esa competición <a href="'.$this->generateUrl('mis_competiciones').'">Volver</a>');
   		$response->headers->set('Refresh', '3; url='.$this->generateUrl('mis_competiciones'));
   		return $response;
   	}
   	$atl = $this->getUser()->getIdAtl();
   	if ($atl == null){
   		$response = new Response('No tienes un atleta asociado a tu cuenta <a href="'.$this->generateUrl('mis_competiciones').'">Volver</a>');
   		$response->headers->set('Refresh', '3; url='.$this->generateUrl('mis_competiciones'));
   		return $response;
   	}
   	if ($com->getEsVisible() == false){
   		$response = new Response('Esta competición está oculta <a href="'.$this->generateUrl('mis_competiciones').'">Volver</a>');
   		$response->headers->set('Refresh', '3; url='.$this->generateUrl('mis_competiciones'));
   		return $response;
   	}
   	$entornos = $repoCom->getComEntornos($sidCom);
   	$cat = Helpers::getAtlCurrentCat($this->getDoctrine(), $atl);
   	$repoPar = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Participacion');
   	$par = $repoPar->findBy(array("sidCom" => $sidCom, "idAtl" => $atl->getId()));
      $repoPru = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Prueba');
      $listaPru = $repoPru->searchByParameters($sidCom, $cat['id']);
      $repoIns = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Inscripcion');
      $inss = $repoIns->findForAtl($sidCom, $atl->getId());
      $ayer = (new \DateTime())->sub(new \DateInterval("P1D"));
      $hoy = new \DateTime();
      $prus = array();
      foreach($listaPru as $pru){
      	$pru['inscrito'] = false;
      	$pru['coste'] = null;
      	$pru['estado'] = "No inscrito";
      	if ($atl->getEsAlta() == true){
      	   $pru['activarMarcas'] = ($com->getFecha() < $hoy);
      	   $pru['activarInscripciones'] = (($com->getEsInscrib() == true) && ($com->getFecha() > $ayer));
      	} else {
      		$pru['activarMarcas'] = false;
      		$pru['activarInscripciones'] = false;
      	}
      	foreach($inss as $ins){
      		if ($ins['sidPru'] == $pru['sid']){
      			$pru['inscrito'] = true;
      			$pru['coste'] = $ins['coste'];
      			$pru['estado'] = $ins['estado'];
      			break;
      		}
      	}
      	if (($pru['inscrito'] == false) && ($pru['sexo'] != 2)
      			&& ($pru['sexo'] != $atl->getSexo())) continue;
      	//Otras restricciones
      	$prus[] = $pru;
      }
      $parametros = array("com" => $com, "atl" => $atl, "par" => $par, "cat" => $cat, "prus" => $prus, "ayer" => $ayer, "entornos" => $entornos);
      
   	return $this->render('EasanlesAtletismoBundle:Miscom:inscripcion_miscom.html.twig', $parametros);
   }
   
   public function pantallaMarcasAction(Request $request, $sidCom){
   	$repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
   	$com = $repoCom->find($sidCom);
   	if ($com == null) {
   		$response = new Response('No existe esa competición <a href="'.$this->generateUrl('mis_competiciones').'">Volver</a>');
   		$response->headers->set('Refresh', '3; url='.$this->generateUrl('mis_competiciones'));
   		return $response;
   	}
   	$atl = $this->getUser()->getIdAtl();
   	if ($atl == null){
   		$response = new Response('No tienes un atleta asociado a tu cuenta <a href="'.$this->generateUrl('mis_competiciones').'">Volver</a>');
   		$response->headers->set('Refresh', '3; url='.$this->generateUrl('mis_competiciones'));
   		return $response;
   	}
   	if ($com->getEsVisible() == false){
   		$response = new Response('Esta competición está oculta <a href="'.$this->generateUrl('mis_competiciones').'">Volver</a>');
   		$response->headers->set('Refresh', '3; url='.$this->generateUrl('mis_competiciones'));
   		return $response;
   	}
   	$entornos = $repoCom->getComEntornos($sidCom);
   	$hoy = new \DateTime();
   	$repoIns = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Inscripcion');
   	$inss = $repoIns->findForAtl($sidCom, $atl->getId());
   	$prus = array();
   	$repoIns = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Prueba');
   	foreach ($inss as $ins){
   		$prus[] = $repoIns->find($ins['sidPru']);
   	}
   	$parametros = array('com' => $com, 'prus' => $prus, 'hoy' => $hoy, 'entornos' => $entornos);
   	$selectedPru = $request->query->get('pru');
   	if (($selectedPru != null) && ($selectedPru != "")){
   		$parametros['selectedPru'] = $selectedPru;
   	}
   	return $this->render('EasanlesAtletismoBundle:Miscom:marcas_miscom.html.twig', $parametros);
   }
    
}
