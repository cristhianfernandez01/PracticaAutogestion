<?php

namespace Diloog\AfiliadoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TarjetaType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numeroTarjeta')
            ->add('descripcionTarjeta','choice', array('choices'   => array('VISA' => 'VISA', 'MasterCard' => 'MasterCard', 'AmericanExpress' => 'AmericanExpress')))
            ->add('vencimiento')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Diloog\AfiliadoBundle\Entity\Tarjeta'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'diloog_afiliadobundle_tarjeta';
    }
}
