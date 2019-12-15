<?php
declare(strict_types=1);

namespace CleaningRobot\Area;

use CleaningRobot\Coordinate;

class Cell implements CellInterface
{
    public const CLEANABLE_SPACE = 'S';

    // can't be occupied or cleaned
    public const UNAVAILABLE_SPACE = 'C';

    /** @var string */
    private $state;

    /** @var Coordinate */
    private $coordinate;

    public function __construct(Coordinate $coordinate, string $state)
    {
        $this->coordinate = $coordinate;

        $this->setState($state);
    }

    public function getCoordinate(): Coordinate
    {
        return $this->coordinate;
    }

    public function canBeOccupied(): bool
    {
        return $this->state !== self::UNAVAILABLE_SPACE;
    }

    private function setState(string $state): void
    {
        if ($state !== self::CLEANABLE_SPACE && $state !== self::UNAVAILABLE_SPACE) {
            throw new \InvalidArgumentException(
                sprintf('Unrecognizable cell state "%s"', $state)
            );
        }

        $this->state = $state;
    }
}
