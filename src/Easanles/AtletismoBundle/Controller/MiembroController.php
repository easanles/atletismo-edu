<?php

namespace Easanles\AtletismoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MiembroController extends Controller
{
    public function mostrarAction($id)
    {
        return $this->render('EasanlesAtletismoBundle:Miembro:index.html.twig', array('id' => $id));
    }
}
