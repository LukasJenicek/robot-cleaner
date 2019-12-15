<?php
declare(strict_types=1);

namespace CleaningRobot\Robot;

use CleaningRobot\Area\Area;
use CleaningRobot\Robot\Command\CommandList;
use CleaningRobot\Robot\Exception\RobotStuckException;
use CleaningRobot\Robot\Factory\AdvanceFactory;
use CleaningRobot\Robot\Factory\BackFactory;
use CleaningRobot\Robot\Factory\TurnLeftFactory;
use CleaningRobot\Robot\Factory\TurnRightFactory;
use Psr\Log\LoggerInterface;

class BackOffStrategy implements BackOffStrategyInterface
{
    /** @var AdvanceFactory */
    private $advanceFactory;

    /** @var BackFactory */
    private $backFactory;

    /** @var TurnLeftFactory */
    private $turnLeftFactory;

    /** @var TurnRightFactory */
    private $turnRightFactory;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        AdvanceFactory $advanceFactory,
        BackFactory $backFactory,
        TurnLeftFactory $turnLeftFactory,
        TurnRightFactory $turnRightFactory,
        LoggerInterface $logger
    ) {
        $this->advanceFactory   = $advanceFactory;
        $this->backFactory      = $backFactory;
        $this->turnLeftFactory  = $turnLeftFactory;
        $this->turnRightFactory = $turnRightFactory;
        $this->logger           = $logger;
    }

    public function performBackOffStrategy(Robot $robot, Area $area): void
    {
        /** @var CommandList[] $backupStrategies */
        $backupStrategies = [
            new CommandList(
                [
                    $this->turnRightFactory->createTurnRightCommand(),
                    $this->advanceFactory->createAdvanceCommand(),
                    $this->turnLeftFactory->createTurnLeftCommand(),
                ]
            ),
            new CommandList(
                [
                    $this->turnRightFactory->createTurnRightCommand(),
                    $this->advanceFactory->createAdvanceCommand(),
                    $this->turnRightFactory->createTurnRightCommand(),
                ]
            ),
            new CommandList(
                [
                    $this->turnRightFactory->createTurnRightCommand(),
                    $this->advanceFactory->createAdvanceCommand(),
                    $this->turnRightFactory->createTurnRightCommand(),
                ]
            ),
            new CommandList(
                [
                    $this->turnRightFactory->createTurnRightCommand(),
                    $this->backFactory->createBackCommand(),
                    $this->turnRightFactory->createTurnRightCommand(),
                    $this->advanceFactory->createAdvanceCommand(),
                ]
            ),
            new CommandList(
                [
                    $this->turnLeftFactory->createTurnLeftCommand(),
                    $this->turnLeftFactory->createTurnLeftCommand(),
                    $this->advanceFactory->createAdvanceCommand(),
                ]
            ),
        ];

        foreach ($backupStrategies as $backupStrategy) {
            foreach ($backupStrategy->getCommands() as $command) {
                try {
                    $robot->run($command, $area);

                    if ($robot->isLowOnBattery()) {
                        $this->logger->warning(
                            "While performing back off strategy robot's battery has died!"
                        );

                        return;
                    }
                } catch (RobotStuckException $exception) {
                    $this->logger->warning("Robot is still stucked, continuing with anoter backup strategy");

                    continue;
                }
            }

            if ($robot->isStuck() === false) {
                return;
            }
        }
    }
}
