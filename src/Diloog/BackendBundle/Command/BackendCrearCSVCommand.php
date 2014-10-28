<?php

namespace Diloog\BackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Gaufrette\Filesystem;
use Gaufrette\Adapter\Sftp as SftpAdapter;

class BackendCrearCSVCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('diloog:backend:crearcsv')
            ->setDescription('Envia por sftp archivos')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sftp = $this->getContainer()->get('diloog_backend.sftp');
        $adapter = new SftpAdapter($sftp,'/pagos/');
        $filesystem = new Filesystem($adapter);
        $file = fopen('Archivo3.csv','a');
        $factory= $this->getContainer()->get('phpexcel');
        //\PHPExcel_IOFactory::load('Archivo.csv');
        $phpexcel = $factory->createPHPExcelObject('Archivo3.csv');
        $activesheet= $phpexcel->getActiveSheet();
        $activesheet->setCellValueByColumnAndRow(0,1,'Primera');
        $activesheet->setCellValueByColumnAndRow(1,1,'Segunda');
        $activesheet->setCellValueByColumnAndRow(2,1,'Tercera');
        $activesheet->setCellValueByColumnAndRow(0,2,'Primera');
        $activesheet->setCellValueByColumnAndRow(1,2,'Segunda');
        $activesheet->setCellValueByColumnAndRow(2,2,'Tercera');
        // $writer = $factory->createWriter($phpexcel,'CSV');
        $writer = $this->getContainer()->get('phpexcel')->createWriter($phpexcel, 'CSV')
            ->setDelimiter(',')
            ->setEnclosure('')
            ->setLineEnding("\r\n")
            ->setSheetIndex(0)
            ->save('../files/Archivo3.csv');
        $filesystem->write('Archivo.csv',file_get_contents('../files/Archivo3.csv'));
    }
}