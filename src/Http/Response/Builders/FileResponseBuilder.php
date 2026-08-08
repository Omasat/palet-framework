<?php

declare(strict_types=1);

namespace Palet\Framework\Http\Response\Builders;

use InvalidArgumentException;
use finfo;

class FileResponseBuilder extends AbstractResponseBuilder
{
    protected string $file;

    public function __construct(string $file)
    {
        $realPath = realpath($file);
        
        if ($realPath === false || !is_file($realPath)) {
            throw new InvalidArgumentException("File not found at path: [{$file}]");
        }
        
        $this->file = $realPath;
        
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($this->file);
        
        $this->headers['Content-Type'] = $mime ?: 'application/octet-stream';
        $this->headers['Content-Disposition'] = 'inline; filename="' . basename($this->file) . '"';
    }

    protected function getContent(): string
    {
        // For very large files, we'd want StreamResponseBuilder.
        // For now, file_get_contents is acceptable for testing basic binary output.
        return file_get_contents($this->file) ?: '';
    }
}
