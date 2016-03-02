<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MiscomController extends Controller{
    public function portadaAction(){
       return $this->render('EasanlesAtletismoBundle:Miscom:portada_miscom.html.twig');
    }
    
}
