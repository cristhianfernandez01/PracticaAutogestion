<?php
/**
 * Created by PhpStorm.
 * User: Cristhian
 * Date: 28/10/14
 * Time: 20:41
 */

namespace Diloog\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AfiliadoFilterType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('apellido', 'filter_text');
        $builder->add('numeroAfiliado', 'filter_number');
    }

    public function getName()
    {
        return 'afiliado_filter';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
    }
} 