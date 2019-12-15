<?php
declare(strict_types=1);

namespace CleaningRobot\Area;

use CleaningRobot\Coordinate;

class Area
{
    /** @var Cell[]|null */
    private $cells;

    public function __construct(array $cells)
    {
        $this->cells = $cells;
    }

    public function canCellBeOccupied(Coordinate $coordinate): bool
    {
        if (!isset($this->cells[$coordinate->getY()][$coordinate->getX()])) {
            return false;
        }

        /** @var Cell $cell */
        $cell = $this->cells[$coordinate->getY()][$coordinate->getX()];

        return $cell->getState() !== Cell::UNAVAILABLE_SPACE;
    }

    public static function createFromArray(array $map): self
    {
        $cells = [];

        foreach ($map as $y => $rows) {
            foreach ($rows as $x => $cellType) {
                if ($cellType === 'null' || $cellType === null) {
                    $cells[$y][$x] = null;
                    continue;
                }

                $cells[$y][$x] = new Cell(new Coordinate($x, $y), $cellType);
            }
        }

        return new Area($cells);
    }
}
