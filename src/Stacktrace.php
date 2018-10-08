<?php declare(strict_types=1);

namespace Logfile;

use Throwable;

class Stacktrace
{
    use PathTrait;

    protected $exception;

    public function __construct(Throwable $exception = null)
    {
        $this->exception = $exception;
    }

    public function getTrace(): array
    {
        $frames = $this->exception->getTrace();

        if (!isset($frames[0]['file']) || $frames[0]['file'] !== $this->exception->getFile()) {
            array_unshift($frames, [
                'file' => $this->exception->getFile(),
                'line' => $this->exception->getLine(),
            ]);
        }

        return $frames;
    }

    public function getFrames(): array
    {
        $frames = [];

        foreach ($this->getTrace() as $params) {
            $frame = Frame::create($params);
            $frame->setPath($this->getPath());
            $frames[] = $frame->toArray();
        }

        return $frames;
    }
}
