<?php
declare(strict_types=1);

namespace CleaningRobot\Robot;

class RobotStateJsonOutput
{
    public function output(Robot $robot): string
    {
        $visitedCells = [];
        foreach ($robot->getVisitedCells() as $visitedCell) {
            $visitedCells[] = ['X' => $visitedCell->getX(), 'Y' => $visitedCell->getY()];
        }

        $cleanedCells = [];
        foreach ($robot->getCleanedCells() as $cleanedCell) {
            $cleanedCells[] = ['X' => $cleanedCell->getX(), 'Y' => $cleanedCell->getY()];
        }

        return json_encode(
            [
                'visited' => $visitedCells,
                'cleaned' => $cleanedCells,
                'final'   => [
                    'X'      => $robot->getCurrentPosition()->getX(),
                    'Y'      => $robot->getCurrentPosition()->getY(),
                    'facing' => $robot->getFacing()->getDirection(),
                ],
                'battery' => $robot->getBatteryLife(),
            ],
            JSON_PRETTY_PRINT
        );
    }
}
