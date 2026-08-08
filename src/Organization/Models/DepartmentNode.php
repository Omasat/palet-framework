<?php

declare(strict_types=1);

namespace Palet\Framework\Organization\Models;

class DepartmentNode extends AbstractNode
{
    public function getType(): string
    {
        return 'department';
    }
}
