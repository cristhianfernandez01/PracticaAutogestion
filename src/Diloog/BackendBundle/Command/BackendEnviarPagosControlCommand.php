<?php

namespace Diloog\BackendBundle\Command;

use Diloog\BackendBundle\Entity\Operacion;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Gaufrette\Filesystem;
use Gaufrette\Adapter\Sftp as SftpAdapter;
use Gaufrette\Adapter\Local;

class BackendEnviarPagosControlCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('diloog:backend:enviarpagoscontrol')
            ->setDescription('Control del envio por sftp los datos de pagos realizados en el sistema')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entitymanager = $this->getContainer()->get('doctrine')->getManager();
        $control = $entitymanager->getRepository('BackendBundle:ControlOperacion')->findOneBy(array('codigo' => 1));
        if($control->getRealizada==false){
        $sftp = $this->getContainer()->get('diloog_backend.sftp');
        $adapter = new SftpAdapter($sftp,'/pagos/');
        $filesystem = new Filesystem($adapter);
        $localadapter = new Local('../files/Pagos/',true);
        $filesystem2 = new Filesystem($localadapter);
        $pagos = $entitymanager->getRepository('PagoBundle:Pago')->findPagosSinProcesar();
        if($pagos==null ){
            $output->write('No se encontraron datos para enviar');
            $descripcion = 'ERROR - No se han encontrado pagos para su envio';
            $this->gestionarErrorOperacion($entitymanager, $descripcion);
            $this->controlOperacion($entitymanager,false);
        }
        else{
        $fechaactual = new \DateTime('now');
        $fecha = trim($fechaactual->format('d-m-YH_i_s'));
        $nombrearchivo = 'Pagos'.$fecha.'.csv';
        //$filesystem2->createFile($nombrearchivo);
        $archivo = fopen('../files/Pagos/'.$nombrearchivo ,'x+');
        $factory= $this->getContainer()->get('phpexcel');
        //\PHPExcel_IOFactory::load('Archivo.csv');
        try{
            $phpexcel = $factory->createPHPExcelObject('../files/Pagos/'.$nombrearchivo);
            $phpexcel->setActiveSheetIndex(0);
        }
        catch(\Exception $e){
            $descripcion = 'ERROR - Se produjo un error al generar el archivo para enviar';
            $this->gestionarErrorOperacion($entitymanager, $descripcion);
            $this->controlOperacion($entitymanager,false);
            return;
        }

        $activesheet= $phpexcel->getActiveSheet();



            $i = 2;
        foreach($pagos as $pago){
            $activesheet->setCellValueByColumnAndRow(0,$i,$pago->getEstadoDeuda()->getNumeroDeuda());
            $activesheet->setCellValueByColumnAndRow(1,$i,$pago->getNumeroSeguimiento());
            $activesheet->setCellValueByColumnAndRow(2,$i,$pago->getFechaPago()->format('dmyHis'));
            $activesheet->setCellValueByColumnAndRow(3,$i,$pago->getCantidadCuotas());
            $cell = $activesheet->getCellByColumnAndRow(0,$i);
            $cell->getValue();
            $pago->setProcesado(true);
            $i ++;
        }
            $activesheet->setCellValueByColumnAndRow(0,1,'Envio Pagos');
            $activesheet->setCellValueByColumnAndRow(1,1,$fechaactual->format('Y-m-d'));
            $activesheet->setCellValueByColumnAndRow(2,1,$fechaactual->format('h:i'));
            $activesheet->setCellValueByColumnAndRow(3,1,($i-2));
        $entitymanager->flush();

        // $writer = $factory->createWriter($phpexcel,'CSV');
        $writer = $this->getContainer()->get('phpexcel')->createWriter($phpexcel, 'CSV')
            ->setDelimiter(',')
            ->setEnclosure('')
            ->setLineEnding("\r\n")
            ->setSheetIndex(0)
            ->save('../files/Pagos/'.$nombrearchivo);
            $filesystem->write($nombrearchivo, $filesystem2->get($nombrearchivo)->getContent());
           // $filesystem->write($nombrearchivo,file_get_contents('../files/Pagos/'.$nombrearchivo));

            $operacion = new Operacion();
            $operacion->setFecha(new \DateTime('now'));
            $operacion->setTipo('Envio Pagos');
            $operacion->setDescripcion('Se ha(n) enviado con exito '.($i-2).' pago(s)');
            $entitymanager->persist($operacion);
            $entitymanager->flush();
            $this->controlOperacion($entitymanager,true);

        }
      }
    }

    protected function gestionarErrorOperacion($entitymanager ,$descripcion){
        $operacion = new Operacion();
        $operacion->setFecha(new \DateTime('now'));
        $operacion->setTipo('Envio Pagos');
        $operacion->setDescripcion($descripcion);
        $entitymanager->persist($operacion);
        $entitymanager->flush();
    }

    protected function controlOperacion($entitymanager, $valor){
        $control = $entitymanager->getRepository('BackendBundle:ControlOperacion')->findOneBy(array('codigo'=>1));
        $control->setRealizada($valor);
        $entitymanager->flush();
    }
}