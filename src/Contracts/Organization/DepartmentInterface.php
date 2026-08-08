<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Organization;

interface DepartmentInterface
{
    public function getId(): string|int;
    public function getName(): string;
    public function getBranchId(): string|int;
}
