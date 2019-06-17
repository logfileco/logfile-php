<?php declare(strict_types=1);

namespace Logfile;

use Throwable;

class Stacktrace
{
    use PathTrait;

    /**
     * @var Throwable
     */
    protected $exception;

    /**
     * @param Throwable
     */
    public function __construct(Throwable $exception)
    {
        $this->exception = $exception;
    }

    protected function inTrace(array $frames): bool
    {
        foreach ($frames as $frame) {
            if (array_key_exists('file', $frame) && $this->exception->getFile() == $frame['file'] &&
                    array_key_exists('line', $frame) && $this->exception->getLine() == $frame['line']) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getTrace(): array
    {
        $frames = $this->exception->getTrace();

        if (!$this->inTrace($frames)) {
            array_unshift($frames, [
                'file' => $this->exception->getFile(),
                'line' => $this->exception->getLine(),
            ]);
        }

        return $frames;
    }

    /**
     * @return array
     */
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
