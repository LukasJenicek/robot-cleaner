<?php
declare(strict_types=1);

namespace CleaningRobot\Tests\Robot;

use CleaningRobot\Area\Area;
use CleaningRobot\Coordinate;
use CleaningRobot\Direction;
use CleaningRobot\Robot\Command\Clean;
use CleaningRobot\Robot\Command\CommandList;
use CleaningRobot\Robot\Command\TurnLeft;
use CleaningRobot\Robot\Command\TurnRight;
use CleaningRobot\Robot\Exception\RobotStuckException;
use CleaningRobot\Robot\Factory\AdvanceFactory;
use CleaningRobot\Robot\Factory\BackFactory;
use CleaningRobot\Robot\Robot;
use PHPUnit\Framework\TestCase;
use Psr\Log\Test\TestLogger;

class RobotTest extends TestCase
{
    /** @var TestLogger */
    private $logger;

    /** @var AdvanceFactory */
    private $advanceFactory;

    /** @var BackFactory */
    private $backFactoryCommand;

    protected function setUp(): void
    {
        $this->logger             = new TestLogger();
        $this->advanceFactory     = new AdvanceFactory($this->logger);
        $this->backFactoryCommand = new BackFactory($this->logger);
    }

    public function testExecuteCommandWhenRobotDoesNotHaveEnoughEnergy(): void
    {
        $robot = new Robot(
            new Coordinate(0, 0),
            new Direction(Direction::NORTH),
            4,
        );

        $robot->run(new Clean($this->logger), $area = Area::createFromArray([['S', 'S', 'S', 'S']]));

        $this->assertEquals([new Coordinate(0, 0)], $robot->getVisitedCells());
        $this->assertEquals([], $robot->getCleanedCells());
    }

    public function testWhenRobotImpactObstacleEnergyShouldBeConsumedAndExceptionIsThrown(): void
    {
        $this->expectException(RobotStuckException::class);

        $robot = new Robot(
            new Coordinate(0, 0),
            new Direction(Direction::NORTH),
            2,
        );

        $area = Area::createFromArray([['S', 'S']]);

        foreach ((new CommandList([$this->advanceFactory->createAdvanceCommand()]))->getCommands() as $command) {
            $robot->run($command, $area);
        }

        $this->assertSame(0, $robot->getBatteryLife());
        $this->assertEquals(new Coordinate(0, 0), $robot->getCurrentPosition());
    }

    public function testRobotGetStuck(): void
    {
        $robot = new Robot(
            new Coordinate(0, 0),
            new Direction(Direction::NORTH),
            2
        );

        $this->expectException(RobotStuckException::class);

        $robot->run(
            $this->advanceFactory->createAdvanceCommand(),
            $area = Area::createFromArray([['S', 'S', 'S', 'S']])
        );

        $this->assertTrue($robot->isStuck());

        $robot = new Robot(new Coordinate(0, 0), new Direction(Direction::NORTH), 5);

        $robot->run($this->backFactoryCommand->createBackCommand(), Area::createFromArray([['S', 'S']]));

        $this->assertTrue($robot->isStuck());
    }

    /**
     * @dataProvider provideDataForAdvanceCommand
     */
    public function testRobotAdvance(
        Direction $robotFacing,
        Coordinate $robotStartingPosition,
        Coordinate $expectedCurrentPosition,
        array $visitedCells
    ): void {
        $robot = new Robot(
            $robotStartingPosition,
            $robotFacing,
            2
        );

        $robot->run(
            $this->advanceFactory->createAdvanceCommand(),
            $area = Area::createFromArray([['S', 'S'], ['S', 'S']])
        );

        $this->assertEquals($expectedCurrentPosition, $robot->getCurrentPosition());
        $this->assertEquals($visitedCells, $robot->getVisitedCells());
    }

    public function provideDataForAdvanceCommand(): iterable
    {
        yield 'advance while facing east direction' => [
            new Direction(Direction::EAST),
            new Coordinate(0, 0),
            new Coordinate(1, 0),
            [new Coordinate(1, 0), new Coordinate(0, 0)],
        ];

        yield 'advance while facing north direction' => [
            new Direction(Direction::NORTH),
            new Coordinate(0, 1),
            new Coordinate(0, 0),
            [new Coordinate(0, 0), new Coordinate(0, 1)],
        ];

        yield 'advance while facing south direction' => [
            new Direction(Direction::SOUTH),
            new Coordinate(0, 0),
            new Coordinate(0, 1),
            [new Coordinate(0, 1), new Coordinate(0, 0)],
        ];

        yield 'advance while facing west direction' => [
            new Direction(Direction::WEST),
            new Coordinate(1, 0),
            new Coordinate(0, 0),
            [new Coordinate(0, 0), new Coordinate(1, 0)],
        ];
    }

