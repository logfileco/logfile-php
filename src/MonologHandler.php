<?php declare(strict_types=1);

namespace Logfile;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\NormalizerFormatter;

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

        if (isset($record['context']['exception']) && ($record['context']['exception'] instanceof \Throwable)) {
            $payload = Payload::createFromException($record['context']['exception'], $config);
        } else {
            $payload = new Payload($record['message'], $config);
        }

        $payload->setExtra('level', $record['level']);
        $payload->setExtra('level_name', $record['level_name']);
        $payload->setExtra('channel', $record['channel']);
        $payload->setExtra('datetime', $record['datetime']);

        $this->logfile->log($payload);
    }
}
