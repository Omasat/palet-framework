<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Workflow;

interface ApprovalInterface
{
    public function approve(string|int $userId, ?string $comment = null): void;
    public function reject(string|int $userId, ?string $comment = null): void;
}
