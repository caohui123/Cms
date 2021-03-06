<?php

namespace Opifer\CmsBundle\Command;

use Doctrine\Common\Persistence\ManagerRegistry;
use Opifer\CmsBundle\Entity\Cron;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * This command is the main entry point for pending cronjobs.
 *
 * To add cronjobs, create your own symfony command and add that command
 * to the cronjobs page in the cms.
 *
 * @see  http://symfony.com/doc/current/cookbook/console/console_command.html
 */
class CronRunCommand extends ContainerAwareCommand
{
    /** @var string */
    private $env;

    /** @var ManagerRegistry */
    private $registry;

    /** @var OutputInterface */
    private $output;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('opifer:cron:run')
            ->setDescription('Runs all due cronjobs from the queue.');
    }

    /**
     * Execute.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->env = $input->getOption('env');
        $this->registry = $this->getContainer()->get('doctrine');
        $this->output = $output;

        $this->runCrons();
    }

    /**
     * Run jobs.
     */
    private function runCrons()
    {
        $due = $this->getRepository()->findDue();

        foreach ($due as $cron) {
            $this->startCron($cron);
        }

        $this->output->writeln('All done.');
    }

    /**
     * Start a cron.
     *
     * @param Cron $cron
     */
    private function startCron(Cron $cron)
    {
        $this->output->writeln(sprintf('Started %s.', $cron));
        $this->changeState($cron, Cron::STATE_RUNNING);

        $pb = $this->getCommandProcessBuilder();
        $pb->add($cron->getCommand());

        $process = $pb->getProcess();

        try {
            $process->mustRun();

            $this->output->writeln(' > '.$process->getOutput());

            if (!$process->isSuccessful() || !empty($process->getErrorOutput())) {
                $this->output->writeln(' > '.$process->getErrorOutput());
                $this->changeState($cron, Cron::STATE_CANCELED, $process->getErrorOutput());
            } else {
                $this->changeState($cron, Cron::STATE_FINISHED);
            }
        } catch (\Exception $e) {
            $this->output->writeln(' > '.$e->getMessage());
            $this->changeState($cron, Cron::STATE_CANCELED, $e->getMessage());
        }
    }

    /**
     * Change the state of the cron.
     *
     * @param Cron   $cron
     * @param string $state
     */
    private function changeState(Cron $cron, $state, $lastError = null)
    {
        $cron->setState($state);
        $cron->setLastError($lastError);

        $em = $this->getEntityManager();
        $em->persist($cron);
        $em->flush($cron);
    }

    /**
     * Get the process builder.
     *
     * @return \Symfony\Component\Process\ProcessBuilder
     */
    private function getCommandProcessBuilder()
    {
        $pb = new ProcessBuilder();

        // PHP wraps the process in "sh -c" by default, but we need to control
        // the process directly.
        if (!defined('PHP_WINDOWS_VERSION_MAJOR')) {
            $pb->add('exec');
        }

        $pb
            ->add('php')
            ->add($this->getContainer()->getParameter('kernel.root_dir').'/console')
            ->add('--env='.$this->env)
        ;

        return $pb;
    }

    /**
     * Get the entity manager.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    private function getEntityManager()
    {
        return $this->registry->getManagerForClass('OpiferCmsBundle:Cron');
    }

    /**
     * Get repository.
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getRepository()
    {
        return $this->getEntityManager()->getRepository('OpiferCmsBundle:Cron');
    }
}
