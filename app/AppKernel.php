<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Diloog\AfiliadoBundle\AfiliadoBundle(),
            new Diloog\BackendBundle\BackendBundle(),
            new Diloog\PagoBundle\PagoBundle(),
			new Knp\Bundle\SnappyBundle\KnpSnappyBundle(),
			new RaulFraile\Bundle\LadybugBundle\RaulFraileLadybugBundle(),
			new Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
			new Avalanche\Bundle\ImagineBundle\AvalancheImagineBundle(),
            new Mopa\Bundle\BarcodeBundle\MopaBarcodeBundle(),
			new Liuggio\ExcelBundle\LiuggioExcelBundle(),
			new APY\DataGridBundle\APYDataGridBundle(),
			new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
			new Lexik\Bundle\FormFilterBundle\LexikFormFilterBundle(),
			new WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
