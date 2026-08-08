<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Seeders;

class SeederRunner
{
    public function run(string $class): void
    {
        $seeder = $this->resolve($class);
        $seeder->setRunner($this);
        $seeder->run();
    }
    
    protected function resolve(string $class): Seeder
    {
        // Container can be used here later
        return new $class;
    }
}
