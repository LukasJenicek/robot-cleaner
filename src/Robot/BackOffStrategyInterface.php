<?php
declare(strict_types=1);

namespace CleaningRobot\Robot;

use CleaningRobot\Area\Area;

interface BackOffStrategyInterface
{
    public function performBackOffStrategy(Robot $robot, Area $area): void;
}
