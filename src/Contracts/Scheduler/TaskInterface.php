<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Scheduler;

interface TaskInterface
{
    public function run(): void;
    public function getId(): string;
}
