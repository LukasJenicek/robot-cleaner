<?php
declare(strict_types=1);

namespace CleaningRobot;

use CleaningRobot\Area\Area;
use CleaningRobot\Robot\BackOffStrategyInterface;
use CleaningRobot\Robot\Command\CommandList;
use CleaningRobot\Robot\Exception\RobotStuckException;
use CleaningRobot\Robot\Robot;
use Psr\Log\LoggerInterface;

class CleaningService
{
    /** @var BackOffStrategyInterface */
    private $backOffStrategy;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(BackOffStrategyInterface $backOffStrategy, LoggerInterface $logger)
    {
        $this->backOffStrategy = $backOffStrategy;
        $this->logger          = $logger;
    }

    public function clean(Robot $robot, Area $area, CommandList $commandList): void
    {
        foreach ($commandList->getCommands() as $command) {
            try {
                $robot->run($command, $area);

                if ($robot->isLowOnBattery()) {
                    $this->logger->warning("Robot's battery has died!");

                    return;
                }
            } catch (RobotStuckException $robotStuckException) {
                $this->logger->info('Robot got stuck, proceeding with back off strategy');

                $this->backOffStrategy->performBackOffStrategy($robot, $area);
            }

            if ($robot->isStuck()) {
                return;
            }
        }
    }
}
