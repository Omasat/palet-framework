<?php

declare(strict_types=1);

namespace Palet\Framework\Organization\Events;

use Palet\Framework\Contracts\Organization\BranchInterface;

class BranchCreated
{
    public function __construct(public readonly BranchInterface $branch) {}
}
