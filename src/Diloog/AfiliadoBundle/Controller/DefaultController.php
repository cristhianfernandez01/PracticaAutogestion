<?php

namespace Diloog\AfiliadoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('AfiliadoBundle:Default:index.html.twig', array('name' => $name));
    }

    public function ayudaAction(){
        return new Response('Ayuda');
    }
}
