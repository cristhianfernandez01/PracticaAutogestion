<?php

namespace Diloog\PagoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('PagoBundle:Default:index.html.twig', array('name' => $name));
    }


    public function pagoSuccessAction(Request $request){
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $seguimiento = $session->get('payment');
        $pago = $em->getRepository('PagoBundle:Pago')->findOneBy(array('numeroSeguimiento'=>$seguimiento));
        $idpago = $pago->getId();
        //$idpago = $session->get('paymentid');
        return $this->render('PagoBundle:Default:pagosuccess.html.twig', array('seguimiento' => $seguimiento,
                                                                               'idpago' => $idpago));
    }

    public function pagoFailAction(){
        return $this->render('@Pago/Default/pagofail.html.twig');
    }

    public function comprobantePagoAction($idpago){
        $em = $this->getDoctrine()->getManager();
        $afiliado = $this->getUser();
        $pago=$em->getRepository('PagoBundle:Pago')->find($idpago);
        $kernel = $this->get('kernel');
        $directorio = $kernel->getRootDir()."/../web/pdf/";
        $nombrearchivo="pago".rand(10000000,60000000).$this->getUser()->getNumeroAfiliado().".pdf";
        $this->get('knp_snappy.pdf')->generateFromHtml(
            $this->renderView(
                'PagoBundle:Default:comprobantepago.html.twig',
                array(
                    'pago' => $pago,
                    'afiliado' => $afiliado
                )
            ),
            $directorio.$nombrearchivo
        );
        $rutaarchivo = $directorio.$nombrearchivo;
        $basepath=$this->getRequest()->server->get('DOCUMENT_ROOT');
        $rutadescarga=$basepath."/pdf/".$nombrearchivo;
        $archivo = new File($directorio.$nombrearchivo);
        $response = new Response(file_get_contents($rutadescarga));
        $disposicion = $response->headers->makeDisposition("attachment",$nombrearchivo);
        $tamaño = $archivo->getSize();

        $response->headers->set("Content-type", "application/force-download");
        $response->headers->set("Content-Disposition", $disposicion);
        $tamaño = filesize($archivo);
        $response->headers->set("Content-Length", $tamaño);

        return $response;
    }
}
