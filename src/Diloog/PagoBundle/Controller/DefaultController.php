<?php

namespace Diloog\PagoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('PagoBundle:Default:index.html.twig', array('name' => $name));
    }
}
