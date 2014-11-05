<?php

namespace Diloog\BackendBundle\Command;

use Diloog\AfiliadoBundle\Entity\Afiliado;
use Gaufrette\Adapter\Local;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Gaufrette\Filesystem;
use Gaufrette\Adapter\Sftp as SftpAdapter;

class BackendRecibirCambioEstadoCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('diloog:backend:recibircambioestado')
            ->setDescription('Recibe datos de cambios de los afiliados')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sftp = $this->getContainer()->get('diloog_backend.sftp');
        $adapter = new SftpAdapter($sftp,'/afiliados/');
        $filesystem = new Filesystem($adapter);
        $entitymanager = $this->getContainer()->get('doctrine')->getManager();

        $nombrearchivo = 'Afiliados.csv';
        if($filesystem->has($nombrearchivo)){
            $localadapter = new Local('../files/Afiliados/',true);
            $filesystem2 = new Filesystem($localadapter);
           $archivo =$filesystem->get('Afiliados.csv');
          $contenido =  $archivo->getContent();
            $fechaactual = new \DateTime('now');
            $fecha = trim($fechaactual->format('d-m-YH_i_s'));
            $nombrearchivo = 'Afiliados'.$fecha.'.csv';
            $filesystem2->createFile($nombrearchivo);
            $filesystem2->write($nombrearchivo, $contenido);
        }
        //$archivo = fopen('../files/'.$nombrearchivo ,'x+');
        $factory= $this->getContainer()->get('phpexcel');
       //$objReader = \PHPExcel_IOFactory::createReader('CSV');
        $objReader = new \PHPExcel_Reader_CSV();
        $objReader->setDelimiter(',');
        $objReader->setEnclosure('');
        $objReader->setLineEnding('\r\n');

        $objPHPExcel = $objReader->load('../files/Afiliados/'.$nombrearchivo);
        $objWorksheet = $objPHPExcel->getActiveSheet();
        foreach ($objWorksheet->getRowIterator() as $row) {
            $row_index = $row->getRowIndex();
            if($row_index==1){
                $tipoarchivo = $objWorksheet->getCellByColumnAndRow(0,$row_index)->getValue();
                $cantidadafiliados = $objWorksheet->getCellByColumnAndRow(1,$row_index)->getValue();
                $fechaarchivo = $objWorksheet->getCellByColumnAndRow(2,$row_index)->getValue();
                $output->writeln(array($tipoarchivo, $cantidadafiliados, $fechaarchivo));
            }
            else{
           $numeroafiliado = $objWorksheet->getCellByColumnAndRow(0,$row_index)->getValue();
           $nombre = $objWorksheet->getCellByColumnAndRow(1,$row_index)->getValue();
           $apellido = $objWorksheet->getCellByColumnAndRow(2,$row_index)->getValue();
           $domicilio = $objWorksheet->getCellByColumnAndRow(3,$row_index)->getValue();
           $alias = $objWorksheet->getCellByColumnAndRow(4,$row_index)->getValue();
           $password = $objWorksheet->getCellByColumnAndRow(5,$row_index)->getValue();
           $salt = $objWorksheet->getCellByColumnAndRow(6,$row_index)->getValue();
           $email = $objWorksheet->getCellByColumnAndRow(7,$row_index)->getValue();
           $dni = $objWorksheet->getCellByColumnAndRow(8,$row_index)->getValue();
           $localidad =  $objWorksheet->getCellByColumnAndRow(9,$row_index)->getValue();
          //  $output->writeln(array($numeroafiliado, $nombre, $apellido, $domicilio, $alias, $password, $salt, $email, $dni, $localidad));
            $this->guardarAfiliado($entitymanager, $numeroafiliado, $nombre, $apellido, $domicilio, $alias, $password, $salt, $email, $dni, $localidad);
            }
           }

        $entitymanager->flush();

    }

    protected function guardarAfiliado($entitymanager, $numeroafiliado, $nombre, $apellido, $domicilio, $alias, $password, $salt, $email, $dni, $localidad){
        $estadoactiivo = $entitymanager->getRepository('AfiliadoBundle:Estado')->findOneBy(array('nombre'=>'Activo'));
        $afiliado = new Afiliado();
        $afiliado->setNumeroAfiliado($numeroafiliado);
        $afiliado->setNombre($nombre);
        $afiliado->setApellido($apellido);
        $afiliado->setDomicilio($domicilio);
        $afiliado->setAlias($alias);
        $afiliado->setPassword($password);
        $afiliado->setSalt($salt);
        $afiliado->setEmail($email);
        $afiliado->setEstado($estadoactiivo);
        $afiliado->setDni($dni);
        $afiliado->setLocalidad($localidad);
        $entitymanager->persist($afiliado);
    }

}