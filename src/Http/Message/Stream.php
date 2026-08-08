<?php

declare(strict_types=1);

namespace Palet\Framework\Http\Message;

use Palet\Framework\Contracts\Http\Message\StreamInterface;
use RuntimeException;
use InvalidArgumentException;

class Stream implements StreamInterface
{
    /** @var resource|null */
    protected $stream;
    
    protected bool $seekable;
    protected bool $readable;
    protected bool $writable;

    public function __construct(mixed $stream, string $mode = 'r')
    {
        if (is_string($stream)) {
            $stream = fopen($stream, $mode);
        }
        
        if (!is_resource($stream) || get_resource_type($stream) !== 'stream') {
            throw new InvalidArgumentException('Stream must be a valid stream resource.');
        }

        $this->stream = $stream;
        $meta = stream_get_meta_data($this->stream);
        $this->seekable = $meta['seekable'];
        $this->readable = (bool) preg_match('/[r+]/', $meta['mode']);
        $this->writable = (bool) preg_match('/[w+a+x+c+]/', $meta['mode']);
    }

    public function __toString(): string
    {
        try {
            if ($this->isSeekable()) {
                $this->rewind();
            }
            return $this->getContents();
        } catch (\Throwable $e) {
            return '';
        }
    }

    public function close(): void
    {
        if (isset($this->stream)) {
            fclose($this->stream);
            $this->detach();
        }
    }

    public function detach()
    {
        $result = $this->stream;
        $this->stream = null;
        $this->readable = $this->writable = $this->seekable = false;
        
        return $result;
    }

    public function getSize(): ?int
    {
        if (!isset($this->stream)) {
            return null;
        }

        $stats = fstat($this->stream);
        return $stats['size'] ?? null;
    }

    public function tell(): int
    {
        if (!isset($this->stream)) {
            throw new RuntimeException('Stream is detached');
        }

        $result = ftell($this->stream);
        if ($result === false) {
            throw new RuntimeException('Unable to determine stream position');
        }

        return $result;
    }

    public function eof(): bool
    {
        return !$this->stream || feof($this->stream);
    }

    public function isSeekable(): bool
    {
        return $this->seekable;
    }

    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        if (!isset($this->stream)) {
            throw new RuntimeException('Stream is detached');
        }
        if (!$this->seekable) {
            throw new RuntimeException('Stream is not seekable');
        }
        if (fseek($this->stream, $offset, $whence) === -1) {
            throw new RuntimeException('Unable to seek to stream position ' . $offset);
        }
    }

    public function rewind(): void
    {
        $this->seek(0);
    }

    public function isWritable(): bool
    {
        return $this->writable;
    }

    public function write(string $string): int
    {
        if (!isset($this->stream)) {
            throw new RuntimeException('Stream is detached');
        }
        if (!$this->writable) {
            throw new RuntimeException('Cannot write to a non-writable stream');
        }

        $result = fwrite($this->stream, $string);
        if ($result === false) {
            throw new RuntimeException('Unable to write to stream');
        }

        return $result;
    }

    public function isReadable(): bool
    {
        return $this->readable;
    }

    public function read(int $length): string
    {
        if (!isset($this->stream)) {
            throw new RuntimeException('Stream is detached');
        }
        if (!$this->readable) {
            throw new RuntimeException('Cannot read from non-readable stream');
        }
        if ($length < 0) {
            throw new RuntimeException('Length parameter cannot be negative');
        }
        if (0 === $length) {
            return '';
        }

        $string = fread($this->stream, $length);
        if ($string === false) {
            throw new RuntimeException('Unable to read from stream');
        }

        return $string;
    }

    public function getContents(): string
    {
        if (!isset($this->stream)) {
            throw new RuntimeException('Stream is detached');
        }
        if (!$this->readable) {
            throw new RuntimeException('Cannot read from non-readable stream');
        }

        $contents = stream_get_contents($this->stream);
        if ($contents === false) {
            throw new RuntimeException('Unable to read stream contents');
        }

        return $contents;
    }

    public function getMetadata(?string $key = null): mixed
    {
        if (!isset($this->stream)) {
            return $key ? null : [];
        }

        $meta = stream_get_meta_data($this->stream);
        
        if (is_null($key)) {
            return $meta;
        }

        return $meta[$key] ?? null;
    }
}