    /**
     * @dataProvider provideDataForBackUpCommand
     */
    public function testRobotBackUp(
        Direction $robotFacing,
        Coordinate $robotStartingPosition,
        Coordinate $expectedCurrentPosition,
        array $visitedCells
    ): void {
        $robot = new Robot($robotStartingPosition, $robotFacing, 3);

        $robot->run($this->backFactoryCommand->createBackCommand(), Area::createFromArray([['S', 'S'], ['S', 'S']]));

        $this->assertEquals($expectedCurrentPosition, $robot->getCurrentPosition());
        $this->assertEquals($visitedCells, $robot->getVisitedCells());

    }

    public function provideDataForBackUpCommand(): iterable
    {
        yield 'backup while facing east direction' => [
            new Direction(Direction::EAST),
            new Coordinate(1, 0),
            new Coordinate(0, 0),
            [new Coordinate(0, 0), new Coordinate(1, 0)],
        ];

        yield 'backup while facing north direction' => [
            new Direction(Direction::NORTH),
            new Coordinate(0, 0),
            new Coordinate(0, 1),
            [new Coordinate(0, 1), new Coordinate(0, 0)],
        ];

        yield 'backup while facing south direction' => [
            new Direction(Direction::SOUTH),
            new Coordinate(0, 1),
            new Coordinate(0, 0),
            [new Coordinate(0, 0), new Coordinate(0, 1)],
        ];

        yield 'backup while facing west direction' => [
            new Direction(Direction::WEST),
            new Coordinate(0, 0),
            new Coordinate(1, 0),
            [new Coordinate(1, 0), new Coordinate(0, 0)],
        ];
    }

    public function testRobotTurnRight(): void
    {
        $robot = new Robot(
            new Coordinate(0, 0),
            new Direction(Direction::NORTH),
            20,
        );
        $area  = Area::createFromArray([['S']]);

        $turnRight = new TurnRight(new TestLogger());

        $turnRight->execute($robot, $area);
        $this->assertEquals(new Direction(Direction::EAST), $robot->getFacing());

        $turnRight->execute($robot, $area);
        $this->assertEquals(new Direction(Direction::SOUTH), $robot->getFacing());

        $turnRight->execute($robot, $area);
        $this->assertEquals(new Direction(Direction::WEST), $robot->getFacing());

        $turnRight->execute($robot, $area);
        $this->assertEquals(new Direction(Direction::NORTH), $robot->getFacing());

        $turnRight->execute($robot, $area);
        $this->assertEquals(new Direction(Direction::EAST), $robot->getFacing());

        $turnRight->execute($robot, $area);
        $this->assertEquals(new Direction(Direction::SOUTH), $robot->getFacing());

        $turnRight->execute($robot, $area);
        $this->assertEquals(new Direction(Direction::WEST), $robot->getFacing());

        $turnRight->execute($robot, $area);
        $this->assertEquals(new Direction(Direction::NORTH), $robot->getFacing());
    }

    public function testRobotTurnLeft(): void
    {
        $robot = new Robot(new Coordinate(0, 0), new Direction(Direction::NORTH), 20);
        $area  = Area::createFromArray([['S']]);

        $turnLeft = new TurnLeft($this->logger);

        $turnLeft->execute($robot, $area);
        $this->assertEquals(new Direction(Direction::WEST), $robot->getFacing());

        $turnLeft->execute($robot, $area);
        $this->assertEquals(new Direction(Direction::SOUTH), $robot->getFacing());

        $turnLeft->execute($robot, $area);
        $this->assertEquals(new Direction(Direction::EAST), $robot->getFacing());

        $turnLeft->execute($robot, $area);
        $this->assertEquals(new Direction(Direction::NORTH), $robot->getFacing());

        $turnLeft->execute($robot, $area);
        $this->assertEquals(new Direction(Direction::WEST), $robot->getFacing());

        $turnLeft->execute($robot, $area);
        $this->assertEquals(new Direction(Direction::SOUTH), $robot->getFacing());
    }

    public function testRobotCleanUp(): void
    {
        $robot = new Robot(new Coordinate(0, 0), new Direction(Direction::NORTH), 5);

        $robot->run(new Clean($this->logger), $area = Area::createFromArray([['S']]));

        $this->assertEquals([new Coordinate(0, 0)], $robot->getCleanedCells());
        $this->assertSame(0, $robot->getBatteryLife());
    }

}
