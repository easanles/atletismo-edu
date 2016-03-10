<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Easanles\AtletismoBundle\Helpers\Helpers;
use Symfony\Component\HttpFoundation\JsonResponse;
use Easanles\AtletismoBundle\Entity\Inscripcion;

class MiscomController extends Controller{
    public function portadaAction(Request $request){
    	 $repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	 $temp = $request->query->get('temp');    	 
    	 $temp = (int)$temp;    	 
    	 if (($temp == null) || ($temp == "") || (!is_int($temp))){
    	    $temp = Helpers::getCurrentTemp($this->getDoctrine());
    	 }    	 
    	 $user = $this->getUser();
    	 if (($user == null) || ($user->getIdAtl()->getId() == null)){
    	 	$response = new Response('El usuario no tiene un atleta asociado');
    	 	$response->headers->set('Refresh', '2; url='.$this->generateUrl('homepage'));
    	 	return $response;
    	 }
    	 $temps = $repoCom->findTemps("user");
    	 
    	 $tempComs = $repoCom->findTempComs($temp, "user");
    	 $listaComInscritos = $repoCom->findAtlComs($user->getIdAtl()->getId(), $temp);
    	 $listaComs = array();
    	 $ayer = (new \DateTime())->sub(new \DateInterval("P1D"));
    	 $hoy = new \DateTime();
    	 foreach ($tempComs as $com){
    	 	 $com['eshoy'] = (($com['fecha'] > $ayer) && ($com['fecha'] < $hoy));
    	    if (in_array($com['sid'], $listaComInscritos)){
    	    	$com['inscrito'] = true;
    	    } else {
    	    	$com['inscrito'] = false;
    	    }
    	    if (($com['fecha'] >= $ayer) || ($com['inscrito'] == true)) {
    	    	if ($com['numpruebas'] == 1){
    	    		$comObj = $repoCom->find($com['sid']);
    	    		$pru = $comObj->getPruebas()->first();
    	    		if (($pru->getSidTprm()->getSexo() != 2)
    	    				&& ($pru->getSidTprm()->getSexo() != $user->getIdAtl()->getSexo())) continue;
    	    	}
    	    	if (($com['esFeder'] == true)
    	    		   && (($user->getIdAtl()->getLfga() == null) || ($user->getIdAtl()->getLfga() == ""))) continue;
    	    	$listaComs[] = $com;
    	    }
    	 }
    	 $parametros = array("temp" => $temp, "temporadas" => $temps, "coms" => $listaComs, "ayer" => $ayer, "hoy" => $hoy);
    	
       return $this->render('EasanlesAtletismoBundle:Miscom:portada_miscom.html.twig', $parametros);
    }
    
   private function operacionesPruebaUnica(Request $request, $comando){
   	$repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
   	$sidCom = $request->query->get('com');
   	$com = $repoCom->find($sidCom);
   	if ($com == null){
   		return new JsonResponse([
   				'success' => false,
   				'message' => "No existe esa competición"
   		]);
   	}
   	$atl = $this->getUser()->getIdAtl();
   	if ($atl == null){
   		return new JsonResponse([
   				'success' => false,
   				'message' => "No tienes un atleta asociado a tu cuenta"
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
   				'message' => "Esta es una competicion oficial del club. Consulta al coordinador del club"
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
   	$pru = $com->getPruebas()->first();
   	if ($pru == null){
   		return new JsonResponse([
   				'success' => false,
   				'message' => "Esta competición no tiene ninguna prueba"
   		]);
   	}
   	$repoIns = $repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Inscripcion');
   	$ins = $repoIns->findOneBy(array("idAtl" => $atl->getId(), "sidPru" => $pru->getSid()));
   	switch ($comando){
   		case ("inscrib"): {
   			if ($ins != null){
   				return new JsonResponse([
   						'success' => false,
   						'message' => "Ya estabas inscrito a esta competición"
   				]);
   			}
   			if (($pru->getSidTprm()->getSexo() != 2)
   					&& ($pru->getSidTprm()->getSexo() != $atl->getSexo())){
   				return new JsonResponse([
   						'success' => false,
   						'message' => "Esta competición solo tiene una prueba de modalidad ".($pru->getSidTprm()->getSexo() == 1? "femenina" : "masculina")
   				]);
   			}
   		} break;
   		case ("desinscrib"): {
   			if ($ins == null){
   				return new JsonResponse([
   						'success' => false,
   						'message' => "No estabas inscrito a esta competición"
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
    
   public function inscribirsePruebaUnicaAction(Request $request){
   	return $this->operacionesPruebaUnica($request, "inscrib");
   }
   
   public function desinscribirsePruebaUnicaAction(Request $request){
   	return $this->operacionesPruebaUnica($request, "desinscrib");
   }
   
   public function inscripcionAction($sidCom){
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
   	$repoPar = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Participacion');
   	$par = $repoPar->findBy(array("sidCom" => $sidCom, "idAtl" => $atl->getId()));
   	$parametros = array("com" => $com, "atl" => $atl, "par" => $par);
   	return $this->render('EasanlesAtletismoBundle:Miscom:inscripcion_miscom.html.twig', $parametros);
   }
    
}
