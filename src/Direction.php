<?php
declare(strict_types=1);

namespace CleaningRobot;

class Direction
{
    public const NORTH = 'N';
    public const WEST  = 'W';
    public const SOUTH = 'S';
    public const EAST  = 'E';

    /** @var string */
    private $direction;

    public function __construct(string $direction)
    {
        $this->direction = $direction;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }

    public function toAngle(): int
    {
        switch ($this->direction) {
            case self::NORTH:
                return 360;

                break;
            case self::EAST:
                return 90;

                break;
            case self::SOUTH:
                return 180;

                break;
            case self::WEST:
                return 270;

                break;
        }
    }

    public static function fromAngle(int $angle): self
    {
        $currentAngle = $angle % 360;

        switch ($currentAngle) {
            case 0:
            case 360:
                return new Direction(self::NORTH);

                break;
            case 90:
                return new Direction(self::EAST);

                break;
            case 180:
                return new Direction(self::SOUTH);

                break;
            case 270:
                return new Direction(self::WEST);
        }
    }
}
