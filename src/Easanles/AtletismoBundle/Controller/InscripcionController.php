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
    	     $estado = "";
    	     foreach($pruebasIns as $ins){
    	        $costeTotal += $ins['coste'];
    	        $estado = $ins['estado']; //TODO: Estados parciales
    	     }
    	     $atlData['costetotal'] = $costeTotal;
    	     $atlData['estado'] = $estado;
    	     $par = $repoPar->findOneBy(array('idAtl' => $atl, 'sidCom' => $com));
    	     if ($par == null){
    	        $atlData['estado'] = "No participa";
    	        $atlData['dorsal'] = "--";
    	        $atlData['asistencia'] = false;
    	     } else {
    	     	  $atlData['sidPar'] = $par->getSid();
    	     	  $atlData['estado'] = "Confirmado";
    	     	  $atlData['dorsal'] = $par->getDorsal();
    	        $atlData['asistencia'] = $par->getAsisten();
    	     }

    	     $atletas[] = $atlData;
    	  }
    	  $parametros['atletas'] = $atletas;
    	   
        return $this->render('EasanlesAtletismoBundle:Inscripcion:list_inscripcion.html.twig', $parametros);
    }
}
