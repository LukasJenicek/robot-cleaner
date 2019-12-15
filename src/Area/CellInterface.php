<?php
declare(strict_types=1);

namespace CleaningRobot\Area;

use CleaningRobot\Coordinate;

interface CellInterface
{
    public function canBeOccupied(): bool;

    public function getCoordinate(): Coordinate;
}
