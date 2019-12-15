<?php
declare(strict_types=1);

namespace CleaningRobot\Robot\Factory;

use CleaningRobot\Robot\Command\Advance;
use Psr\Log\LoggerInterface;

class AdvanceFactory
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function createAdvanceCommand(): Advance
    {
        return new Advance($this->logger);
    }
}
