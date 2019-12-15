<?php
declare(strict_types=1);

namespace CleaningRobot\Tests;

use CleaningRobot\Coordinate;
use PHPUnit\Framework\TestCase;

class CoordinateTest extends TestCase
{
    public function testInitializingCoordinate(): void
    {
        $coordinate = new Coordinate(0, 1);

        $this->assertSame(0, $coordinate->getX());
        $this->assertSame(1, $coordinate->getY());
    }
}
