#!/usr/bin/env php
<?php
// AudioMan.php
require __DIR__.'/../vendor/autoload.php';


use audioMan\command\DefaultCommand;
use audioMan\command\UpdateCommand;
use audioMan\console\CustomApplication;

$application = new CustomApplication('audioMan', '@package_version@');
$application->add(new DefaultCommand());
$application->add(new UpdateCommand());
$application->setDefaultCommand('audioMan');
$application->run();
