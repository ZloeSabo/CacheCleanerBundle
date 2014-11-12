<?php

namespace ZloeSabo\CacheCleanerBundle\ModificationObserver;

use Psr\Log\LoggerInterface;

class ObserverFactory
{
    /**
     * @param $subjects
     * @param LoggerInterface $logger
     * @return ModificationObserverInterface
     */
    public static function createObserver($subjects, LoggerInterface $logger)
    {
        if(!extension_loaded('inotify')) {
            $logger->info(sprintf(
                'Inotify extension is available, can use InotifyObserver'
            ));

            return new InotifyObserver($subjects, $logger);
        }

        $logger->info(sprintf(
            'Inotify extension is not available, will use FinderObserver'
        ));

        return new FinderObserver($subjects, $logger);
    }
} 