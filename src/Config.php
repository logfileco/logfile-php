<?php declare(strict_types=1);

namespace Logfile;

class Config
{
    protected $path = '';

    protected $tags = [];

    protected $user = [];

    protected $release = '';

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

    public function hasTags(): bool
    {
        return !empty($this->tags);
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }

    public function addTag(string $key, $value): void
    {
        $this->tags[$key] = $value;
    }

    public function hasUser(): bool
    {
        return !empty($this->user);
    }

    public function getUser(): array
    {
        return $this->user;
    }

    public function setUser(array $user): void
    {
        $this->user = $user;
    }

    public function hasRelease(): bool
    {
        return !empty($this->release);
    }

    public function getRelease(): string
    {
        return $this->release;
    }

    public function setRelease(string $release)
    {
        $this->release = $release;
    }
}
