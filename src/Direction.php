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
            case self::EAST:
                return 90;
            case self::SOUTH:
                return 180;
            case self::WEST:
                return 270;
            default:
                throw new \Exception(
                    sprintf('Invalid direction passed "%s"', $this->direction)
                );
        }
    }

    public static function fromAngle(int $angle): self
    {
        $currentAngle = $angle % 360;

        switch ($currentAngle) {
            case 0:
            case 360:
                return new Direction(self::NORTH);
            case 90:
                return new Direction(self::EAST);
            case 180:
                return new Direction(self::SOUTH);
            case 270:
                return new Direction(self::WEST);
            default:
                throw new \Exception(
                    sprintf('Unknown angle %d', $currentAngle)
                );
        }
    }
}
