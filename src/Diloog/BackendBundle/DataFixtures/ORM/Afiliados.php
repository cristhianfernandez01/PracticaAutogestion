<?php
/**
 * Created by PhpStorm.
 * User: Cristhian
 * Date: 29/10/14
 * Time: 14:22
 */

namespace Diloog\BackendBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Diloog\AfiliadoBundle\Entity\Afiliado;

class Afiliados implements FixtureInterface, ContainerAwareInterface{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        //$em = $this->container->get('doctrine')->getManager();
        $estado1 = $manager->find('AfiliadoBundle:Estado',1);
        $estado2 = $manager->find('AfiliadoBundle:Estado',2);
        $estado3 = $manager->find('AfiliadoBundle:Estado',3);
        $estado4 = $manager->find('AfiliadoBundle:Estado',4);
        $estado5 = $manager->find('AfiliadoBundle:Estado',5);
        $nombres = array("Adan","Alberto","Americo","Abraham","Bruno","Carlos","Juan","Jorge","Mario","Fernando","Daniel","Gabriel", "Agustin", "Marcelo", "Soledad", "Maria", "Pablo", "Luisa", "Minerva", "Camila", "Gladis");
        $apellidos = array("Monteros", "Leon", "Bernaski","Rodriguez","Guerra","Romano","Diaz","Nieva", "Lopez", "Montenegro", "Leguizamon", "Velardez", "Arce", "Barrionuevo", "Gonzalez", "Salvadeo", "Gimenez", "Aragon", "Correa", "Cajal");
        $calles = array("San Juan","Corrientes","Muñecas","Las Heras","España","Italia","Colombia","Gral. Paz","Avellaneda");
        $localidades= array("San Miguel de Tucuman","Tafi Viejo","Yerba Buena","El Manantial","Concepcion","Raco","Trancas","San Miguel de Tucuman","San Miguel de Tucuman","San Miguel de Tucuman");
        $estados = array($estado1,$estado1,$estado1,$estado1,$estado1,$estado2,$estado2,$estado3,$estado4, $estado5, $estado1, $estado1);
        $cantidadlocalidades = count($localidades)-1;
        $cantidadapellidos = count($apellidos)-1;
        $cantidadnombres= count($nombres)-1;
        $cantidadcalles= count($calles)-1;
        $cantidadestados=count($estados)-1;
        for ($i=0; $i<60; $i++) {
            $afiliado = new Afiliado();

            $afiliado->setNombre($nombres[rand(0,$cantidadnombres)]);
            $afiliado->setApellido($apellidos[rand(0,$cantidadapellidos)]);
            $afiliado->setDni(rand(4500000,38000000));
            $afiliado->setDomicilio($calles[rand(0,$cantidadcalles)].rand(0,3000));
            $afiliado->setLocalidad($localidades[rand(0,$cantidadlocalidades)]);
            $afiliado->setAlias('usuario'.$i);
            $afiliado->setEstado($estados[rand(0,$cantidadestados)]);
            $afiliado->setPassword('pass'.$i);
            $afiliado->setSalt('');
            $afiliado->setEmail(strtolower($afiliado->getApellido()).strtolower($afiliado->getNombre()).'@mail.com');
            $afiliado->setNumeroAfiliado(rand(200,4000));
            // ...

            $manager->persist($afiliado);
        }

        $manager->flush();
    }
} 