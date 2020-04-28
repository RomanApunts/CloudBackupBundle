<?php
namespace Dizda\CloudBackupBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Dizda\CloudBackupBundle\Manager\BackupManager;

/**
 * Run backup command.
 *
 * @author Jonathan Dizdarevic <dizda@dizda.fr>
 * @author István Manzuk <istvan.manzuk@gmail.com>
 */
class BackupCommand extends Command
{
    
    private $container;
    
     public function __construct(BackupManager $manager, ContainerInterface $container){

        $this->container    = $container;
        $this->manager      = $manager;
        parent::__construct();
        
    }
    
    /**
     * Configure the command.
     */
    //protected static $defaultName = 'dizda:backup:start';
    
    protected function configure()
    {
        $this
            ->setName('dizda:backup:start')
            ->setDescription('Upload a backup of your database to your cloud services.');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->checkEnvironment($output);

        if (!$this->getContainer()->get('dizda.cloudbackup.manager.backup')->execute()) {
        //if(!$this->manager->execute()){
            $output->writeln('<error>Something went terribly wrong. We could not create a backup. Read your log files to see what caused this error.</error>');

            return 1; //error
        }

        $output->writeln('<info>Backup complete.</info>');
        return 0;
    }

    /**
     * Print a warning if we do not run the command in production environment
     *
     * @param OutputInterface $output
     */
    protected function checkEnvironment(OutputInterface $output)
    {
        if ($this->container->get('kernel')->getEnvironment() !== 'prod') {
            $output->writeln('<bg=yellow>                                                                            </bg=yellow>');
            $output->writeln('<bg=yellow;options=bold;fg=black>  Warning:                                                                  </bg=yellow;options=bold;fg=black>');
            $output->writeln('<bg=yellow;fg=black>  You should run the command in production environment ("--env=prod")       </bg=yellow;fg=black>');
            $output->writeln('<bg=yellow>                                                                            </bg=yellow>');
        }
    }
}
