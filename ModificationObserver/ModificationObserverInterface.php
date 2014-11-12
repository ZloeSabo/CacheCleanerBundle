<?php

namespace ZloeSabo\CacheCleanerBundle\ModificationObserver;

use Psr\Log\LoggerInterface;

interface ModificationObserverInterface
{
    public function __construct(array $subjects, LoggerInterface $logger);
    public function observe();
    public function onChangeDetected($callback);
} 