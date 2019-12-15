<?php
declare(strict_types=1);

namespace CleaningRobot\Robot\Command;

use CleaningRobot\Area\Area;
use CleaningRobot\Coordinate;
use CleaningRobot\Direction;
use CleaningRobot\Robot\Exception\RobotStuckException;
use CleaningRobot\Robot\Robot;
use Psr\Log\LoggerInterface;

class Advance implements Command
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
                'Robot is advancing to new position "x: %d", "y: %d"',
                $targetPosition->getX(),
                $targetPosition->getY()
            )
        );

        if ($area->canCellBeOccupied($targetPosition) === false) {
            throw new RobotStuckException('Robot could not advance, there is obstacle');
        }

        $robot->moveToPosition($targetPosition);
        $robot->setState(Robot::AVAILABLE);
        $robot->addVisitedCell($targetPosition);

        $this->logger->info('Robot successfully advanced to new position');
    }

    public function getBatteryConsumption(): int
    {
        return 2;
    }

    private function getTargetedPosition(Coordinate $currentPosition, Direction $direction): Coordinate
    {
        switch ($direction->getDirection()) {
            case Direction::NORTH:
                return new Coordinate($currentPosition->getX(), $currentPosition->getY() - 1);
            case Direction::EAST:
                return new Coordinate($currentPosition->getX() + 1, $currentPosition->getY());
            case Direction::SOUTH:
                return new Coordinate($currentPosition->getX(), $currentPosition->getY() + 1);
            case Direction::WEST:
                return new Coordinate($currentPosition->getX() - 1, $currentPosition->getY());
            default:
                throw new \InvalidArgumentException(
                    sprintf(
                        'Targeted position could not be discovered because of passed direction "%s"',
                        $direction->getDirection()
                    )
                );
        }
    }
}
