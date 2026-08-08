<?php

declare(strict_types=1);

namespace Palet\Framework\Http\Message;

use Palet\Framework\Contracts\Http\Message\StreamInterface;
use Palet\Framework\Contracts\Http\Message\UploadedFileInterface;
use RuntimeException;
use InvalidArgumentException;

class UploadedFile implements UploadedFileInterface
{
    protected ?StreamInterface $stream = null;
    protected ?string $file;
    protected bool $moved = false;

    public function __construct(
        protected mixed $streamOrFile,
        protected ?int $size,
        protected int $errorStatus,
        protected ?string $clientFilename = null,
        protected ?string $clientMediaType = null
    ) {
        if (is_string($streamOrFile)) {
            $this->file = $streamOrFile;
        } elseif ($streamOrFile instanceof StreamInterface) {
            $this->stream = $streamOrFile;
        } else {
            throw new InvalidArgumentException('Invalid stream or file provided.');
        }
    }

    public function getStream(): StreamInterface
    {
        if ($this->moved) {
            throw new RuntimeException('Cannot retrieve stream after it has been moved.');
        }

        if ($this->stream) {
            return $this->stream;
        }

        $this->stream = new Stream($this->file, 'r');
        return $this->stream;
    }

    public function moveTo(string $targetPath): void
    {
        if ($this->moved) {
            throw new RuntimeException('Cannot move file. It has already been moved.');
        }

        if (empty($targetPath)) {
            throw new InvalidArgumentException('Invalid path provided for move operation.');
        }

        if ($this->file) {
            $this->moved = PHP_SAPI === 'cli'
                ? rename($this->file, $targetPath)
                : move_uploaded_file($this->file, $targetPath);
        } else {
            $stream = $this->getStream();
            if ($stream->isSeekable()) {
                $stream->rewind();
            }

            $dest = new Stream($targetPath, 'w');
            while (!$stream->eof()) {
                if (!$dest->write($stream->read(1048576))) {
                    break;
                }
            }
            $dest->close();
            $this->moved = true;
        }

        if (!$this->moved) {
            throw new RuntimeException('Error occurred while moving uploaded file.');
        }
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function getError(): int
    {
        return $this->errorStatus;
    }

    public function getClientFilename(): ?string
    {
        return $this->clientFilename;
    }

    public function getClientMediaType(): ?string
    {
        return $this->clientMediaType;
    }
}
