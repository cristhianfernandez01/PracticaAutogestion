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

    public function procesamientoPagoAction(){
        $clienteid='7912305901278826';
        $clientesecret= '6mDS3QEWFQmmr7qAW6GaWnq5BNHNVobg';
        $MP = new \mercadopago($clienteid, $clientesecret);

    }

    public function pagopruebaAction(){
        $referencia= "Afiliado referencia 1256";
        $router=$this->get('router');
        $url_retorno = $router->generate('procesar_pago',array('referencia' => $referencia),true);
        $clienteid='7912305901278826';
        $clientesecret= '6mDS3QEWFQmmr7qAW6GaWnq5BNHNVobg';
       $MP = new \mercadopago($clienteid, $clientesecret);
        $MP->sandbox_mode(TRUE);
     // $datos = $MP->get_payment(213456);
        $preference_data= array(
            "items" => array(
                array(
                    "title" => "Pago Deuda Afiliado",
                    "quantity" => 1,
                    "currency_id" => "ARS",
                    "unit_price" => 35.00
                )
            ),
            "back_urls" => array(
                    "success" => $url_retorno,
                    "failure" => "https://www.failure.com",
                    "pending" => "http://www.pending.com"
            ),
            "external_reference"=> $referencia
        );
      $preference = $MP->create_preference($preference_data);


        return $this->render('AfiliadoBundle:Default:botonpago.html.twig', array('preferencias'=>$preference));

    }


    public function pruebaImprimirAction(){
        $em = $this->getDoctrine()->getManager();
        $estadodeuda=$em->getRepository('PagoBundle:EstadoDeDeuda')->find(1);
        $kernel = $this->get('kernel');
        $directorio = $kernel->getRootDir()."/../web/pdf/";
        $nombrearchivo="deuda".rand(1,24000).$this->getUser()->getNumeroAfiliado().".pdf";
        $this->get('knp_snappy.pdf')->generateFromHtml(
            $this->renderView(
                'AfiliadoBundle:Default:comprobantedeuda.html.twig',
                array(
                    'deuda'=>$estadodeuda
                )
            ),
            $directorio.$nombrearchivo
        );

        if (!file_exists($directorio.$nombrearchivo)) {
            throw $this->createNotFoundException();
        }
        $response = new Response();
        $response->headers->set('Content-type', 'application/pdf');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $nombrearchivo));
        $response->headers->set('Content-Length', filesize($directorio.$nombrearchivo));
        return $response;
        //return $this->render('@Afiliado/Default/comprobantedeuda.html.twig',array('deuda'=>$estadodeuda));
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
