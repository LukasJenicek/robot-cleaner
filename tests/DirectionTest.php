<?php
declare(strict_types=1);

namespace CleaningRobot\Tests;

use CleaningRobot\Direction;
use PHPUnit\Framework\TestCase;

class DirectionTest extends TestCase
{
    /**
     * @dataProvider provideDirections
     */
    public function testDirectionToAngle(Direction $direction, int $expectedAngle): void
    {
        $this->assertSame($expectedAngle, $direction->toAngle());
    }

    public function provideDirections(): iterable
    {
        yield 'north' => [new Direction(Direction::NORTH), 360];
        yield 'east' => [new Direction(Direction::EAST), 90];
        yield 'south' => [new Direction(Direction::SOUTH), 180];
        yield 'west' => [new Direction(Direction::WEST), 270];
    }

    /**
     * @dataProvider provideAngles
     */
    public function testConstructDirectionFromAngle(int $angle, Direction $expectedDirection): void
    {
        $this->assertEquals($expectedDirection, Direction::fromAngle($angle));
    }

    public function provideAngles(): iterable
    {
        yield 'north' => [360, new Direction(Direction::NORTH)];
        yield 'east' => [90, new Direction(Direction::EAST)];
        yield 'south' => [180, new Direction(Direction::SOUTH)];
        yield 'west' => [270, new Direction(Direction::WEST)];
    }
}
