<?php

use CleaningRobot\Area\Area;
use CleaningRobot\CleaningService;
use CleaningRobot\Robot\BackOffStrategy;
use CleaningRobot\Robot\Command\CommandFactory;
use CleaningRobot\Robot\Command\CommandListFactory;
use CleaningRobot\Robot\Factory\AdvanceFactory;
use CleaningRobot\Robot\Factory\BackFactory;
use CleaningRobot\Robot\Factory\TurnLeftFactory;
use CleaningRobot\Robot\Factory\TurnRightFactory;
use CleaningRobot\Robot\Robot;
use CleaningRobot\Robot\RobotStateJsonOutput;
use Psr\Log\Test\TestLogger;

require_once 'vendor/autoload.php';

if (!isset($argv[1])) {
    throw new InvalidArgumentException('Input json is mandatory argument');
}

if (!isset($argv[2])) {
    throw new InvalidArgumentException('Output file name is mandatory argument');
}

$sourceFile          = $argv[1];
$destinationFileName = $argv[2];

if (file_exists($sourceFile) === false) {
    throw new InvalidArgumentException(
        sprintf('File %s was not found', $sourceFile)
    );
}

$input = json_decode(file_get_contents($sourceFile), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    throw new InvalidArgumentException(
        'JSON in passed file is invalid'
    );
}

$logger = new TestLogger();

$cleaningService = new CleaningService(
    new BackOffStrategy(
        new AdvanceFactory($logger),
        new BackFactory($logger),
        new TurnLeftFactory($logger),
        new TurnRightFactory($logger),
        $logger
    ),
    $logger
);

$commandListFactory = new CommandListFactory(new CommandFactory($logger));

$cleaningService->clean(
    $robot = Robot::fromArray($input),
    Area::createFromArray($input['map']),
    $commandListFactory->createCommandListFromAbbreviations($input['commands'])
);


foreach ($logger->records as $record) {
    echo "{$record['message']} \n";
}

$output = (new RobotStateJsonOutput())->output($robot);

file_put_contents($destinationFileName, $output);

echo sprintf("\nOutput %s saved to file '%s'\n", $output, $destinationFileName);
