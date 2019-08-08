<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

class FrameTest extends TestCase
{
    public function testFilename()
    {
        $frame = new \Logfile\Inspection\Frame;

        $frame->setFile(__FILE__);
        $this->assertEquals(__FILE__, $frame->getFile());
        $this->assertTrue($frame->hasFile());
        $this->assertEquals(__FILE__, $frame->getRelativeFilepath());
    }

    public function testPathAndRelativeFilepath()
    {
        $frame = new \Logfile\Inspection\Frame;

        $frame->setFile(__FILE__);
        $frame->setPath(__DIR__.'/../tests');
        $this->assertEquals(__DIR__ .'/', $frame->getPath());
        $this->assertTrue($frame->hasPath());
        $this->assertEquals(basename(__FILE__), $frame->getRelativeFilepath());
    }

    public function testLineNumber()
    {
        $frame = new \Logfile\Inspection\Frame;

        $frame->setLine(1);
        $this->assertEquals(1, $frame->getLine());
        $this->assertTrue($frame->hasLine());
    }

    public function testCaller()
    {
        $frame = new \Logfile\Inspection\Frame;

        $frame->setCaller('foo');
        $this->assertEquals('foo', $frame->getCaller());
        $this->assertTrue($frame->hasCaller());
    }

    public function testArguments()
    {
        $frame = new \Logfile\Inspection\Frame;

        $frame->setArguments(['foo']);
        $this->assertEquals(['param1'=> 'foo'], $frame->getArguments());
    }

    public function testManyArguments()
    {
        $frame = new \Logfile\Inspection\Frame;

        $frame->setArguments([range(1, 101)]);
        $this->assertEquals(['param1'=> 'Array of length 101'], $frame->getArguments());
    }

    public function testArgumentsTypes()
    {
        $frame = new \Logfile\Inspection\Frame;

        $frame->setArguments([['foo', 'bar']]);
        $this->assertEquals(['param1'=> 'Array<string> of length 2'], $frame->getArguments());

        $frame->setArguments([[new \StdClass]]);
        $this->assertEquals(['param1'=> 'Array<stdClass> of length 1'], $frame->getArguments());

        $frame->setArguments([[1, '2', false]]);
        $this->assertEquals(['param1'=> 'Array<integer|string|boolean> of length 3'], $frame->getArguments());

        $frame->setArguments([[1, '2', false, null]]);
        $this->assertEquals(['param1'=> 'Mixed Array of length 4'], $frame->getArguments());

        $frame->setArguments([true]);
        $this->assertEquals(['param1'=> 'true'], $frame->getArguments());

        $frame->setArguments([false]);
        $this->assertEquals(['param1'=> 'false'], $frame->getArguments());

        $frame->setArguments([null]);
        $this->assertEquals(['param1'=> 'null'], $frame->getArguments());

        $frame->setArguments([1]);
        $this->assertEquals(['param1'=> '1'], $frame->getArguments());

        $frame->setArguments([1.0]);
        $this->assertEquals(['param1'=> '1.0'], $frame->getArguments());

        $frame->setArguments([new \StdClass]);
        $this->assertEquals(['param1'=> 'Object stdClass'], $frame->getArguments());

        $frame->setArguments([stream_context_create()]);
        $this->assertEquals(['param1'=> 'Resource stream-context'], $frame->getArguments());
    }

    public function testContext()
    {
        $frame = new \Logfile\Inspection\Frame;
        $frame->setFile(__FILE__);
        $frame->setLine(1);
        $this->assertTrue($frame->hasContext());
        $this->assertTrue($frame->getContext() instanceof \Logfile\Inspection\Context);
    }

    public function testArray()
    {
        $frame = new \Logfile\Inspection\Frame;
        $frame->setFile(__FILE__);
        $frame->setLine(1);
        $frame->setCaller('foo');
        $this->assertTrue(is_array($frame->toArray()));
    }

    public function testCreate()
    {
        $frame = \Logfile\Inspection\Frame::create([
            'file' => __FILE__,
            'line' => 1,
            'class' => 'stdClass',
            'type' => '->',
            'function' => '__construct'
        ]);
        $this->assertTrue($frame instanceof \Logfile\Inspection\Frame);

        $frame = \Logfile\Inspection\Frame::create([
            'file' => __FILE__,
            'line' => 1,
            'function' => 'isset',
            'args' => [true],
        ]);
        $this->assertTrue($frame instanceof \Logfile\Inspection\Frame);
    }
}
