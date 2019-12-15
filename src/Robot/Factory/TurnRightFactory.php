<?php
declare(strict_types=1);

namespace CleaningRobot\Robot\Factory;

use CleaningRobot\Robot\Command\TurnRight;
use Psr\Log\LoggerInterface;

class TurnRightFactory
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function createTurnRightCommand(): TurnRight
    {
        return new TurnRight($this->logger);
    }
}
