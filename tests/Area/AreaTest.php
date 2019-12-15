<?php
declare(strict_types=1);

namespace CleaningRobot\Tests\Area;

use CleaningRobot\Area\Area;
use CleaningRobot\Area\Cell;
use CleaningRobot\Area\Obstacle;
use CleaningRobot\Coordinate;
use PHPUnit\Framework\TestCase;

class AreaTest extends TestCase
{
    public function testCanCellBeOccupied(): void
    {
        $area = new Area([new Cell(new Coordinate(0, 0), 'S'), new Cell(new Coordinate(0, 1), 'C')]);

        $this->assertTrue($area->canCellBeOccupied(new Coordinate(0, 0)));

        $this->assertFalse($area->canCellBeOccupied(new Coordinate(0, 1)));
        // non existing cell cannot be occupied
        $this->assertFalse($area->canCellBeOccupied(new Coordinate(1, 0)));
    }

    /**
     * @dataProvider provideArea
     */
    public function testCreateFromArray(array $inputArea, Area $expectedArea): void
    {
        $this->assertEquals($expectedArea, Area::createFromArray($inputArea));
    }

    public function provideArea(): iterable
    {
        yield 'simple area with one cleanable space' => [
            [['S']],
            new Area([new Cell(new Coordinate(0, 0), 'S')]),
        ];

        yield 'area with walls and unoccupied places' => [
            [
                ['S', 'S', 'null', 'C'],
                ['S', 'S', 'S', 'null'],
            ],
            new Area(
                [
                    new Cell(new Coordinate(0, 0), 'S'),
                    new Cell(new Coordinate(1, 0), 'S'),
                    new Obstacle(new Coordinate(2, 0)),
                    new Cell(new Coordinate(3, 0), 'C'),
                    new Cell(new Coordinate(0, 1), 'S'),
                    new Cell(new Coordinate(1, 1), 'S'),
                    new Cell(new Coordinate(2, 1), 'S'),
                    new Obstacle(new Coordinate(3, 1)),
                ]
            ),
        ];
    }
}
