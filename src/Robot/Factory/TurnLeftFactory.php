<?php
declare(strict_types=1);

namespace CleaningRobot\Robot\Factory;

use CleaningRobot\Robot\Command\TurnLeft;
use Psr\Log\LoggerInterface;

class TurnLeftFactory
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function createTurnLeftCommand(): TurnLeft
    {
        return new TurnLeft($this->logger);
    }
}
