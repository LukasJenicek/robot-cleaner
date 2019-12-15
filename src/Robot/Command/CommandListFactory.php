<?php
declare(strict_types=1);

namespace CleaningRobot\Robot\Command;

class CommandListFactory
{
    /** @var CommandFactory */
    private $commandFactory;

    public function __construct(CommandFactory $commandFactory)
    {
        $this->commandFactory = $commandFactory;
    }

    /**
     * @param string[] $commandAbbreviations
     */
    public function createCommandListFromAbbreviations(array $commandAbbreviations): CommandList
    {
        $commands = [];

        foreach ($commandAbbreviations as $commandAbbreviation) {
            $commands[] = $this->commandFactory->createCommandFromAbbreviation($commandAbbreviation);
        }

        return new CommandList($commands);
    }
}
