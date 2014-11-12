<?php

namespace ZloeSabo\CacheCleanerBundle\ModificationObserver;

use Psr\Log\LoggerInterface;
use Symfony\Component\Finder\Finder;

class FinderObserver implements ModificationObserverInterface
{
    private $subjects = array();
    private $checkdate;
    private $logger;

    public function __construct(array $subjects, LoggerInterface $logger)
    {
        $this->subjects = $subjects;
        $this->logger = $logger;

        $this->checkdate = date('c');
    }

    public function observe()
    {

        //TODO detect removal
        while(true) {

            foreach ($this->subjects as $subject) {
                $finder = new Finder();
                $finder->useBestAdapter();

                $changedCount = $finder
                        ->in($subject)
                        ->date(sprintf('> %s', $this->checkdate))
//                        ->name('/^[^\.]*/')
//                        ->name('*.php')
//                        ->name('*.yml')
//                        ->name('*.xml')
//                        ->name('*.twig')
                    ->count();

                if ($changedCount) {
                    $this->logger->info(sprintf('Detected changes in %s', $subject));

                    $this->checkdate = date('c');

                    if(is_callable($this->callback)) {
                        call_user_func($this->callback);
                    }
                }
            }

            usleep(1000000);
        }
    }

    public function onChangeDetected($callback)
    {
        $this->callback = $callback;

        return $this;
    }
} 