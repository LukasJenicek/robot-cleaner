<?php
declare(strict_types=1);

namespace CleaningRobot\Area;

use CleaningRobot\Coordinate;

class Area
{
    /** @var CellInterface[][]|null */
    private $cells;

    public function __construct(array $cells)
    {
        foreach ($cells as $cell) {
            $this->addCell($cell);
        }
    }

    public function addCell(CellInterface $cell): void
    {
        $this->cells[$cell->getCoordinate()->getY()][$cell->getCoordinate()->getX()] = $cell;
    }

    public function canCellBeOccupied(Coordinate $coordinate): bool
    {
        if (!isset($this->cells[$coordinate->getY()][$coordinate->getX()])) {
            return false;
        }

        $cell = $this->cells[$coordinate->getY()][$coordinate->getX()];

        return $cell->canBeOccupied();
    }

    public static function createFromArray(array $map): self
    {
        $cells = [];

        foreach ($map as $y => $rows) {
            foreach ($rows as $x => $cellType) {
                if ($cellType === 'null' || $cellType === null) {
                    $cells[] = new Obstacle(new Coordinate($x, $y));

                    continue;
                }

                $cells[] = new Cell(new Coordinate($x, $y), $cellType);
            }
        }

        return new Area($cells);
    }
}
