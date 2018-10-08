<?php declare(strict_types=1);

namespace Logfile;

interface DataInterface
{
    public function getTags(): array;

    public function getUser(): array;

    public function getRelease(): string;
}
