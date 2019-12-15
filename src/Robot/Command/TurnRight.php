<?php
declare(strict_types=1);

namespace CleaningRobot\Robot\Command;

use CleaningRobot\Area\Area;
use CleaningRobot\Direction;
use CleaningRobot\Robot\Robot;
use Psr\Log\LoggerInterface;

class TurnRight implements Command
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function execute(Robot $robot, Area $area): void
    {
        $direction = $robot->getFacing();

        $robot->setFacing(Direction::fromAngle($direction->toAngle() + 90));

        $this->logger->info(
            sprintf('Robot has turned to right and now he is facing "%s"', $robot->getFacing()->getDirection())
        );
    }

    public function getBatteryConsumption(): int
    {
        return 1;
    }
}
