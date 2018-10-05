<?php

require __DIR__ . '/vendor/autoload.php';

$logfile = new Logfile\Logfile('92193684-b2a9-84ca-cd07-d1631a3813d2');


set_exception_handler(function (Throwable $e) use($logfile) {
    $logfile->captureException($e);
});

$handler = new Logfile\MonologHandler($logfile);

$logger = new Monolog\Logger('debug');
$logger->pushHandler($handler);
$logger->pushProcessor(function ($record) {
    $record['extra']['somemore'] = 'info';
    return $record;
});

function fail($logger) {
    $logger->error('whoops', ['foo' => 'bar']);
    throw new ErrorException('whoops');
}

fail($logger);
