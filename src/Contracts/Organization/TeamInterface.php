<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Organization;

interface TeamInterface
{
    public function getId(): string|int;
    public function getName(): string;
    public function getDepartmentId(): string|int;
}
