<?php

declare(strict_types=1);

namespace Palet\Framework\Generator\Scaffold;

class GenerationManifest
{
    protected string $manifestPath;
    protected array $data = [];

    public function __construct(string $manifestPath)
    {
        $this->manifestPath = $manifestPath;
        $this->load();
    }

    public function record(string $blueprint, array $generatedFiles): void
    {
        $this->data[] = [
            'blueprint' => $blueprint,
            'files' => $generatedFiles,
            'timestamp' => date('Y-m-d H:i:s'),
            'status' => 'completed'
        ];
        
        $this->save();
    }
    
    public function recordFailure(string $blueprint, string $error): void
    {
        $this->data[] = [
            'blueprint' => $blueprint,
            'error' => $error,
            'timestamp' => date('Y-m-d H:i:s'),
            'status' => 'failed'
        ];
        
        $this->save();
    }

    protected function load(): void
    {
        if (file_exists($this->manifestPath)) {
            $content = file_get_contents($this->manifestPath);
            $this->data = json_decode($content, true) ?? [];
        }
    }

    protected function save(): void
    {
        file_put_contents($this->manifestPath, json_encode($this->data, JSON_PRETTY_PRINT));
    }
    
    public function getData(): array
    {
        return $this->data;
    }
}
