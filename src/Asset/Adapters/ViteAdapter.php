<?php

declare(strict_types=1);

namespace Palet\Framework\Asset\Adapters;

use Palet\Framework\Contracts\Asset\AssetAdapterInterface;
use Palet\Framework\Contracts\Asset\DevServerInterface;
use Palet\Framework\Contracts\View\Html\HtmlStringInterface;
use Palet\Framework\Asset\AssetManifest;
use Palet\Framework\View\Html\HtmlString;
use Palet\Framework\View\Html\HtmlElement;

class ViteAdapter implements AssetAdapterInterface
{
    protected DevServerInterface $devServer;
    protected AssetManifest $manifest;
    protected string $buildDirectory;

    public function __construct(DevServerInterface $devServer, AssetManifest $manifest, string $buildDirectory = '/build')
    {
        $this->devServer = $devServer;
        $this->manifest = $manifest;
        $this->buildDirectory = rtrim($buildDirectory, '/');
    }

    public function __invoke(string|array $assets): HtmlStringInterface
    {
        $assets = (array) $assets;

        if ($this->devServer->isRunning()) {
            return $this->devServerAssets($assets);
        }

        return $this->manifestAssets($assets);
    }

    protected function devServerAssets(array $assets): HtmlStringInterface
    {
        $url = rtrim($this->devServer->url(), '/');
        
        $tags = [
            $this->makeScriptTag($url . '/@vite/client', ['type' => 'module'])
        ];

        foreach ($assets as $asset) {
            if ($this->isCssPath($asset)) {
                $tags[] = $this->makeStyleTag($url . '/' . ltrim($asset, '/'));
            } else {
                $tags[] = $this->makeScriptTag($url . '/' . ltrim($asset, '/'), ['type' => 'module']);
            }
        }

        return new HtmlString(implode("\n", $tags));
    }

    protected function manifestAssets(array $assets): HtmlStringInterface
    {
        $tags = [];

        foreach ($assets as $asset) {
            $chunk = $this->manifest->get($asset);
            
            if (!$chunk) {
                throw new \RuntimeException("Unable to locate file in Vite manifest: {$asset}");
            }

            $tags[] = $this->makeTagForChunk($chunk);

            // Also load css files associated with this JS chunk
            if (isset($chunk['css'])) {
                foreach ($chunk['css'] as $css) {
                    $tags[] = $this->makeStyleTag($this->buildDirectory . '/' . $css);
                }
            }
        }

        return new HtmlString(implode("\n", $tags));
    }

    protected function makeTagForChunk(array $chunk): string
    {
        $file = $this->buildDirectory . '/' . $chunk['file'];

        if ($this->isCssPath($file)) {
            return $this->makeStyleTag($file);
        }

        return $this->makeScriptTag($file, ['type' => 'module']);
    }

    protected function isCssPath(string $path): bool
    {
        return preg_match('/\.(css|less|sass|scss|styl|stylus|pcss|postcss)$/', $path) === 1;
    }

    protected function makeScriptTag(string $url, array $attributes = []): string
    {
        $attributes['src'] = $url;
        return (new HtmlElement('script', '', $attributes))->toHtml();
    }

    protected function makeStyleTag(string $url, array $attributes = []): string
    {
        $attributes['rel'] = 'stylesheet';
        $attributes['href'] = $url;
        return (new HtmlElement('link', '', $attributes))->toHtml();
    }
}
