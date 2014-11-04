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
use Diloog\AfiliadoBundle\Entity\Estado;

class Estados extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface ,ContainerAwareInterface{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $estado1 = new Estado();
        $estado1->setNombre('Activo');
        $manager->persist($estado1);

        $estado2 = new Estado();
        $estado2->setNombre('Suspendido');
        $manager->persist($estado2);

        $estado3 = new Estado();
        $estado3->setNombre('Baja');
        $manager->persist($estado3);

        $estado4 = new Estado();
        $estado4->setNombre('Socio Vitalicio');
        $manager->persist($estado4);

        $estado5 = new Estado();
        $estado5->setNombre('Mantenimiento de Matricula');
        $manager->persist($estado5);


        $manager->flush();

        $this->addReference('estado-1', $estado1);
        $this->addReference('estado-2', $estado2);
        $this->addReference('estado-3', $estado3);
        $this->addReference('estado-4', $estado4);
        $this->addReference('estado-5', $estado5);
    }

    public function getOrder()
    {
        return 1; // the order in which fixtures will be loaded
    }

} 