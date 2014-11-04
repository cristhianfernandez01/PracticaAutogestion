<?php

namespace Diloog\BackendBundle\Command;

use Gaufrette\Adapter\Local;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Gaufrette\Filesystem;
use Gaufrette\Adapter\Sftp as SftpAdapter;

class BackendRecibirAfiliadosCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('diloog:backend:recibirafiliados')
            ->setDescription('Recibe datos de nuevos afiliados registrados')
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
            $localadapter = new Local('../files/',true);
            $filesystem2 = new Filesystem($localadapter);
           $archivo =$filesystem->get('Afiliados.csv');
          $contenido =  $archivo->getContent();
        }
        //$archivo = fopen('../files/'.$nombrearchivo ,'x+');
        $factory= $this->getContainer()->get('phpexcel');
       $objReader = \PHPExcel_IOFactory::createReader('CSV');
        $objReader->setDelimiter("\t");
        $objReader->setEnclosure('');
        $objPHPExcel = $objReader->load('../files/');
        $objWorksheet = $objPHPExcel->getActiveSheet();
        foreach ($objWorksheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            foreach ($cellIterator as $cell) {
                $cell_value = $cell->getValue();
            }
        }

/*
        $phpexcel = $factory->createPHPExcelObject('../files/'.$nombrearchivo);
        $phpexcel->setActiveSheetIndex(0);
        $activesheet= $phpexcel->getActiveSheet();

        $activesheet->setCellValueByColumnAndRow(0,'');
*/


        $entitymanager->flush();
/*
        // $writer = $factory->createWriter($phpexcel,'CSV');
        $writer = $this->getContainer()->get('phpexcel')->createWriter($phpexcel, 'CSV')
            ->setDelimiter(',')
            ->setEnclosure('')
            ->setLineEnding("\r\n")
            ->setSheetIndex(0)
            ->save('../files/'.$nombrearchivo);
        $filesystem->write($nombrearchivo,file_get_contents('../files/'.$nombrearchivo));
*/
    }

}