<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Workflow;

interface ProcessInterface
{
    public function start(): void;
    public function suspend(): void;
    public function resume(): void;
    public function cancel(): void;
}
