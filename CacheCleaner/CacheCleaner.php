<?php

namespace ZloeSabo\CacheCleanerBundle\CacheCleaner;

use ZloeSabo\CacheCleanerBundle\ModificationObserver\ObserverFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class CacheCleaner
{
    private $logger;
    private $cachedir;
    private $directories = array();

    public function __construct(LoggerInterface $logger, $cachedir)
    {
        $this->logger = $logger;
        $this->cachedir = $cachedir;
    }

    public function addSubjectForObserver($subject)
    {
        //TODO check if added subject intersects other watches. I.e. adding file in directory which is already being watched
        if(file_exists($subject)) {
            $this->directories[] = $subject;
        } else {
            $this->logger->error(sprintf('Subject of observation does not exist: %s', $subject));
        }
    }

    public function start()
    {
        if(empty($this->directories)) {
            $this->logger->error('Observation list is empty, exiting');

            return;
        }

        $observer = ObserverFactory::createObserver($this->directories, $this->logger);

        $observer->onChangeDetected(function() {
            $this->performCleanup();
        });

        $observer->observe();
    }

    private function performCleanup()
    {
        $fs = new Filesystem();
        $finder = new Finder();

        //TODO optionaly ignore session dir
        foreach($finder->in($this->cachedir)->depth('== 0')->exclude('sessions') as $subject) {
            $this->logger->info(sprintf('Removing cache: %s', $subject->getRealpath()));
            $fs->remove($subject->getRealpath());
        }
    }
} 