<?php declare(strict_types=1);

namespace Logfile;

class Frame
{
    protected $file;

    protected $line;

    protected $caller;

    protected $args;

    public static function create(array $params): self
    {
        $frame = new self;

        if (isset($params['file'])) {
            $frame->setFile($params['file']);
        }

        if (isset($params['line'])) {
            $frame->setLine($params['line']);
        }

        if (isset($params['class'])) {
            $frame->setCaller(\sprintf('%s%s%s', $params['class'], $params['type'], $params['function']));
        } elseif (isset($params['function'])) {
            $frame->setCaller(\sprintf('%s(anonymous)', $params['function']));
        } else {
            $frame->setCaller('{main}');
        }

        if (isset($params['args'])) {
            $frame->setArguments($params['args']);
        } else {
            $frame->setArguments([]);
        }

        return $frame;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function setFile(string $file): void
    {
        $this->file = $file;
    }

    public function hasFile(): bool
    {
        return $this->file !== null && \is_readable($this->file);
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function setLine(int $line): void
    {
        $this->line = $line;
    }

    public function hasLine(): bool
    {
        return $this->line !== null;
    }

    public function getCaller(): string
    {
        return $this->caller;
    }

    public function setCaller(string $caller): void
    {
        $this->caller = $caller;
    }

    public function getArguments(): array
    {
        return $this->args;
    }

    public function setArguments(array $args): void
    {
        $this->args = [];

        foreach (\array_values($args) as $index => $arg) {
            $this->args['param'.($index + 1)] = $this->normalise($arg);
        }
    }

    protected function normalise($value): string
    {
        if ($value === null) {
            return 'null';
        } elseif ($value === false) {
            return 'false';
        } elseif ($value === true) {
            return 'true';
        } elseif (is_float($value) && (int) $value == $value) {
            return $value.'.0';
        } elseif (is_integer($value) || is_float($value)) {
            return (string) $value;
        } elseif (is_object($value) || gettype($value) == 'object') {
            return 'Object '.get_class($value);
        } elseif (is_resource($value)) {
            return 'Resource '.get_resource_type($value);
        } elseif (is_array($value)) {
            return 'Array of length ' . count($value);
        }

        $truncation = new Truncation($value);
        return $truncation->truncate();
    }

    public function hasContext(): bool
    {
        return $this->hasFile() && $this->hasLine();
    }

    public function getContext(): Context
    {
        return new Context($this->getFile(), $this->getLine());
    }

    public function toArray(): array
    {
        $frame = [];

        if ($this->hasFile()) {
            $frame['file'] = $this->getFile();
        }

        if ($this->hasLine()) {
            $frame['line'] = $this->getLine();
        }

        $frame['caller'] = $this->getCaller();
        $frame['args'] = $this->getArguments();

        if ($this->hasContext()) {
            $frame['context'] = $this->getContext()->getPlaceInFile();
        }

        return $frame;
    }
}
