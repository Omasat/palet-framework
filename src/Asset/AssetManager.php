<?php

declare(strict_types=1);

namespace Palet\Framework\Asset;

use Palet\Framework\Contracts\Asset\AssetManagerInterface;
use Palet\Framework\Contracts\Asset\AssetAdapterInterface;
use Palet\Framework\Contracts\View\Html\HtmlStringInterface;

class AssetManager implements AssetManagerInterface
{
    protected ?AssetAdapterInterface $adapter = null;

    public function setAdapter(AssetAdapterInterface $adapter): void
    {
        $this->adapter = $adapter;
    }

    public function getAdapter(): AssetAdapterInterface
    {
        if ($this->adapter === null) {
            throw new \RuntimeException('No asset adapter has been configured.');
        }

        return $this->adapter;
    }

    public function resolve(string|array $assets): HtmlStringInterface
    {
        return ($this->getAdapter())($assets);
    }
}
