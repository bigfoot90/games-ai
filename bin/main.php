#!/bin/env php
<?php

const PROJECT_ROOT = __DIR__ . '/..';

require PROJECT_ROOT . '/vendor/autoload.php';

$application = new Symfony\Component\Console\Application();

$application->add(new \Games\Tris\Console\HumanConsole());
$application->add(new \Games\Tris\Console\BotConsole());

$application->run();