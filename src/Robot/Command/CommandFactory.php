<?php
declare(strict_types=1);

namespace CleaningRobot\Robot\Command;

use CleaningRobot\Robot\Factory\AdvanceFactory;
use CleaningRobot\Robot\Factory\BackFactory;
use CleaningRobot\Robot\Factory\CleanFactoryCommand;
use CleaningRobot\Robot\Factory\TurnLeftFactory;
use CleaningRobot\Robot\Factory\TurnRightFactory;
use Psr\Log\LoggerInterface;

class CommandFactory
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function createCommandFromAbbreviation(string $commandAbbreviation): Command
    {
        switch ($commandAbbreviation) {
            case 'TL':
                return (new TurnLeftFactory($this->logger))->createTurnLeftCommand();
            case 'A':
                return (new AdvanceFactory($this->logger))->createAdvanceCommand();
            case 'C':
                return (new CleanFactoryCommand($this->logger))->createCleanCommand();
            case 'TR':
                return (new TurnRightFactory($this->logger))->createTurnRightCommand();
            case 'B':
                return (new BackFactory($this->logger))->createBackCommand();
            default:
                throw new \InvalidArgumentException(
                    sprintf('Unknown command abbreviation "%s" passed', $commandAbbreviation)
                );
        }
    }
}
