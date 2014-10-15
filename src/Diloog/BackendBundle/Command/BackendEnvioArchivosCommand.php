<?php

namespace Diloog\BackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Gaufrette\Filesystem;
use Gaufrette\Adapter\Sftp as SftpAdapter;

class BackendEnvioArchivosCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('diloog:backend:envioarchivos')
            ->setDescription('Envia por sftp archivos')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sftp = $this->getContainer()->get('diloog_backend.sftp');
        $adapter = new SftpAdapter($sftp,'/pagos/');
        $filesystem = new Filesystem($adapter);
        //ld($filesystem);
        $content = 'Hello I am the new content 2';
        $filesystem->write('archivo2.txt', $content);
    }
}