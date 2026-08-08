<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Workflow;

interface WorkflowInterface
{
    public function getId(): string|int;
    public function getName(): string;
    public function getVersion(): int;
}
