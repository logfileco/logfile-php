<?php declare(strict_types=1);

namespace Logfile;

use LimitIterator;
use SplFileObject;

class Context
{
    protected $file;

    protected $line;

    public function __construct(string $file, int $line)
    {
        $this->file = $file;
        $this->line = $line;
    }

    public function getPlaceInFile(int $lines = 4): array
    {
        $context = [];

        $min = $this->line - $lines;

        if ($min < 0) {
            $min = 0;
        }

        $file = new SplFileObject($this->file, 'rb');
        $iterator = new LimitIterator($file, $min, $lines + 1);
        $index = $min;

        foreach ($iterator as $text) {
            $context[$index] = $text;
            $index++;
        }

        return $context;
    }
}
