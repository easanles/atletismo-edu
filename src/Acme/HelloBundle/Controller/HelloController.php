<?php

namespace Acme\HelloBundle\Controller;
 
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
 
class HelloController extends Controller{
    public function indexAction($name) {
        return $this->render('AcmeHelloBundle:Hello:index.html.twig', array('name' => $name));

        // si utilizas PHP en vez de Twig
        // return $this->render('AcmeHelloBundle:Hello:index.html.php', array('name' => $name));
    }
}

