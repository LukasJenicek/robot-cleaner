<?php
declare(strict_types=1);

namespace CleaningRobot\Robot;

use CleaningRobot\Area\Area;
use CleaningRobot\Area\Cell;
use CleaningRobot\Coordinate;
use CleaningRobot\Direction;
use CleaningRobot\Robot\Command\Command;
use CleaningRobot\Robot\Exception\RobotStuckException;

class Robot
{
    public const AVAILABLE      = 'available';
    public const STUCK          = 'stuck';
    public const LOW_ON_BATTERY = 'low_on_battery';

    /** @var Coordinate */
    private $currentPosition;

    /** @var int */
    private $batteryLife;

    /** @var Direction */
    private $facing;

    /** @var string */
    private $state;

    /** @var Coordinate[] */
    private $visitedCells = [];

    /** @var Coordinate[] */
    private $cleanedCells = [];

    public function __construct(Coordinate $start, Direction $facing, int $batteryLife)
    {
        $this->currentPosition = $start;
        $this->batteryLife     = $batteryLife;
        $this->facing          = $facing;
        $this->state           = self::AVAILABLE;

        $this->addVisitedCell($start);
    }

    /**
     * @param array<mixed, mixed> $robotData
     */
    public static function fromArray(array $robotData): self
    {
        return new self(
            new Coordinate($robotData['start']['X'], $robotData['start']['Y']),
            new Direction($robotData['start']['facing']),
            $robotData['battery']
        );
    }

    public function run(Command $command, Area $area): void
    {
        if ($this->batteryLife < $command->getBatteryConsumption()) {
            $this->state = self::LOW_ON_BATTERY;

            return;
        }

        try {
            $command->execute($this, $area);
        } catch (RobotStuckException $exception) {
            $this->batteryLife -= $command->getBatteryConsumption();
            $this->state       = self::STUCK;

            throw $exception;
        }

        $this->batteryLife -= $command->getBatteryConsumption();
    }

    public function isStuck(): bool
    {
        return $this->state === self::STUCK;
    }

    public function isLowOnBattery(): bool
    {
        return $this->state === self::LOW_ON_BATTERY;
    }

    public function getBatteryLife(): int
    {
        return $this->batteryLife;
    }

    public function getCurrentPosition(): Coordinate
    {
        return $this->currentPosition;
    }

    public function getFacing(): Direction
    {
        return $this->facing;
    }

    public function moveToPosition(Coordinate $coordinate): void
    {
        $this->currentPosition = $coordinate;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function setFacing(Direction $facing): void
    {
        $this->facing = $facing;
    }

    public function addCleanedCell(Coordinate $coordinate): void
    {
        if (in_array($coordinate, $this->cleanedCells)) {
            return;
        }

        array_unshift($this->cleanedCells, $coordinate);
    }

    public function addVisitedCell(Coordinate $coordinate): void
    {
        if (in_array($coordinate, $this->visitedCells)) {
            return;
        }

        array_unshift($this->visitedCells, $coordinate);
    }

    /**
     * @return Coordinate[]
     */
    public function getVisitedCells(): array
    {
        return $this->visitedCells;
    }

    /**
     * @return Coordinate[]
     */
    public function getCleanedCells(): array
    {
        return $this->cleanedCells;
    }
}
