<?php
declare(strict_types=1);

namespace CleaningRobot\Area;

use CleaningRobot\Coordinate;

class Obstacle implements CellInterface
{
    /** @var Coordinate */
    private $coordinate;

    public function __construct(Coordinate $coordinate)
    {
        $this->coordinate = $coordinate;
    }

    public function canBeOccupied(): bool
    {
        return false;
    }

    public function getCoordinate(): Coordinate
    {
        return $this->coordinate;
    }
}
