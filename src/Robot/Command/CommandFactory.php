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

                break;
            case 'A':
                return (new AdvanceFactory($this->logger))->createAdvanceCommand();

                break;
            case 'C':
                return (new CleanFactoryCommand($this->logger))->createCleanCommand();

                break;
            case 'TR':
                return (new TurnRightFactory($this->logger))->createTurnRightCommand();

                break;
            case 'B':
                return (new BackFactory($this->logger))->createBackCommand();

                break;
            default:
                throw new \InvalidArgumentException(
                    sprintf('Unknown command abbreviation "%s" passed', $commandAbbreviation)
                );
        }
    }
}
