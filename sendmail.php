#!/usr/bin/php5
<?php

require __DIR__ . '/vendor/autoload.php';

if ( !defined('ROOT') ) {
    define('ROOT', __DIR__);
}

$app = new \Symfony\Component\Console\Application();
$app->add(new \Flock\Console\Command\MailerCommand());
$app->run();

