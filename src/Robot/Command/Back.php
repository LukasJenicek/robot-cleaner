<?php
declare(strict_types=1);

namespace CleaningRobot\Robot\Command;

use CleaningRobot\Area\Area;
use CleaningRobot\Coordinate;
use CleaningRobot\Direction;
use CleaningRobot\Robot\Exception\RobotStuckException;
use CleaningRobot\Robot\Robot;
use Psr\Log\LoggerInterface;

class Back implements Command
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function execute(Robot $robot, Area $area): void
    {
        $targetPosition = $this->getTargetedPosition($robot->getCurrentPosition(), $robot->getFacing());

        $this->logger->info(
            sprintf(
                'Robot is backing up to new position "x: %d", "y: %d"',
                $targetPosition->getX(),
                $targetPosition->getY()
            )
        );

        if ($area->canCellBeOccupied($targetPosition) === false) {
            throw new RobotStuckException('Robot could not go back, there is obstacle');
        }

        $robot->moveToPosition($targetPosition);
        $robot->addVisitedCell($targetPosition);
        $robot->setState(Robot::AVAILABLE);

        $this->logger->info('Robot successfully backed up');
    }

    public function getBatteryConsumption(): int
    {
        return 3;
    }

    private function getTargetedPosition(Coordinate $currentPosition, Direction $direction): Coordinate
    {
        switch ($direction->getDirection()) {
            case Direction::NORTH:
                return new Coordinate($currentPosition->getX(), $currentPosition->getY() + 1);
                break;
            case Direction::EAST:
                return new Coordinate($currentPosition->getX() - 1, $currentPosition->getY());
                break;
            case Direction::SOUTH:
                return new Coordinate($currentPosition->getX(), $currentPosition->getY() - 1);
                break;
            case Direction::WEST:
                return new Coordinate($currentPosition->getX() + 1, $currentPosition->getY());
                break;
        }
    }
}
