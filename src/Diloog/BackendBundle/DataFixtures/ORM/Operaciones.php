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
use Diloog\BackendBundle\Entity\Operacion;

class Operaciones extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface ,ContainerAwareInterface{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {

        for($i=1; $i<=20; $i++){
            if ($i <=9){
                $fecha = new \DateTime('2014-09-0'.$i);
            }
            else {
                $fecha = new \DateTime('2014-09-'.$i);
            }
            $operacion = new Operacion();
            $operacion->setFecha($fecha);
            $operacion->setTipo('Envio Pagos');
            $operacion->setDescripcion('Se realizaron con exito '.$i.' pagos');
            $manager->persist($operacion);
        }

        for($i=1; $i<=20; $i++){
            if ($i <=9){
                $fecha = new \DateTime('2014-04-0'.$i);
            }
            else {
                $fecha = new \DateTime('2014-04-'.$i);
            }
            $operacion = new Operacion();
            $operacion->setFecha($fecha);
            $operacion->setTipo('Actualizacion Deudas');
            $operacion->setDescripcion('Se recibieron con exito '.$i.' deudas');
            $manager->persist($operacion);
        }

        $manager->flush();

    }

    public function getOrder()
    {
        return 4; // the order in which fixtures will be loaded
    }

} 