<?php declare(strict_types=1);

namespace Logfile;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Formatter\FormatterInterface;

class MonologHandler extends AbstractProcessingHandler
{
    protected $logfile;

    public function __construct(Logfile $logfile, int $level = Logger::DEBUG, bool $bubble = true)
    {
        $this->logfile = $logfile;
        parent::__construct($level, $bubble);
    }

    protected function getDefaultFormatter(): FormatterInterface
    {
        return new MonologFormatter();
    }

    protected function write(array $record)
    {
        $config = clone $this->logfile->getConfig();

        foreach ($record['context'] as $key => $value) {
            if ('exception' === $key && $value instanceof \Throwable) {
                continue;
            }
            $config->addTag($key, $value);
        }

        foreach ($record['extra'] as $key => $value) {
            $config->addTag($key, $value);
        }

        $payload = new Payload($record['message'], $config);

        if (isset($record['context']['exception']) && ($record['context']['exception'] instanceof \Throwable)) {
            $payload->pushException($record['context']['exception']);
        }

        $payload->setExtra('level', $record['level']);
        $payload->setExtra('level_name', $record['level_name']);
        $payload->setExtra('channel', $record['channel']);
        $payload->setExtra('datetime', $record['datetime']);

        $this->logfile->log($payload);
    }
}
