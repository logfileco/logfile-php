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

    protected function getTrace(): array
    {
        if ($this->exception) {
            return $this->exception->getTrace();
        }
        return \debug_backtrace();
    }

    public function getFrames(): array
    {
        $frames = [];

        foreach ($this->getTrace() as $frame) {
            $frames[] = Frame::create($frame);
        }

        return $frames;
    }
}
