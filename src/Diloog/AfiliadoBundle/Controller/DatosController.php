<?php

namespace Diloog\AfiliadoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DatosController extends Controller
{
    public function indexAction()
    {
        $afiliado = $this->getUser();
        return $this->render('AfiliadoBundle:Datos:datosafiliado.html.twig', array('afiliado' => $afiliado));
    }
}
