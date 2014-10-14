<?php

namespace Diloog\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Gaufrette\Filesystem;
use Gaufrette\Adapter\Sftp as SftpAdapter;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('BackendBundle:Default:index.html.twig', array('name' => $name));
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
}
