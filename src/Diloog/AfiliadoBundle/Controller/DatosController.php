<?php

namespace Diloog\AfiliadoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Diloog\AfiliadoBundle\Form\Model\ChangePassword;

class DatosController extends Controller
{
    public function indexAction()
    {
        $afiliado = $this->getUser();
        return $this->render('AfiliadoBundle:Datos:datosafiliado.html.twig', array('afiliado' => $afiliado));
    }


    public function cambioPasswordAction(Request $request){
        $cambioPassword = new ChangePassword();
        $form = $this->createFormBuilder($cambioPassword)
            ->add('oldpassword', 'password')
            ->add('newpassword', 'password')
            ->add('newpassword2', 'password')
            ->add('aceptar', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $cambioPassword = $form->getData();
            $afiliado = $this->getUser();
            $afiliado->setPassword($cambioPassword->getNewpassword());
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha cambiado exitosamente la contraseña.'
            );

            return $this->redirect($this->generateUrl('datos_afiliado'));
        }

       return $this->render('AfiliadoBundle:Datos:cambiopassword.html.twig', array('form' => $form->createView()));

    }
}
