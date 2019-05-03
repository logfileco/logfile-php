<?php declare(strict_types=1);

namespace Logfile;

use Monolog\Formatter\FormatterInterface;

class MonologFormatter implements FormatterInterface
{
    /**
     * {@inherit}
     */
    public function format(array $record)
    {
        return $record;
    }

    /**
     * {@inherit}
     */
    public function formatBatch(array $records)
    {
        return array_map([$this, 'format'], $records);
    }
}
