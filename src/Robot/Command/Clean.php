<?php
declare(strict_types=1);

namespace CleaningRobot\Robot\Command;

use CleaningRobot\Area\Area;
use CleaningRobot\Robot\Robot;
use Psr\Log\LoggerInterface;

class Clean implements Command
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function execute(Robot $robot, Area $area): void
    {
        $robot->addCleanedCell($robot->getCurrentPosition());

        $this->logger->info(
            sprintf(
                'Robot cleaned cell "x: %d", "y: %d"',
                $robot->getCurrentPosition()->getX(),
                $robot->getCurrentPosition()->getY()
            )
        );
    }

    public function getBatteryConsumption(): int
    {
        return 5;
    }
}
