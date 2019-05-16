<?php

use Becklyn\FixCi\Command\FixCiCommand;
use Symfony\Component\Console\Application;

if (is_file($autoloader = dirname(__DIR__) . "/vendor/autoload.php"))
{
    require_once $autoloader;
}
else if (is_file($autoloader = dirname(__DIR__, 3) . "/autoload.php"))
{
    require_once $autoloader;
}

$application = new Application();
$command = new FixCiCommand();
$application->add($command);
$application->setDefaultCommand($command->getName(), true);
$application->run();
