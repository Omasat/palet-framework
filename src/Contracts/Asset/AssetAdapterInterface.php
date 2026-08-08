<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Asset;

use Palet\Framework\Contracts\View\Html\HtmlStringInterface;

interface AssetAdapterInterface
{
    /**
     * Generate HTML tags for the given assets.
     */
    public function __invoke(string|array $assets): HtmlStringInterface;
}
