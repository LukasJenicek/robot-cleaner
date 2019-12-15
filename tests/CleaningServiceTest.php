<?php
declare(strict_types=1);

namespace CleaningRobot\Tests;

use CleaningRobot\Area\Area;
use CleaningRobot\CleaningService;
use CleaningRobot\Coordinate;
use CleaningRobot\Direction;
use CleaningRobot\Robot\BackOffStrategy;
use CleaningRobot\Robot\Command\CommandList;
use CleaningRobot\Robot\Factory\AdvanceFactory;
use CleaningRobot\Robot\Factory\BackFactory;
use CleaningRobot\Robot\Factory\CleanFactoryCommand;
use CleaningRobot\Robot\Factory\TurnLeftFactory;
use CleaningRobot\Robot\Factory\TurnRightFactory;
use CleaningRobot\Robot\Robot;
use PHPUnit\Framework\TestCase;
use Psr\Log\Test\TestLogger;

class CleaningServiceTest extends TestCase
{
    /** @var TestLogger */
    private $logger;

    /** @var AdvanceFactory */
    private $advanceFactory;

    /** @var TurnLeftFactory */
    private $turnLeftFactoryCommand;

    /** @var CleanFactoryCommand */
    private $cleanFactoryCommand;

    /** @var TurnRightFactory */
    private $turnRightFactoryCommand;

    /** @var BackFactory */
    private $backFactoryCommand;

    protected function setUp(): void
    {
        $this->logger = new TestLogger();

        $this->advanceFactory          = new AdvanceFactory($this->logger);
        $this->turnLeftFactoryCommand  = new TurnLeftFactory($this->logger);
        $this->turnRightFactoryCommand = new TurnRightFactory($this->logger);
        $this->cleanFactoryCommand     = new CleanFactoryCommand($this->logger);
        $this->backFactoryCommand      = new BackFactory($this->logger);
    }

    public function testCleanArea(): void
    {
        $cleaningService = new CleaningService(
            new BackOffStrategy(
                $this->advanceFactory,
                $this->backFactoryCommand,
                $this->turnLeftFactoryCommand,
                $this->turnRightFactoryCommand,
                $this->logger
            ),
            $this->logger
        );

        $area = Area::createFromArray(
            [
                ['S', 'S', 'S', 'S'],
                ['S', 'S', 'C', 'S'],
                ['S', 'S', 'S', 'S'],
                ['S', 'null', 'S', 'S'],
            ]
        );

        $robot = new Robot(new Coordinate(3, 0), new Direction(Direction::NORTH), 80);

        $commandList = new CommandList(
            [
                $this->turnLeftFactoryCommand->createTurnLeftCommand(),
                $this->advanceFactory->createAdvanceCommand(),
                $this->cleanFactoryCommand->createCleanCommand(),
                $this->advanceFactory->createAdvanceCommand(),
                $this->cleanFactoryCommand->createCleanCommand(),
                $this->turnRightFactoryCommand->createTurnRightCommand(),
                $this->advanceFactory->createAdvanceCommand(),
                $this->cleanFactoryCommand->createCleanCommand(),
            ]
        );

        $cleaningService->clean($robot, $area, $commandList);

        $this->assertEquals(
            [new Coordinate(1, 0), new Coordinate(2, 0), new Coordinate(3, 0)],
            $robot->getVisitedCells()
        );
        $this->assertEquals(
            [new Coordinate(1, 0), new Coordinate(2, 0)],
            $robot->getCleanedCells()
        );
        $this->assertEquals(new Direction(Direction::NORTH), $robot->getFacing());
        $this->assertEquals(new Coordinate(2, 0), $robot->getCurrentPosition());
        $this->assertEquals(53, $robot->getBatteryLife());
    }

    public function testCleanAnotherArea(): void
    {
        $cleaningService = new CleaningService(
            new BackOffStrategy(
                $this->advanceFactory,
                $this->backFactoryCommand,
                $this->turnLeftFactoryCommand,
                $this->turnRightFactoryCommand,
                $this->logger
            ),
            $this->logger
        );

        $area = Area::createFromArray(
            [
                ['S', 'S', 'S', 'S'],
                ['S', 'S', 'C', 'S'],
                ['S', 'S', 'S', 'S'],
                ['S', 'null', 'S', 'S'],
            ]
        );

        $robot = new Robot(new Coordinate(3, 1), new Direction(Direction::SOUTH), 1094);

        $commandList = new CommandList(
            [
                $this->turnRightFactoryCommand->createTurnRightCommand(),
                $this->advanceFactory->createAdvanceCommand(),
                $this->cleanFactoryCommand->createCleanCommand(),
                $this->advanceFactory->createAdvanceCommand(),
                $this->cleanFactoryCommand->createCleanCommand(),
                $this->turnRightFactoryCommand->createTurnRightCommand(),
                $this->advanceFactory->createAdvanceCommand(),
                $this->cleanFactoryCommand->createCleanCommand(),
            ]
        );

        $cleaningService->clean($robot, $area, $commandList);

        $this->assertEquals(
            [new Coordinate(2, 0), new Coordinate(3, 0), new Coordinate(3, 1)],
            $robot->getVisitedCells()
        );
        $this->assertEquals(
            [new Coordinate(2, 0), new Coordinate(3, 0)],
            $robot->getCleanedCells()
        );
        $this->assertEquals(new Direction(Direction::NORTH), $robot->getFacing());
        $this->assertEquals(new Coordinate(3, 0), $robot->getCurrentPosition());
        $this->assertEquals(1063, $robot->getBatteryLife());
    }
}
