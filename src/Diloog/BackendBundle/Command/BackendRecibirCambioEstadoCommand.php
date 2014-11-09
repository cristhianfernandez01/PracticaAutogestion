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
        $adapter = new SftpAdapter($sftp,'/estados/');
        $filesystem = new Filesystem($adapter);
        $entitymanager = $this->getContainer()->get('doctrine')->getManager();
        $localadapter = new Local('../files/Estados/',true);
        $filesystem2 = new Filesystem($localadapter);
        $nombrearchivo1 = 'Estado.csv';

        if($filesystem->has($nombrearchivo1)){
           $archivo =$filesystem->get($nombrearchivo1);
          $contenido =  $archivo->getContent();
            $fechaactual = new \DateTime('now');
            $fecha = trim($fechaactual->format('d-m-YH_i_s'));
            $nombrearchivo = 'Estados'.$fecha.'.csv';
            $filesystem2->createFile($nombrearchivo);
            $filesystem2->write($nombrearchivo, $contenido);
        }
        else{
            $descripcion = 'ERROR - Se produjo un error. No se ha encontrado el archivo de Estados en el servidor';
            $this->gestionarErrorOperacion($entitymanager, $descripcion);
            throw new \Exception('No se ha encontrado el archivo en el servidor');
            exit;
        }

        //$archivo = fopen('../files/'.$nombrearchivo ,'x+');
       // $factory= $this->getContainer()->get('phpexcel');
       //$objReader = \PHPExcel_IOFactory::createReader('CSV');
        $objReader = new \PHPExcel_Reader_CSV();
        $objReader->setDelimiter(',');
        $objReader->setEnclosure('');
        $objReader->setLineEnding('\r\n');

        try {
        $objPHPExcel = $objReader->load('../files/Estados/'.$nombrearchivo);
        $objWorksheet = $objPHPExcel->getActiveSheet();
        }
        catch(\Exception $e){
          // $output->writeln('Excepcion capturada '."\n".$e->getMessage());
            $descripcion = 'ERROR - No se pudo cargar el archivo para su procesamiento';
            $this->gestionarErrorOperacion($entitymanager, $descripcion);
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
                if($tipoarchivo != 'Cambio Estado'){
                    $descripcion = 'ERROR - El archivo recibido no es del tipo correcto';
                    $this->gestionarErrorOperacion($entitymanager, $descripcion);
                    throw new \Exception('El archivo recibido no es correcto');
                    exit;
                }
            }
            else{
           $numeroafiliado = $objWorksheet->getCellByColumnAndRow(0,$row_index)->getValue();
           $nuevoestado = $objWorksheet->getCellByColumnAndRow(1,$row_index)->getValue();
          $output->writeln(array($numeroafiliado, $nuevoestado));
           $this->cambiarEstado($entitymanager, $numeroafiliado,$nuevoestado);

            }
           }
        $operacion = new Operacion();
        $operacion->setFecha(new \DateTime('now'));
        $operacion->setTipo('Cambio Estado');
        $operacion->setDescripcion('Se han modificado exitosamente los estados de '.$cantidadafiliados.' afiliados');
        $entitymanager->persist($operacion);
        $entitymanager->flush();
        $output->writeln('Se han modificado exitosamente los estados de '.$cantidadafiliados.' afiliados');
    }

    protected function cambiarEstado($entitymanager, $numeroafiliado, $nuevoestado){
        $estado = $entitymanager->getRepository('AfiliadoBundle:Estado')->findOneBy(array('nombre'=>$nuevoestado));
        $afiliado = $entitymanager->getRepository('AfiliadoBundle:Afiliado')->findOneBy(array('numeroAfiliado'=>$numeroafiliado));
        $afiliado->setEstado($estado);
        $entitymanager->persist($afiliado);
    }

    protected function gestionarErrorOperacion($entitymanager ,$descripcion){
        $operacion = new Operacion();
        $operacion->setFecha(new \DateTime('now'));
        $operacion->setTipo('Cambio Estado');
        $operacion->setDescripcion($descripcion);
        $entitymanager->persist($operacion);
        $entitymanager->flush();
    }

}