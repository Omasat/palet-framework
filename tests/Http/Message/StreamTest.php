<?php

declare(strict_types=1);

namespace Tests\Http\Message;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Http\Message\Stream;
use RuntimeException;

class StreamTest extends TestCase
{
    public function test_stream_reads_and_writes()
    {
        $stream = new Stream('php://temp', 'r+');
        $stream->write('hello world');
        
        $this->assertEquals(11, $stream->getSize());
        
        $stream->rewind();
        $this->assertEquals('hello world', $stream->getContents());
    }

    public function test_tostring_rewinds_and_reads()
    {
        $stream = new Stream('php://temp', 'r+');
        $stream->write('test');
        
        $this->assertEquals('test', (string) $stream);
    }

    public function test_close_and_detach()
    {
        $stream = new Stream('php://temp', 'r+');
        $stream->close();
        
        $this->expectException(RuntimeException::class);
        $stream->getContents();
    }
}
