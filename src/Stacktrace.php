<?php declare(strict_types=1);

namespace Logfile;

use Throwable;

class Stacktrace
{
    protected $exception;

    public function __construct(Throwable $exception = null)
    {
        $this->exception = $exception;
    }

    public function getTrace(): array
    {
        $frames = $this->exception->getTrace();

        if (empty($frames)) {
            $frames = [
                [
                    'file' => $this->exception->getFile(),
                    'line' => $this->exception->getLine(),
                ]
            ];
        }

        return $frames;
    }

    public function getFrames(): array
    {
        $frames = [];

        foreach ($this->getTrace() as $frame) {
            $frames[] = Frame::create($frame)->toArray();
        }

        return $frames;
    }
}
