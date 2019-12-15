<?php
declare(strict_types=1);

namespace CleaningRobot\Robot\Command;

class CommandList
{
    /**
     * @var Command[]
     */
    private $commands;

    public function __construct(array $commands)
    {
        foreach ($commands as $command) {
            $this->addCommand($command);
        }
    }

    public function addCommand(Command $command): void
    {
        $this->commands[] = $command;
    }

    /**
     * @return Command[]
     */
    public function getCommands(): array
    {
        return $this->commands;
    }
}
