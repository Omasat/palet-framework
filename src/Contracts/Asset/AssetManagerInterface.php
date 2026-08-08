<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Asset;

use Palet\Framework\Contracts\View\Html\HtmlStringInterface;

interface AssetManagerInterface
{
    /**
     * Resolve the given assets using the configured adapter.
     */
    public function resolve(string|array $assets): HtmlStringInterface;

    /**
     * Set the current active adapter.
     */
    public function setAdapter(AssetAdapterInterface $adapter): void;

    /**
     * Get the current active adapter.
     */
    public function getAdapter(): AssetAdapterInterface;
}
