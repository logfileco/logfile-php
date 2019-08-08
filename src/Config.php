<?php declare(strict_types=1);

namespace Logfile;

class Config
{
    use Traits\Path;

    protected $tags = [];

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
        return !empty($this->tags['user']);
    }

    public function getUser(): array
    {
        return $this->tags['user'] ?? [];
    }

    public function setUser(array $user): void
    {
        $this->tags['user'] = $user;
    }

    public function hasRelease(): bool
    {
        return !empty($this->tags['release']);
    }

    public function getRelease(): string
    {
        return $this->tags['release'] ?? '';
    }

    public function setRelease(string $release)
    {
        $this->tags['release'] = $release;
    }
}
