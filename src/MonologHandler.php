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

        $context = array_merge(
            array_diff_key($record['context'], array_flip(['exception'])),
            $record['extra'],
            array_intersect_key($record, array_flip(['level', 'level_name', 'channel', 'datetime']))
        );

        foreach ($context as $key => $value) {
            $config->addTag($key, $value);
        }

        if (isset($record['context']['exception']) && ($record['context']['exception'] instanceof \Throwable)) {
            $payload = Payload::createFromException($record['context']['exception'], $config);
        } else {
            $payload = new Payload($record['message'], $config);
        }

        $this->logfile->log($payload);
    }
}
