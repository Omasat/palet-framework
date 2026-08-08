<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Tenancy\Provisioning;

interface WorkspaceTemplateInterface
{
    public function getName(): string;
    public function getDefaultConfig(): array;
    public function getRequiredServices(): array;
}
