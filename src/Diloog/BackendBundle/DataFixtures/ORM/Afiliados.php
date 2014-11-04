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
use Diloog\AfiliadoBundle\Entity\Afiliado;

class Afiliados extends AbstractFixture  implements FixtureInterface, OrderedFixtureInterface ,ContainerAwareInterface{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {

        $estado1 = $this->getReference('estado-1');
        $estado2 = $this->getReference('estado-2');
        $estado3 = $this->getReference('estado-3');
        $estado4 = $this->getReference('estado-4');
        $estado5 = $this->getReference('estado-5');
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

        $afiliado1 = new Afiliado();

        $afiliado1->setNombre('Hugo');
        $afiliado1->setApellido('Denett');
        $afiliado1->setDni(15572345);
        $afiliado1->setDomicilio('25 de Mayo 342');
        $afiliado1->setLocalidad('San Miguel de Tucuman');
        $afiliado1->setAlias('hugo_denett');
        $afiliado1->setEstado($estado1);
        $afiliado1->setPassword('12345');
        $afiliado1->setSalt('');
        $afiliado1->setEmail('h_denett@hotmail.com');
        $afiliado1->setNumeroAfiliado(2341);
        $manager->persist($afiliado1);

        $afiliado2 = new Afiliado();

        $afiliado2->setNombre('Cristhian');
        $afiliado2->setApellido('Fernandez');
        $afiliado2->setDni(35029798);
        $afiliado2->setDomicilio('San Juan 368');
        $afiliado2->setLocalidad('Tafi Viejo');
        $afiliado2->setAlias('cristhian_fer');
        $afiliado2->setEstado($estado1);
        $afiliado2->setPassword('123');
        $afiliado2->setSalt('');
        $afiliado2->setEmail('cristhian.fernandez01@gmail.com');
        $afiliado2->setNumeroAfiliado(3721);

        $manager->persist($afiliado2);

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

        $this->addReference('afiliado-1', $afiliado1);
        $this->addReference('afiliado-2', $afiliado2);
    }
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2; // the order in which fixtures will be loaded
    }

} 