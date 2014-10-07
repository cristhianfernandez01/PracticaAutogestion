<?php

namespace Diloog\AfiliadoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AfiliadoBundle:Default:index.html.twig', array());
    }

    public function ayudaAction(){
        return new Response('Ayuda');
    }


    public function estadoDeudaMostrarAction(){
        $em = $this->getDoctrine()->getManager();
        $estadodeuda=$em->getRepository('PagoBundle:EstadoDeDeuda')->find(1);
        return $this->render('@Afiliado/Default/estadodeuda.html.twig',array('deuda'=>$estadodeuda));

    }

    public function pagopruebaAction(){
        $clienteid=7912305901278826;
        $clientesecret= "6mDS3QEWFQmmr7qAW6GaWnq5BNHNVobg";
       $MP = new \mercadopago($clienteid, $clientesecret);

        return $this->render('AfiliadoBundle:Default:index.html.twig', array());

    }


    public function pruebaImprimirAction(){
        $em = $this->getDoctrine()->getManager();
        $estadodeuda=$em->getRepository('PagoBundle:EstadoDeDeuda')->find(1);
        $this->get('knp_snappy.pdf')->generateFromHtml(
            $this->renderView(
                'AfiliadoBundle:Default:comprobantedeuda.html.twig',
                array(
                    'deuda'=>$estadodeuda
                )
            ),
            'C:\Proyectos\Symfony2\PracticaSupervisada\web\pdf\archivo.pdf'
        );
        return $this->render('@Afiliado/Default/comprobantedeuda.html.twig',array('deuda'=>$estadodeuda));
    }

    public function loginAction(){
        $peticion = $this->getRequest();
        $sesion = $peticion->getSession();
        $error = $peticion->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $sesion->get(SecurityContext::AUTHENTICATION_ERROR)
        );
        return $this->render('AfiliadoBundle::login.html.twig', array(
            'last_username' => $sesion->get(SecurityContext::LAST_USERNAME),
            'error' => $error
        ));

    }
}
