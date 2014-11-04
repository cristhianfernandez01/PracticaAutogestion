<?php

namespace Diloog\BackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Gaufrette\Filesystem;
use Gaufrette\Adapter\Sftp as SftpAdapter;
use Zend\Stdlib\DateTime;

class BackendEnviarPagosCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('diloog:backend:enviarpagos')
            ->setDescription('Envia por sftp los datos de pagos realizados en el sistema')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sftp = $this->getContainer()->get('diloog_backend.sftp');
        $adapter = new SftpAdapter($sftp,'/pagos/');
        $filesystem = new Filesystem($adapter);
        $entitymanager = $this->getContainer()->get('doctrine')->getManager();
        $pagos = $entitymanager->getRepository('PagoBundle:Pago')->findPagosSinProcesar();
        if($pagos[0]==null ){
            $output->write('No se encontraron datos para enviar');
        }
        else{
        $fechaactual = new \DateTime('now');
        $fecha = trim($fechaactual->format('d-m-YH_i_s'));
        $nombrearchivo = 'Pagos'.$fecha.'.csv';
        $archivo = fopen('../files/'.$nombrearchivo ,'x+');
        $factory= $this->getContainer()->get('phpexcel');
        //\PHPExcel_IOFactory::load('Archivo.csv');
        $phpexcel = $factory->createPHPExcelObject('../files/'.$nombrearchivo);
        $phpexcel->setActiveSheetIndex(0);
        $activesheet= $phpexcel->getActiveSheet();
        $i = 1;
        foreach($pagos as $pago){
            $activesheet->setCellValueByColumnAndRow(0,$i,$pago->getEstadoDeuda()->getNumeroDeuda());
            $activesheet->setCellValueByColumnAndRow(1,$i,$pago->getNumeroSeguimiento());
            $activesheet->setCellValueByColumnAndRow(2,$i,$pago->getFechaPago()->format('dmyHis'));
            $activesheet->setCellValueByColumnAndRow(3,$i,$pago->getCantidadCuotas());
            $pago->setProcesado(true);
            $i ++;
        }

        $entitymanager->flush();

        // $writer = $factory->createWriter($phpexcel,'CSV');
        $writer = $this->getContainer()->get('phpexcel')->createWriter($phpexcel, 'CSV')
            ->setDelimiter(',')
            ->setEnclosure('')
            ->setLineEnding("\r\n")
            ->setSheetIndex(0)
            ->save('../files/'.$nombrearchivo);
        $filesystem->write($nombrearchivo,file_get_contents('../files/'.$nombrearchivo));
        }
    }
}