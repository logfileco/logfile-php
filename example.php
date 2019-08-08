<?php

require __DIR__ . '/vendor/autoload.php';

$logfile = new Logfile\Logfile('edebc96b-85df-cf14-1487-72fb8ee7a171');
$logfile->getSender()->setHost('localhost:8077');
$logfile->getSender()->setScheme('http');
$logfile->sendAsync(true);
$logfile->getConfig()->setUser(['id' => '4']);
$logfile->getConfig()->setTags([
    'php_version' => phpversion(),
    'user' => ['id' => 1234, 'username' => 'bob'],
]);
$logfile->getConfig()->setRelease(exec('git log --pretty="%H" -n1 HEAD'));

$handler = new Logfile\MonologHandler($logfile);

$logger = new Monolog\Logger('debug');

$logger->pushProcessor(new Monolog\Processor\ProcessIdProcessor);
$logger->pushProcessor(new Monolog\Processor\MemoryUsageProcessor);
$logger->pushProcessor(new Monolog\Processor\MemoryPeakUsageProcessor);

$logger->pushHandler($handler);

// --------

set_exception_handler(function (Throwable $e) use($logfile, $logger) {
    $logger->error($e->getMessage(), ['exception' => $e]);
});

function fail($how) {
    throw new ErrorException('whoops '.$how);
}

try {
    fail('this');
} catch(ErrorException $e) {
    throw new RuntimeException('doh!', 0, $e);
}
