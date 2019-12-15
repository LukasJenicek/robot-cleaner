<?php
declare(strict_types=1);

namespace CleaningRobot\Tests\Robot\Command;

use CleaningRobot\Robot\Command\Advance;
use CleaningRobot\Robot\Command\Back;
use CleaningRobot\Robot\Command\Clean;
use CleaningRobot\Robot\Command\CommandFactory;
use CleaningRobot\Robot\Command\TurnLeft;
use CleaningRobot\Robot\Command\TurnRight;
use PHPUnit\Framework\TestCase;
use Psr\Log\Test\TestLogger;

class CommandFactoryTest extends TestCase
{
    public function testCreatingCommandFromUnknownAbbreviationThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Unknown command abbreviation "%s" passed', 'test'));

        $commandFactory = new CommandFactory(new TestLogger());
        $commandFactory->createCommandFromAbbreviation('test');
    }

    /**
     * @dataProvider provideCommandAbbreviations
     */
    public function testCreateCommand(string $commandAbbreviation, string $expectedInstance): void
    {
        $commandFactory = new CommandFactory(new TestLogger());

        $this->assertInstanceOf(
            $expectedInstance,
            $commandFactory->createCommandFromAbbreviation($commandAbbreviation)
        );
    }

    public function provideCommandAbbreviations(): iterable
    {
        yield 'Clean command' => ['C', Clean::class];

        yield 'Advance command' => ['A', Advance::class];

        yield 'Back command' => ['B', Back::class];

        yield 'Turn right command' => ['TR', TurnRight::class];

        yield 'Turn left command' => ['TL', TurnLeft::class];
    }
}
