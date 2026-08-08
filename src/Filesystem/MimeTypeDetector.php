<?php

declare(strict_types=1);

namespace Palet\Framework\Filesystem;

class MimeTypeDetector
{
    /**
     * Detects the MIME type of a file using finfo.
     *
     * @param string $path Absolute path to the file.
     * @return string|false
     */
    public function detect(string $path): string|false
    {
        if (!file_exists($path)) {
            return false;
        }

        if (class_exists('finfo')) {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            return $finfo->file($path);
        }

        return mime_content_type($path);
    }
}
