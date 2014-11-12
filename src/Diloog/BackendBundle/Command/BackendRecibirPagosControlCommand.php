<?php

namespace Diloog\BackendBundle\Command;

use Diloog\AfiliadoBundle\Entity\Afiliado;
use Diloog\BackendBundle\Entity\Operacion;
use Diloog\PagoBundle\Entity\Pago;
use Gaufrette\Adapter\Local;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Gaufrette\Filesystem;
use Gaufrette\Adapter\Sftp as SftpAdapter;

class BackendRecibirPagosControlCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('diloog:backend:recibirpagoscontrol')
            ->setDescription('Controla la recepcion de datos de pagos realizados en la entidad')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entitymanager = $this->getContainer()->get('doctrine')->getManager();
        $control = $entitymanager->getRepository('BackendBundle:ControlOperacion')->findOneBy(array('codigo' => 2));
        if($control->getRealizada==false){
        $sftp = $this->getContainer()->get('diloog_backend.sftp');
        $adapter = new SftpAdapter($sftp,'/pagos_entidad/');
        $filesystem = new Filesystem($adapter);

        $nombrearchivo = 'PagosEntidad.csv';
        if($filesystem->has($nombrearchivo)){
            $localadapter = new Local('../files/PagosEntidad/',true);
            $filesystem2 = new Filesystem($localadapter);
           $archivo =$filesystem->get('PagosEntidad.csv');
            $contenido =  $archivo->getContent();
            $fechaactual = new \DateTime('now');
            $fecha = trim($fechaactual->format('d-m-YH_i_s'));
            $nombrearchivo = 'PagosEntidad'.$fecha.'.csv';
            $filesystem2->createFile($nombrearchivo);
            $filesystem2->write($nombrearchivo, $contenido);
        }
        else{
            $descripcion = 'ERROR - Se produjo un error. No se ha encontrado el archivo de Pagos en Entidad en el servidor';
            $this->gestionarErrorOperacion($entitymanager, $descripcion);
            $this->controlOperacion($entitymanager,false);
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
          $objPHPExcel = $objReader->load('../files/PagosEntidad/'.$nombrearchivo);
          $objWorksheet = $objPHPExcel->getActiveSheet();
      }
      catch(\Exception $e){
          // $output->writeln('Excepcion capturada '."\n".$e->getMessage());
          $descripcion = 'ERROR - No se pudo cargar el archivo para su procesamiento';
          $this->gestionarErrorOperacion($entitymanager, $descripcion);
          $this->controlOperacion($entitymanager,false);
          $output->writeln('No se pudo realizar la operacion');
          return;
      }
        $cantidadpagos = 0;
        foreach ($objWorksheet->getRowIterator() as $row) {
            $row_index = $row->getRowIndex();
            if($row_index==1){
                $tipoarchivo = $objWorksheet->getCellByColumnAndRow(0,$row_index)->getValue();
                $cantidadpagos = $objWorksheet->getCellByColumnAndRow(1,$row_index)->getValue();
                $fechaarchivo = $objWorksheet->getCellByColumnAndRow(2,$row_index)->getValue();
                $output->writeln(array($tipoarchivo, $cantidadpagos, $fechaarchivo));

                if($tipoarchivo != 'Pagos Entidad'){
                    $descripcion = 'ERROR - El archivo recibido no es del tipo correcto';
                    $this->gestionarErrorOperacion($entitymanager, $descripcion);
                    $this->controlOperacion($entitymanager,false);
                    throw new \Exception('El archivo recibido no es correcto');
                    exit;
                }
            }
            else{
           $numerodeuda = $objWorksheet->getCellByColumnAndRow(0,$row_index)->getValue();
           $cantidadcuotas = $objWorksheet->getCellByColumnAndRow(1,$row_index)->getValue();
           $aniopago = $objWorksheet->getCellByColumnAndRow(2,$row_index)->getValue();
           $mespago = $objWorksheet->getCellByColumnAndRow(3,$row_index)->getValue();
           $diapago = $objWorksheet->getCellByColumnAndRow(4,$row_index)->getValue();
           $horapago = $objWorksheet->getCellByColumnAndRow(5,$row_index)->getValue();
           $minutopago = $objWorksheet->getCellByColumnAndRow(6,$row_index)->getValue();
           $this->pagarDeuda($entitymanager, $numerodeuda,$cantidadcuotas, $aniopago, $mespago, $diapago, $horapago, $minutopago);
          //  $output->writeln(array($numeroafiliado, $nombre, $apellido, $domicilio, $alias, $password, $salt, $email, $dni, $localidad));

            }
           }

        $operacion = new Operacion();
        $operacion->setFecha(new \DateTime('now'));
        $operacion->setTipo('Recepcion Pagos');
        $operacion->setDescripcion('Se han registrado exitosamente '.$cantidadpagos.' pagos');
        $entitymanager->persist($operacion);
        $entitymanager->flush();
        $this->controlOperacion($entitymanager,true);
        }
    }

    protected function pagarDeuda($entitymanager, $numerodeuda, $cantidadcuotas, $aniopago, $mespago, $diapago, $horapago, $minutopago){
        $deuda = $entitymanager->getRepository('PagoBundle:EstadoDeDeuda')->findOneBy(array('numeroDeuda'=>$numerodeuda));
        $deuda->setPagada(true);
        $pago = new Pago();
        $pago->setEstadoDeuda($deuda);
        $pago->setCantidadCuotas($cantidadcuotas);
        $pago->setProcesado(true);
        $pago->setNumeroSeguimiento($numerodeuda);
        $pago->setFechaPago(new \DateTime($aniopago.'-'.$mespago.'-'.$diapago.' '.$horapago.':'.$minutopago));
        $entitymanager->persist($pago);
    }

    protected function gestionarErrorOperacion($entitymanager ,$descripcion){
        $operacion = new Operacion();
        $operacion->setFecha(new \DateTime('now'));
        $operacion->setTipo('Recepcion Pagos');
        $operacion->setDescripcion($descripcion);
        $entitymanager->persist($operacion);
        $entitymanager->flush();
    }

    protected function controlOperacion($entitymanager, $valor){
        $control = $entitymanager->getRepository('BackendBundle:ControlOperacion')->findOneBy(array('codigo'=>2));
        $control->setRealizada($valor);
        $entitymanager->flush();
    }

}