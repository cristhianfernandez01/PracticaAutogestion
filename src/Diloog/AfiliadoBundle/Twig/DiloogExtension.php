<?php
namespace Diloog\AfiliadoBundle\Twig;

class DiloogExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('md5', array($this, 'md5Function')),
        );
    }

    public function md5Function($cadena)
    {
        $hash = md5($cadena); 

        return $hash;
    }

    public function getName()
    {
        return 'diloog_extension';
    }
}