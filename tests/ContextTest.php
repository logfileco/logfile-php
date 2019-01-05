<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

class ContextTest extends TestCase
{
    protected $file;

    public function setup()
    {
        $this->file = tempnam(sys_get_temp_dir(), 'PHPUnitContext');
        $handle = fopen($this->file, 'w+');
        if (false === $handle) {
            throw new \ErrorException('Failed to open file: '.$this->file);
        }
        foreach (range(1, 40) as $line) {
            fwrite($handle, 'Line '.$line."\n");
        }
        $stat = fstat($handle);
        ftruncate($handle, $stat['size'] - 1);
        fclose($handle);
    }

    public function tearDown()
    {
        unlink($this->file);
    }

    public function testContextTop()
    {
        $context = new \Logfile\Context($this->file, 1);
        $this->assertCount(5, $context->getPlaceInFile());
    }

    public function testContextMiddle()
    {
        $context = new \Logfile\Context($this->file, 10);
        $this->assertCount(9, $context->getPlaceInFile());
    }

    public function testContextBottom()
    {
        $context = new \Logfile\Context($this->file, 40);
        $this->assertCount(5, $context->getPlaceInFile());
    }
}
