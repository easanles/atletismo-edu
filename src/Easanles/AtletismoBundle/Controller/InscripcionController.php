<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Easanles\AtletismoBundle\Helpers\Helpers;

class InscripcionController extends Controller
{
    public function listadoInscripcionesAction($sidCom, Request $request) {
    	  $repository = $this->getDoctrine()->getRepository('EasanlesAtletismoBundle:Competicion');
    	  $com = $repository->find($sidCom);
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
}
