<?php
/**
 * Created by PhpStorm.
 * User: Cristhian
 * Date: 29/10/14
 * Time: 14:22
 */

namespace Diloog\BackendBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Diloog\PagoBundle\Entity\EstadoDeDeuda;

class EstadosDeDeuda extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface ,ContainerAwareInterface{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
       // $afiliado1 = $manager->find('AfiliadoBundle:Afiliado', 1);
        $deuda1 = new EstadoDeDeuda();
        $deuda1->setNumeroDeuda(233123);
        $deuda1->setFechaEmision(new \DateTime());
        $deuda1->setImporteTotal(35);
        $deuda1->setActiva(false);
        $deuda1->setPagada(true);
        $deuda1->setAfiliado($this->getReference('afiliado-1'));
        $manager->persist($deuda1);


        $deuda2 = new EstadoDeDeuda();
        $deuda2->setNumeroDeuda(234567);
        $deuda2->setFechaEmision(new \DateTime());
        $deuda2->setImporteTotal(40);
        $deuda2->setActiva(true);
        $deuda2->setPagada(false);
        $deuda2->setAfiliado($this->getReference('afiliado-1'));
        $manager->persist($deuda2);

       // $afiliado2 = $manager->find('AfiliadoBundle:Afiliado', 2);
        $deuda3 = new EstadoDeDeuda();
        $deuda3->setNumeroDeuda(223456);
        $deuda3->setFechaEmision(new \DateTime());
        $deuda3->setImporteTotal(38);
        $deuda3->setActiva(true);
        $deuda3->setPagada(false);
        $deuda3->setAfiliado($this->getReference('afiliado-2'));
        $manager->persist($deuda3);

        $manager->flush();
    }
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 3; // the order in which fixtures will be loaded
    }

} 