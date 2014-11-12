<?php

namespace ZloeSabo\CacheCleanerBundle\ModificationObserver;

use Psr\Log\LoggerInterface;

class InotifyObserver implements ModificationObserverInterface
{
    private $inotify;
    private $watches = array();
    private $logger;
    private $callback;

    private $actions = array(
        IN_MODIFY => 'modified in observed',
        IN_ATTRIB => 'attribute changed in observed',
        IN_MOVED_FROM => 'moved from observed',
        IN_MOVED_TO => 'moved to observed',
        IN_CREATE => 'created in observed',
        IN_DELETE => 'deleted in observed'
    );

    public function __construct(array $subjects, LoggerInterface $logger)
    {
        if(!extension_loaded('inotify')) {
            throw new \RuntimeException('No inotify extension loaded for Inotify Observer');
        }

        $this->logger = $logger;
        $this->inotify = inotify_init();
        $observer = $this;

        foreach($subjects as $subject) {
            $this->watches[$subject] = inotify_add_watch($this->inotify, $subject, $this->getObserveMask());
            $logger->info(sprintf(
                'Added %s to watch list',
                $subject
            ));
        }

        register_shutdown_function(function() use ($observer) {
            $observer->shutdown();
        });
    }

    private function shutdown()
    {
        if(!empty($this->watches)) {
            foreach($this->watches as $subject => $watch) {
                inotify_rm_watch($this->inotify, $watch);
                $this->logger->info(sprintf(
                    'Removed %s from watch list',
                    $subject
                ));
            }
        }

        fclose($this->inotify);
    }

    public function observe()
    {
        while(true) {
            $events = inotify_read($this->inotify);

            foreach($events as $event) {
                $eventType = $event['mask'];

                $this->logger->info(sprintf(
                    'Detected %s %s',
                    $event['name'],
                    $this->actions[$eventType]
                ));
            }

            if(is_callable($this->callback)) {
                call_user_func($this->callback);
            }
        }
    }

    public function onChangeDetected($callback)
    {
        $this->callback = $callback;

        return $this;
    }

    private function getObserveMask()
    {
        return IN_MODIFY | IN_ATTRIB | IN_MOVE | IN_CREATE | IN_DELETE;
    }

} 