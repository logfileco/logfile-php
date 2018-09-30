<?php declare(strict_types=1);

namespace Logfile;

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

        $file = new SplFileObject($this->file, 'rb');

        $offset = \max(0, ($this->line - ($lines + 1)));
        $file->seek((int) $offset);

        $line = $offset + 1;

        while (!$file->eof()) {
            $context[$line] = $file->current();

            if ($line > ($this->line + ($lines - 1))) {
                break;
            }

            $file->next();

            $line++;
        }

        return $context;
    }
}
