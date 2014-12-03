<?php

namespace Diloog\AfiliadoBundle\Controller;

use Diloog\PagoBundle\Entity\Pago;
use Diloog\PagoBundle\Form\PagoType;
use Diloog\PagoBundle\Form\Model\PagoModel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\File\File;

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
        $afiliado = $this->getUser();
        $estadodeuda=$em->getRepository('PagoBundle:EstadoDeDeuda')->findUltimaDeudaActiva($afiliado);
        if ($estadodeuda->isPagada() ){
            return $this->render('@Afiliado/Default/sindeuda.html.twig');
        }
        return $this->render('@Afiliado/Default/estadodeuda.html.twig',array('deuda'=>$estadodeuda));
    }

    public function procesamientoPagoAction($referencia){
        $clienteid='7912305901278826';
        $clientesecret= '6mDS3QEWFQmmr7qAW6GaWnq5BNHNVobg';
        $MP = new \mercadopago($clienteid, $clientesecret);
        return new Response("<html><head><title>Numero referencia</title></head><body><p>$referencia</p></body></html>");
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


    public function comprobanteDeudaAction(){
        $em = $this->getDoctrine()->getManager();
        $afiliado = $this->getUser();
        $estadodeuda=$em->getRepository('PagoBundle:EstadoDeDeuda')->findUltimaDeudaActiva($afiliado);
        $kernel = $this->get('kernel');
        $directorio = $kernel->getRootDir()."/../web/pdf/";
        $nombrearchivo="deuda".rand(1,24000).$this->getUser()->getNumeroAfiliado().".pdf";
        $this->get('knp_snappy.pdf')->generateFromHtml(
            $this->renderView(
                'AfiliadoBundle:Default:comprobantedeuda.html.twig',
                array(
                    'deuda'=>$estadodeuda,
                    'afiliado' =>$afiliado
                )
            ),
            $directorio.$nombrearchivo
        );

        $rutaarchivo = $directorio.$nombrearchivo;
        $basepath=$this->getRequest()->server->get('DOCUMENT_ROOT');
        $rutadescarga=$basepath."/pdf/".$nombrearchivo;
        $archivo = new File($directorio.$nombrearchivo);
        $response = new Response(file_get_contents($rutadescarga));
       //readfile($directorio.$nombrearchivo);
        $disposicion = $response->headers->makeDisposition("attachment",$nombrearchivo);
        $tamaño = $archivo->getSize();

        //$mime= $archivo->getMimeType();
        $response->headers->set("Content-type", "application/force-download");
        $response->headers->set("Content-Disposition", $disposicion);
        $tamaño = filesize($archivo);
        $response->headers->set("Content-Length", $tamaño);
      //  readfile($rutadescarga);

      //  readfile($rutaarchivo);
        return $response;
    }

    public function pruebaBarcodeAction(){
        return $this->render('AfiliadoBundle:Default:barcode.html.twig');

    }
    public function cuponPagoAction(){
        $em = $this->getDoctrine()->getManager();
        $afiliado = $this->getUser();
        $estadodeuda=$em->getRepository('PagoBundle:EstadoDeDeuda')->findUltimaDeudaActiva($afiliado);
        $kernel = $this->get('kernel');
        $directorio = $kernel->getRootDir()."/../web/pdf/";
        $nombrearchivo="cuponpago".rand(1,24000).$this->getUser()->getNumeroAfiliado().".pdf";
        $codigo = $afiliado->getNumeroAfiliado().$estadodeuda->getNumeroDeuda().rand(255,295);
        $this->get('knp_snappy.pdf')->generateFromHtml(
            $this->renderView(
                'AfiliadoBundle:Default:cupondepago.html.twig',
                array(
                    'deuda'=>$estadodeuda,
                    'afiliado' =>$afiliado,
                    'codigo' => $codigo
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

    public function elegirPagoAction(){
        return $this->render('@Afiliado/Default/elegirpago.html.twig');
    }

    public function elegirTarjetaAction($referer){
        $em = $this->getDoctrine()->getManager();
        $tarjetas = $this->getUser()->getTarjetas();
        return $this->render('PagoBundle:Default:elegirtarjeta.html.twig',array('tarjetas' => $tarjetas, 'referer' => $referer));
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


    public function listarPagosRealizadosAction(){
        $afiliado = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $pagos = $em->getRepository('PagoBundle:Pago')->findUltimosPagos($afiliado);
        //ld($pagos);
        if ($pagos[0]==null){
            $pagos = array();
        }
        //ld($pagos);
        //ld($pagos);
       //return $this->render('@Pago/Default/vervariables.html.twig');
       return $this->render('AfiliadoBundle:Datos:listapagos.html.twig', array('pagos' => $pagos));
       // return $this->render('@Pago/Default/vervariables.html.twig');
    }


    public function realizarPagoAction(Request $request, $idtarjeta, $token){
        $em = $this->getDoctrine()->getManager();
        $tarjeta = $em->getRepository('AfiliadoBundle:Tarjeta')->find($idtarjeta);
        $propia = $this->getUser()->tarjetaPropia($idtarjeta);
        if ($propia === false && $token != md5($tarjeta->getNumeroTarjeta())){
            return $this->render('AfiliadoBundle:Default:tarjetaimpropia.html.twig');
        }
       // ld($propia);
        $afiliado = $this->getUser();
        $estadodeuda=$em->getRepository('PagoBundle:EstadoDeDeuda')->findUltimaDeudaActiva($afiliado);
        $pagomodelo = new PagoModel();
        $pagomodelo->setNumeroTarjeta($tarjeta->getNumeroTarjeta());
        $pagomodelo->setTipoTarjeta($tarjeta->getDescripcionTarjeta());
        $pagomodelo->setVencimiento($tarjeta->getVencimiento());
        $pagomodelo->setDni($this->getUser()->getDni());
        $form = $this->createForm(new PagoType(),$pagomodelo);
        $form->add('submit', 'submit', array('label' => 'Pagar'));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $checkmanager = $this->get('checkout.manager');
           // $checkmanager->setServer(new CheckoutServer());
            $checkmanager->definirDatos($tarjeta, $estadodeuda->getNumeroDeuda(), $estadodeuda->getImporteTotal(), 1, $pagomodelo->getCodigoSeguridad());
            $valido = $checkmanager->validateCheckout();
            if($valido){
                $idpago = $checkmanager->getIdPago();
                $session = $request->getSession();
                $session->set('payment',$idpago);
                $pago = new Pago();
                $pago->setCantidadCuotas(1);
                $pago->setEstadoDeuda($estadodeuda);
                $pago->setNumeroSeguimiento($idpago);
                $pago->setFechaPago(new \DateTime('now'));
                $estadodeuda->setPagada(true);
                $em->persist($pago);
                $em->flush();
               // ld($pago);
                //$id = $pago->getId();
                //$session->set('paymentid',$id);
               return $this->redirect($this->generateUrl('pago_succes'));
                //return $this->render('@Pago/Default/vervariables.html.twig');
            }
            else {
                return $this->redirect($this->generateUrl('pago_fail'));
            }
        }

        return $this->render('PagoBundle:Default:realizarpago.html.twig', array(
            'form'   => $form->createView(),
            'deuda'  => $estadodeuda
        ));
    }

    public function realizarFinanciacionAction(Request $request, $idtarjeta, $token){
        $em = $this->getDoctrine()->getManager();
        $tarjeta = $em->getRepository('AfiliadoBundle:Tarjeta')->find($idtarjeta);
        $propia = $this->getUser()->tarjetaPropia($idtarjeta);
        if ($propia === false && $token != md5($tarjeta->getNumeroTarjeta())){
            return $this->render('AfiliadoBundle:Default:tarjetaimpropia.html.twig');
        }
        $afiliado = $this->getUser();
        $estadodeuda=$em->getRepository('PagoBundle:EstadoDeDeuda')->findUltimaDeudaActiva($afiliado);
        $pagomodelo = new PagoModel();
        $pagomodelo->setNumeroTarjeta($tarjeta->getNumeroTarjeta());
        $pagomodelo->setTipoTarjeta($tarjeta->getDescripcionTarjeta());
        $pagomodelo->setVencimiento($tarjeta->getVencimiento());
        $pagomodelo->setDni($this->getUser()->getDni());
        $form = $this->createForm(new PagoType(),$pagomodelo);
        $form->add('cantidadCuotas','choice', array('choices'   => array('1' =>'1','2' => '2', '3' => '3', '4' => '4')));
        $form->add('submit', 'submit', array('label' => 'Pagar'));
        $form->handleRequest($request);

        if ($form->isValid()) {

            $checkmanager = $this->get('checkout.manager');
            // $checkmanager->setServer(new CheckoutServer());
            $checkmanager->definirDatos($tarjeta, $estadodeuda->getNumeroDeuda(), $estadodeuda->getImporteTotal(), 1, $pagomodelo->getCodigoSeguridad());
            $valido = $checkmanager->validateCheckout();
            if($valido){
                $idpago = $checkmanager->getIdPago();
                $session = $request->getSession();
                $session->set('payment',$idpago);
                $pago = new Pago();
                $pago->setCantidadCuotas($pagomodelo->getCantidadCuotas());
                $pago->setEstadoDeuda($estadodeuda);
                $pago->setNumeroSeguimiento($idpago);
                $pago->setFechaPago(new \DateTime('now'));
                $estadodeuda->setPagada(true);
                $em->persist($pago);
                $em->flush();
                return $this->redirect($this->generateUrl('pago_succes'));
            }
            else {
                return $this->redirect($this->generateUrl('pago_fail'));
            }
        }

        return $this->render('PagoBundle:Default:realizarfinanciacion.html.twig', array(
            'form'   => $form->createView(),
            'deuda'  => $estadodeuda
        ));

    }

    public function obtenerUsuarioPruebaAction(){
        $clienteid='7912305901278826';
        $clientesecret= '6mDS3QEWFQmmr7qAW6GaWnq5BNHNVobg';
        $MP = new \mercadopago($clienteid, $clientesecret);
       /*$token = $MP->get_access_token();
       $url= 'https://api.mercadolibre.com/users/test_user?access_token='.$token;
       $ch = curl_init($url);
        $jsonData = array(
            'site_id' => 'MLA'
        );
        $jsonDataEncoded = json_encode($jsonData);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($jsonData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        if($result === false)
        {
            echo "Error Number:".curl_errno($ch)."<br>";
            echo "Error String:".curl_error($ch);
        }
       */
       // ladybug_dump($result);
        //$array_usuario = json_decode($result);
        $token = "APP_USR-7912305901278826-101221-90e20568934ec8578551229a2a5e550c__D_E__-168184967";
        $url = "https://api.mercadolibre.com/users/test_user?access_token=$token";
        $vars = "{\"site_id\":\"MLA\"}";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $vars);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $respuesta = trim(curl_exec($curl));
        curl_close($curl);
        ladybug_dump($respuesta);
        $array_usuario = json_decode($respuesta);

        return $this->render("@Afiliado/Default/usuariopruebamp.html.twig", array( 'token' => $token, 'resultado' => $array_usuario));

    }

    public function vistaPreviaAction(){
        $em = $this->getDoctrine()->getManager();
        $afiliado = $this->getUser();
        $estadodeuda=$em->getRepository('PagoBundle:EstadoDeDeuda')->findUltimaDeudaActiva($afiliado);
        $basepath=$this->getRequest()->server->get('DOCUMENT_ROOT');
        $nombrearchivo = 'bootstrap.min.css';
        $rutacss=$basepath."/bundles/afiliado/css/".$nombrearchivo;
        return $this->render(
            'AfiliadoBundle:Default:comprobantedeuda.html.twig',
            array(
                'rutacss' => $rutacss,
                'deuda' =>$estadodeuda,
                'afiliado' =>$afiliado
            )
        );
    }

}
