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
    	 $hoy = (new \DateTime())->sub(new \DateInterval("P1D"));
    	 foreach ($tempComs as $com){
    	    if (in_array($com['sid'], $listaComInscritos)){
    	    	$com['inscrito'] = true;
    	    } else {
    	    	$com['inscrito'] = false;
    	    }
    	    if (($com['fecha'] >= $hoy) || ($com['inscrito'] == true)) {
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
    	 $parametros = array("temp" => $temp, "temporadas" => $temps, "coms" => $listaComs, "hoy" => $hoy);
    	
       return $this->render('EasanlesAtletismoBundle:Miscom:portada_miscom.html.twig', $parametros);
    }
    
   public function inscribirsePruebaUnicaAction(Request $request){
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
   	$repoIns = $repoCom = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Inscripcion');
   	$checkIns = $repoIns->findOneBy(array("idAtl" => $atl->getId(), "sidPru" => $pru->getSid()));
   	if ($checkIns != null){
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
   	try {
         $ins = new Inscripcion();
   		$ins->setIdAtl($atl)
   		->setSidPru($pru)
   		->setCoste(0)
   		->setOrigen($this->getUser()->getNombre())
   		->setFecha(new \DateTime())
   		->setEstado("Pagado")
   		->setCodGrupo(null);
   		$em = $this->getDoctrine()->getManager();
   		$em->persist($ins);
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
    
}
