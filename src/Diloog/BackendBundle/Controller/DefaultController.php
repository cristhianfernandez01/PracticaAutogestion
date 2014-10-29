<?php

namespace Diloog\BackendBundle\Controller;

use APY\DataGridBundle\Grid\Source\Entity;
use Diloog\BackendBundle\Form\AfiliadoFilterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Gaufrette\Filesystem;
use Gaufrette\Adapter\Sftp as SftpAdapter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $name = 'Mundo';
        return $this->render('BackendBundle:Default:index.html.twig', array('name' => $name));
    }

    public function loginAction(){
        $peticion = $this->getRequest();
        $sesion = $peticion->getSession();
        $error = $peticion->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $sesion->get(SecurityContext::AUTHENTICATION_ERROR)
        );
        return $this->render('BackendBundle::login.html.twig', array(
            'last_username' => $sesion->get(SecurityContext::LAST_USERNAME),
            'error' => $error
        ));

    }


    public  function envioAction(){
      $sftp = $this->get('diloog_backend.sftp');
      $adapter = new SftpAdapter($sftp,'/pagos/');
      $filesystem = new Filesystem($adapter);
      //ld($filesystem);
      $content = 'Hello I am the new content';
      $filesystem->write('archivo.txt', $content);
      return new Response('<html><head><title>Prueba SFTP</title></head><body><h1>Esto es una prueba para SFTP</h1></body></html>');
    }

    public function pruebaExcelAction(){
        $factory= $this->get('phpexcel');
       $phpexcel = $factory->createPHPExcelObject('Archivo');
       // $writer = $factory->createWriter($phpexcel,'CSV');
        $writer = $this->get('phpexcel')->createWriter($phpexcel, 'CSV')
            ->setDelimiter(',')
            ->setEnclosure('"')
            ->setLineEnding("\r\n")
            ->setSheetIndex(0)
            ->save(str_replace('.php', '.csv', __FILE__));
    }

    public function listarAfiliadoAction(){
        $source = new Entity('AfiliadoBundle:Afiliado');

        // Get a grid instance
        $grid = $this->get('grid');

        // Attach the source to the grid
        $grid->setSource($source);

        // Configuration of the grid

        // Manage the grid redirection, exports and the response of the controller
        return $grid->getGridResponse('@Backend/Default/listaafiliados.html.twig');
    }
}
