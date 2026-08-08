<?php

declare(strict_types=1);

namespace Palet\Framework\Http\Response\Builders;

class DownloadResponseBuilder extends FileResponseBuilder
{
    public function __construct(string $file, ?string $name = null)
    {
        parent::__construct($file);
        
        $filename = $name ?: basename($this->file);
        
        $this->headers['Content-Disposition'] = 'attachment; filename="' . $filename . '"';
    }
}
