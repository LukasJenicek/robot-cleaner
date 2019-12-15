<?php
declare(strict_types=1);

namespace CleaningRobot\Tests\Robot\Command;

use CleaningRobot\Robot\Command\CommandFactory;
use CleaningRobot\Robot\Command\CommandListFactory;
use PHPUnit\Framework\TestCase;
use Psr\Log\Test\TestLogger;

class CommandListFactoryTest extends TestCase
{
    public function testCreateCommandList(): void
    {
        $commandListFactory = new CommandListFactory(new CommandFactory(new TestLogger()));

        $commandList = $commandListFactory->createCommandListFromAbbreviations(['TR', 'TL', 'C', 'B']);

        $this->assertCount(4, $commandList->getCommands());
    }
}
