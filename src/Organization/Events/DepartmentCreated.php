<?php

declare(strict_types=1);

namespace Palet\Framework\Organization\Events;

use Palet\Framework\Contracts\Organization\DepartmentInterface;

class DepartmentCreated
{
    public function __construct(public readonly DepartmentInterface $department) {}
}
