<?php declare(strict_types=1);

namespace Logfile;

trait PathTrait
{
    protected $path = '';

    public function hasPath(): bool
    {
        return !empty($this->path);
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $realpath = realpath($path);

        if (false === $realpath) {
            throw new \InvalidArgumentException('Path does not exist: '.$path);
        }

        $this->path = $realpath . '/';
    }
}
