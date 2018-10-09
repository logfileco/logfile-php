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
        return new NormalizerFormatter();
    }

    protected function write(array $record)
    {
        if (isset($record['context']['exception']) && ($record['context']['exception'] instanceof \Throwable)) {
            $this->logfile->captureException($record['context']['exception']);
        } else {
            $payload = new Payload($record['formatted']['message'], Payload::uuid4());
            $payload->setContext($record['formatted']['context']);
            foreach ($record['formatted']['extra'] as $key => $value) {
                $payload->addTag($key, $value);
            }
            $this->logfile->log($payload);
        }
    }
}
