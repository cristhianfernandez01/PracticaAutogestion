<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 25/10/14
 * Time: 4:38
 */

namespace Diloog\PagoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class PagoType extends AbstractType{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tipoTarjeta','text',array('read_only' => true))
            ->add('numeroTarjeta')
            ->add('vencimiento')
            ->add('codigoSeguridad')
            ->add('titularNombre')
            ->add('dni')
        ;
    }



    /**
     * @return string
     */
    public function getName()
    {
        return 'diloog_pagobundle_pago';
    }

} 