<?php

namespace ZloeSabo\CacheCleanerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface
    ;
use Symfony\Component\Filesystem\Filesystem;

class CacheAutoCleanCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cache:autoclean')
            ->setDescription('Perform automatic cache cleanup')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if(!extension_loaded('inotify')) {
            $output->writeln('<error>Inotify extension is not loaded. Running autoclean can cause performance issues.</error>');
            $output->writeln('<error>Cleanup will be unreliable</error>');
        }

        $cleaner = $this->getContainer()->get('cache_cleaner');

        $symfonyRoot = implode(DIRECTORY_SEPARATOR, array(
            $this->getContainer()->getParameter('kernel.root_dir'),
            '..'
        ));

        $configDir = implode(DIRECTORY_SEPARATOR, array(
            $symfonyRoot,
            'app',
            'config'
        ));

        $bundlesDir = implode(DIRECTORY_SEPARATOR, array(
            $symfonyRoot,
            'src'
        ));

        $cleaner->addSubjectForObserver($configDir);
        $cleaner->addSubjectForObserver($bundlesDir);

        $fs = new Filesystem();

        $output->writeln('Watching for changes in:');
        $output->writeln(sprintf("\t%s",
            $fs->makePathRelative($configDir, $symfonyRoot)
        ));
        $output->writeln(sprintf("\t%s",
            $fs->makePathRelative($bundlesDir, $symfonyRoot)
        ));

        $cleaner->start();
    }
} 