<?php
declare(strict_types=1);

namespace CleaningRobot\Tests\Robot;

use CleaningRobot\Coordinate;
use CleaningRobot\Direction;
use CleaningRobot\Robot\Robot;
use CleaningRobot\Robot\RobotStateJsonOutput;
use PHPUnit\Framework\TestCase;

class RobotStateJsonOutputTest extends TestCase
{
    public function testOutput(): void
    {
        $robot = new Robot(new Coordinate(0, 0), new Direction(Direction::NORTH), 50);

        $robot->addVisitedCell(new Coordinate(0, 0));
        $robot->addVisitedCell(new Coordinate(1, 0));

        $robot->addCleanedCell(new Coordinate(0, 0));
        $robot->addCleanedCell(new Coordinate(1, 0));

        $output = (new RobotStateJsonOutput())->output($robot);

        $this->assertIsString($output);
        $this->assertSame(
            json_encode(
                [
                    'visited' => [['X' => 1, 'Y' => 0], ['X' => 0, 'Y' => 0]],
                    'cleaned' => [['X' => 1, 'Y' => 0], ['X' => 0, 'Y' => 0]],
                    'final'   => [
                        'X'      => 0,
                        'Y'      => 0,
                        'facing' => 'N',
                    ],
                    'battery' => 50,
                ],
                JSON_PRETTY_PRINT
            ),
            $output
        );
    }
}
