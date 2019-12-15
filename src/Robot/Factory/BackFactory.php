<?php
declare(strict_types=1);

namespace CleaningRobot\Robot\Factory;

use CleaningRobot\Robot\Command\Back;
use Psr\Log\LoggerInterface;

class BackFactory
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function createBackCommand(): Back
    {
        return new Back($this->logger);
    }
}
