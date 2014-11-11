<?php

namespace Diloog\BackendBundle\Command;

use Diloog\AfiliadoBundle\Entity\Afiliado;
use Diloog\BackendBundle\Entity\Operacion;
use Gaufrette\Adapter\Local;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Gaufrette\Filesystem;
use Gaufrette\Adapter\Sftp as SftpAdapter;

class BackendRecibirAfiliadosControlCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('diloog:backend:recibirafiliadoscontrol')
            ->setDescription('Controla la recepcion de datos de nuevos afiliados registrados')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entitymanager = $this->getContainer()->get('doctrine')->getManager();
        $control = $entitymanager->getRepository('BackendBundle:ControlOperacion')->findOneBy(array('codigo' => 5));
        if($control->getRealizada==false){
        $sftp = $this->getContainer()->get('diloog_backend.sftp');
        $adapter = new SftpAdapter($sftp,'/afiliados/');
        $filesystem = new Filesystem($adapter);

        $nombrearchivo = 'Afiliados.csv';
        if($filesystem->has($nombrearchivo)){
            $localadapter = new Local('../files/Afiliados/',true);
            $filesystem2 = new Filesystem($localadapter);
           $archivo =$filesystem->get($nombrearchivo);
          $contenido =  $archivo->getContent();
            $fechaactual = new \DateTime('now');
            $fecha = trim($fechaactual->format('d-m-YH_i_s'));
            $nombrearchivo = 'Afiliados'.$fecha.'.csv';
            $filesystem2->createFile($nombrearchivo);
            $filesystem2->write($nombrearchivo, $contenido);
        }
        else{
            $descripcion = 'ERROR - Se produjo un error. No se ha encontrado el archivo de Afiliados en el servidor';
            $this->gestionarErrorOperacion($entitymanager, $descripcion);
            $this->controlOperacion($entitymanager, false);
            throw new \Exception('No se ha encontrado el archivo en el servidor');
            exit;
        }
        //$archivo = fopen('../files/'.$nombrearchivo ,'x+');
        $factory= $this->getContainer()->get('phpexcel');
       //$objReader = \PHPExcel_IOFactory::createReader('CSV');
        $objReader = new \PHPExcel_Reader_CSV();
        $objReader->setDelimiter(',');
        $objReader->setEnclosure('');
        $objReader->setLineEnding('\r\n');
      try{
          $objPHPExcel = $objReader->load('../files/Afiliados/'.$nombrearchivo);
          $objWorksheet = $objPHPExcel->getActiveSheet();
      }
      catch(\Exception $e){
          // $output->writeln('Excepcion capturada '."\n".$e->getMessage());
          $descripcion = 'ERROR - No se pudo cargar el archivo para su procesamiento';
          $this->gestionarErrorOperacion($entitymanager, $descripcion);
          $this->controlOperacion($entitymanager, false);
          $output->writeln('No se pudo realizar la operacion');
          return;
      }
        $cantidadafiliados = 0;
        foreach ($objWorksheet->getRowIterator() as $row) {
            $row_index = $row->getRowIndex();
            if($row_index==1){
                $tipoarchivo = $objWorksheet->getCellByColumnAndRow(0,$row_index)->getValue();
                $cantidadafiliados = $objWorksheet->getCellByColumnAndRow(1,$row_index)->getValue();
                $fechaarchivo = $objWorksheet->getCellByColumnAndRow(2,$row_index)->getValue();
                $output->writeln(array($tipoarchivo, $cantidadafiliados, $fechaarchivo));

                if($tipoarchivo != 'Nuevos Afiliados'){
                    $descripcion = 'ERROR - El archivo recibido no es del tipo correcto';
                    $this->gestionarErrorOperacion($entitymanager, $descripcion);
                    $this->controlOperacion($entitymanager, false);
                    throw new \Exception('El archivo recibido no es correcto');
                    exit;
                }
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

        $operacion = new Operacion();
        $operacion->setFecha(new \DateTime('now'));
        $operacion->setTipo('Nuevos Afiliados');
        $operacion->setDescripcion('Se han agregado exitosamente los datos de '.$cantidadafiliados.' afiliados');
        $entitymanager->persist($operacion);

        $entitymanager->flush();
        $this->controlOperacion($entitymanager, true);
      }

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

    protected function gestionarErrorOperacion($entitymanager ,$descripcion){
        $operacion = new Operacion();
        $operacion->setFecha(new \DateTime('now'));
        $operacion->setTipo('Nuevos Afiliados');
        $operacion->setDescripcion($descripcion);
        $entitymanager->persist($operacion);
        $entitymanager->flush();
    }

    protected function controlOperacion($entitymanager, $valor){
        $control = $entitymanager->getRepository('BackendBundle:ControlOperacion')->findOneBy(array('codigo'=>5));
        $control->setRealizada($valor);
        $entitymanager->flush();
    }

}