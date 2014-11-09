<?php
/**
 * Created by PhpStorm.
 * User: Cristhian
 * Date: 28/10/14
 * Time: 20:41
 */

namespace Diloog\BackendBundle\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OperacionFilterType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('tipo', 'filter_choice', array('choices'   => array('Envio pagos' => 'Envio Pagos', 'Cambio Estados' => 'Cambio Estados', 'Actualizacion Deudas' => 'Actualizacion Deudas')));
        $builder->add('fecha', 'filter_date_range');
    }

    public function getName()
    {
        return 'operacion_filter';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
    }
} 