<?php
declare(strict_types=1);

namespace CleaningRobot\Robot\Command;

use CleaningRobot\Area\Area;
use CleaningRobot\Robot\Robot;

interface Command
{
    public function execute(Robot $robot, Area $area): void;

    public function getBatteryConsumption(): int;
}
