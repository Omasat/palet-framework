<?php

declare(strict_types=1);

namespace Palet\Framework\Environment;

use RuntimeException;

class EnvLoader
{
    protected EnvParser $parser;
    protected EnvRepository $repository;
    protected string $path;
    protected string $file;

    public function __construct(string $path, string $file = '.env', ?EnvParser $parser = null)
    {
        $this->path = rtrim($path, DIRECTORY_SEPARATOR);
        $this->file = $file;
        $this->parser = $parser ?? new EnvParser();
        $this->repository = new EnvRepository();
    }

    /**
     * Dosyayı okur, parse eder ve Repository içine yükler.
     *
     * @return EnvRepository
     * @throws RuntimeException
     */
    public function load(): EnvRepository
    {
        $filePath = $this->path . DIRECTORY_SEPARATOR . $this->file;

        if (!file_exists($filePath) || !is_readable($filePath)) {
            // .env dosyası zorunlu olmayabilir, ancak varsa okunabilir olmalıdır.
            return $this->repository;
        }

        $content = file_get_contents($filePath);

        if ($content === false) {
            throw new RuntimeException("Unable to read the environment file at: {$filePath}");
        }

        $parsed = $this->parser->parse($content);

        foreach ($parsed as $key => $value) {
            $this->repository->set($key, $value);
        }

        return $this->repository;
    }
}
