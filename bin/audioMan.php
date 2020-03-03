#!/usr/bin/env php
<?php
// AudioMan.php
require __DIR__.'/../vendor/autoload.php';

use audioMan\command\DefaultCommand;
use audioMan\command\UpdateCommand;
use audioMan\console\CustomApplication;

try {
$application = new CustomApplication('audioMan', '@package_version@');
$application->setCatchExceptions(false);
$application->add(new DefaultCommand());
$application->add(new UpdateCommand());
$application->setDefaultCommand('audioMan');
$application->run();
} catch (Exception $e) {

    $msg = sprintf("  [Exception] %s  ", $e->getMessage());
    $indentLength = strlen($msg);
    $lineIndentation = str_repeat(' ', $indentLength);

    echo PHP_EOL;
    echo "\033[37;41m".$lineIndentation."\033[0m".PHP_EOL;
    echo "\033[37;41m".$msg."\033[0m".PHP_EOL;
    echo "\033[37;41m".$lineIndentation."\033[0m".PHP_EOL;
    echo PHP_EOL;
}
