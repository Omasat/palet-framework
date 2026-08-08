<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Seeders;

use Palet\Framework\Contracts\Database\Seeders\SeederInterface;

abstract class Seeder implements SeederInterface
{
    protected ?SeederRunner $runner = null;

    public function setRunner(SeederRunner $runner): static
    {
        $this->runner = $runner;
        return $this;
    }

    public function call(array|string $class): void
    {
        $classes = (array) $class;

        foreach ($classes as $c) {
            if ($this->runner) {
                $this->runner->run($c);
            } else {
                $seeder = new $c;
                $seeder->run();
            }
        }
    }
}
