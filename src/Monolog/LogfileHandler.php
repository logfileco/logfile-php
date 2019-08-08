<?php declare(strict_types=1);

namespace Logfile\Monolog;

use Logfile\Logfile;
use Logfile\Payload;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class LogfileHandler extends AbstractProcessingHandler
{
    protected $logfile;

    protected $levelMap = array(
        Logger::DEBUG     => 'debug',
        Logger::INFO      => 'info',
        Logger::NOTICE    => 'info',
        Logger::WARNING   => 'warning',
        Logger::ERROR     => 'error',
        Logger::CRITICAL  => 'critical',
        Logger::ALERT     => 'critical',
        Logger::EMERGENCY => 'critical',
    );

    public function __construct(Logfile $logfile, int $level = Logger::DEBUG, bool $bubble = true)
    {
        $this->logfile = $logfile;
        parent::__construct($level, $bubble);
    }

    protected function write(array $record)
    {
        $config = clone $this->logfile->getConfig();

        $context = array_filter(array_merge(
            array_diff_key($record['context'], array_flip(['exception'])),
            $record['extra'],
            array_diff_key($record, array_flip(['context', 'extra', 'message', 'level', 'level_name', 'formatted', 'datetime']))
        ));

        foreach ($context as $key => $value) {
            $config->addTag($key, $value);
        }

        if (isset($record['context']['exception']) && ($record['context']['exception'] instanceof \Throwable)) {
            $payload = Payload::createFromException($record['context']['exception'], $config);
        } else {
            $payload = new Payload($record['message'], $config);
        }

        if (isset($record['datetime'])) {
            $payload->setDateTime($record['datetime']);
        }

        if (isset($record['level'])) {
            $payload->setSeverity($this->levelMap[$record['level']]);
        }

        $this->logfile->log($payload);
    }
}
