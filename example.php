<?php

require __DIR__ . '/vendor/autoload.php';

$logfile = new Logfile\Logfile('5d428582-7733-151c-4eaf-d23d0ebdca3b');
$logfile->getSender()->setHost('localhost:8077');
$logfile->getSender()->setScheme('http');
$logfile->sendAsync(true);
$logfile->getConfig()->setUser(['id' => '4']);
$logfile->getConfig()->setTags([
    'php_version' => phpversion(),
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
