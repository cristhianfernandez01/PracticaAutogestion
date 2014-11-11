<?php

namespace Diloog\BackendBundle\Command;

use Diloog\BackendBundle\Entity\Operacion;
use Diloog\PagoBundle\Entity\DetalleDeuda;
use Diloog\PagoBundle\Entity\EstadoDeDeuda;
use Diloog\PagoBundle\Entity\SubdetalleDeuda;
use Gaufrette\Adapter\Local;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Gaufrette\Filesystem;
use Gaufrette\Adapter\Sftp as SftpAdapter;

class BackendRecibirDeudasCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('diloog:backend:recibirdeudas')
            ->setDescription('Recibe actualizaciones de Estados de Deuda mensualmente para todos los afiliados')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sftp = $this->getContainer()->get('diloog_backend.sftp');
        $adapter = new SftpAdapter($sftp,'/deudas/');
        $filesystem = new Filesystem($adapter);
        $entitymanager = $this->getContainer()->get('doctrine')->getManager();
        $this->desacivarDeudas($entitymanager);
        $nombrearchivo = 'DeudasActualizadas.csv';
        if($filesystem->has($nombrearchivo)){
            $localadapter = new Local('../files/Deudas/',true);
            $filesystem2 = new Filesystem($localadapter);
           $archivo =$filesystem->get($nombrearchivo);
          $contenido =  $archivo->getContent();
            $fechaactual = new \DateTime('now');
            $fecha = trim($fechaactual->format('d-m-YH_i_s'));
            $nombrearchivo = 'DeudasActualizadas'.$fecha.'.csv';
            $filesystem2->createFile($nombrearchivo);
            $filesystem2->write($nombrearchivo, $contenido);
        }
        else{
            $descripcion = 'ERROR - Se produjo un error. No se ha encontrado el archivo de Deudas en el servidor';
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
          $objPHPExcel = $objReader->load('../files/Deudas/'.$nombrearchivo);
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
        $auxiliar1 = 0;
        $auxiliar2= 0;
        $deuda = new EstadoDeDeuda();
        $detalledeuda = new DetalleDeuda();
        $subdetalledeuda = new SubdetalleDeuda();
        foreach ($objWorksheet->getRowIterator() as $row) {
            $row_index = $row->getRowIndex();
            $fechaarchivo = '';
            if($row_index==1){
                $tipoarchivo = $objWorksheet->getCellByColumnAndRow(0,$row_index)->getValue();
                $fechaarchivo = $objWorksheet->getCellByColumnAndRow(1,$row_index)->getValue();
                $cantidadafiliados = $objWorksheet->getCellByColumnAndRow(2,$row_index)->getValue();
                $output->writeln(array($tipoarchivo, $cantidadafiliados, $fechaarchivo));

                if($tipoarchivo != 'Deudas Actualizadas'){
                    $descripcion = 'ERROR - El archivo recibido no es del tipo correcto';
                    $this->gestionarErrorOperacion($entitymanager, $descripcion);
                    $this->controlOperacion($entitymanager, false);
                    throw new \Exception('El archivo recibido no es correcto');
                    exit;
                }
            }
            else{

                if($auxiliar2 == $objWorksheet->getCellByColumnAndRow(0,$row_index)->getValue()){
                    $descripcionsubdetalle = $objWorksheet->getCellByColumnAndRow(1,$row_index)->getValue();
                    $importesubdetalle = $objWorksheet->getCellByColumnAndRow(2,$row_index)->getValue();
                    $vencimientosubdetalle = $objWorksheet->getCellByColumnAndRow(3,$row_index)->getValue();
                    $output->writeln(array($descripcionsubdetalle, $importesubdetalle, $vencimientosubdetalle));
                  $subdetalledeuda = $this->generarSubdetalle($entitymanager, $detalledeuda, $descripcionsubdetalle, $importesubdetalle, $vencimientosubdetalle);
                   $entitymanager->persist($subdetalledeuda);
                  //  $entitymanager->flush();
                   $detalledeuda->addSubdetalledeuda($subdetalledeuda);
                }
                    elseif($auxiliar1 == $objWorksheet->getCellByColumnAndRow(0,$row_index)->getValue()){
                        $codigodetalle = $objWorksheet->getCellByColumnAndRow(1,$row_index)->getValue();
                        $descripciondetalle = $objWorksheet->getCellByColumnAndRow(2,$row_index)->getValue();
                        $subtotaldetalle = $objWorksheet->getCellByColumnAndRow(3,$row_index)->getValue();
                        $output->writeln(array($codigodetalle, $descripciondetalle, $subtotaldetalle));
                        $auxiliar2 = $codigodetalle;
                      $detalledeuda =  $this->generarDetalle($entitymanager, $codigodetalle, $deuda, $descripciondetalle, $subtotaldetalle);
                       $entitymanager->persist($detalledeuda);
                      //  $entitymanager->flush();
                        $deuda->addDetalleDeuda($detalledeuda);
                    }
                else{
                    $numeroafiliado = $objWorksheet->getCellByColumnAndRow(0,$row_index)->getValue();
                    $numerodeuda = $objWorksheet->getCellByColumnAndRow(1,$row_index)->getValue();
                    $importetotal = $objWorksheet->getCellByColumnAndRow(2,$row_index)->getValue();
                    $output->writeln(array($numeroafiliado, $numerodeuda, $importetotal));
                    $auxiliar1 = $numerodeuda;
                    $deuda = $this->generarDeuda($entitymanager, $numeroafiliado, $numerodeuda, $importetotal, $fechaarchivo);
                    //$entitymanager->flush();
                }

          //  $output->writeln(array($numeroafiliado, $nombre, $apellido, $domicilio, $alias, $password, $salt, $email, $dni, $localidad));
           // $this->guardarAfiliado($entitymanager, $numeroafiliado, $nombre, $apellido, $domicilio, $alias, $password, $salt, $email, $dni, $localidad);

                $entitymanager->flush();
            }

           }
        $operacion = new Operacion();
        $operacion->setFecha(new \DateTime('now'));
        $operacion->setTipo('Actualizacion Deudas');
        $operacion->setDescripcion('Se han actualizado exitosamente los datos de Estados de Deudas de '.$cantidadafiliados.' afiliados');
        $entitymanager->persist($operacion);
        $entitymanager->flush();
        $this->controlOperacion($entitymanager, true);
    }

    protected function generarDeuda($entitymanager, $numeroafiliado, $numerodeuda, $importetotal, $fechaarchivo){
        $afiliado = $entitymanager->getRepository('AfiliadoBundle:Afiliado')->findOneBy(array('numeroAfiliado'=>$numeroafiliado));
        $deuda = new EstadoDeDeuda();
        $deuda->setAfiliado($afiliado);
        $deuda->setNumeroDeuda($numerodeuda);
        $deuda->setActiva(true);
        $deuda->setFechaEmision(new \DateTime($fechaarchivo));
        $deuda->setPagada(false);
        $deuda->setImporteTotal($importetotal);
        return $deuda;
    }

    protected function generarDetalle($entitymanager, $codigodetalle, $deuda, $decripciondetalle, $subtotaldetalle ){
        $detalle = new DetalleDeuda();
        $detalle->setEstadoDeuda($deuda);
        $detalle->setCodigo($codigodetalle);
        $detalle->setConcepto($decripciondetalle);
        $detalle->setSubtotal($subtotaldetalle);
      return $detalle;
    }

    protected function generarSubdetalle($entitymanager,$detalle, $descripcionsubdetalle, $importesubdetalle, $vencimientosubdetalle){
        $subdetalle = new SubdetalleDeuda();
        $subdetalle->setDetalleDeuda($detalle);
        $subdetalle->setImporte($importesubdetalle);
        $subdetalle->setSubconcepto($descripcionsubdetalle);
        $subdetalle->setFechaVencimiento(new \DateTime($vencimientosubdetalle));
        return $subdetalle;
    }

    protected function desacivarDeudas($entitymanager){
       $deudas = $entitymanager->getRepository('PagoBundle:EstadoDeDeuda')->findAll();
        foreach($deudas as $deuda){
            $deuda->setActiva(false);
            $entitymanager->persist($deuda);
        }
        $entitymanager->flush();
    }

    protected function gestionarErrorOperacion($entitymanager ,$descripcion){
        $operacion = new Operacion();
        $operacion->setFecha(new \DateTime('now'));
        $operacion->setTipo('Actualizacion Deudas');
        $operacion->setDescripcion($descripcion);
        $entitymanager->persist($operacion);
        $entitymanager->flush();
    }

    protected function controlOperacion($entitymanager, $valor){
        $control = $entitymanager->getRepository('BackendBundle:ControlOperacion')->findOneBy(array('codigo'=>3));
        $control->setRealizada($valor);
        $entitymanager->flush();
    }
}