<?php

declare(strict_types=1);

namespace Palet\Framework\View;

use Palet\Framework\Contracts\View\ViewFactoryInterface;
use Palet\Framework\Contracts\View\ViewFinderInterface;
use Palet\Framework\Contracts\View\ViewInterface;
use Palet\Framework\Contracts\View\EngineInterface;
use InvalidArgumentException;

class Factory implements ViewFactoryInterface
{
    protected ViewFinderInterface $finder;
    protected array $shared = [];
    protected array $engines = []; // Ext: extension => EngineInterface

    public function __construct(ViewFinderInterface $finder)
    {
        $this->finder = $finder;
    }

    public function exists(string $view): bool
    {
        try {
            $this->finder->find($view);
            return true;
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }

    public function make(string $view, array $data = []): ViewInterface
    {
        $path = $this->finder->find($view);

        $engine = $this->getEngineFromPath($path);

        return new View($this, $engine, $view, $path, $data);
    }

    public function share(string|array $key, mixed $value = null): mixed
    {
        $keys = is_array($key) ? $key : [$key => $value];

        foreach ($keys as $k => $v) {
            $this->shared[$k] = $v;
        }

        return $value;
    }

    public function getShared(): array
    {
        return $this->shared;
    }

    public function addEngine(string $extension, EngineInterface $engine): void
    {
        $this->engines[$extension] = $engine;
    }

    protected function getEngineFromPath(string $path): EngineInterface
    {
        foreach ($this->engines as $extension => $engine) {
            if (str_ends_with($path, '.' . $extension)) {
                return $engine;
            }
        }

        throw new InvalidArgumentException("Unrecognized extension in file: {$path}");
    }

    // Layout and Section Management
    protected array $sections = [];
    protected array $sectionStack = [];

    public function startSection(string $section): void
    {
        if (ob_start()) {
            $this->sectionStack[] = $section;
        }
    }

    public function stopSection(bool $overwrite = false): string
    {
        if (empty($this->sectionStack)) {
            throw new InvalidArgumentException('Cannot stop a section without first starting one.');
        }

        $last = array_pop($this->sectionStack);

        if ($overwrite || !isset($this->sections[$last])) {
            $this->sections[$last] = ob_get_clean();
        } else {
            $this->sections[$last] .= ob_get_clean();
        }

        return $last;
    }

    public function yieldContent(string $section, string $default = ''): string
    {
        return $this->sections[$section] ?? $default;
    }

    // Component Management
    protected array $componentStack = [];
    protected array $slotStack = [];

    public function startComponent(string $name, array $data = []): void
    {
        if (ob_start()) {
            $this->componentStack[] = [
                'name' => $name,
                'data' => $data,
                'slots' => []
            ];
        }
    }

    public function renderComponent(): string
    {
        $component = array_pop($this->componentStack);
        
        $view = $this->make($component['name'], $component['data']);
        
        $slotContent = ob_get_clean();
        
        // Pass the default slot and named slots
        $view->with('slot', new Components\Slot($slotContent));
        
        foreach ($component['slots'] as $slotName => $slotHtml) {
            $view->with($slotName, new Components\Slot($slotHtml));
        }
        
        // Convert attributes
        if (!isset($component['data']['attributes'])) {
            $view->with('attributes', new Components\AttributeBag($component['data']));
        }
        
        return $view->render();
    }

    public function slot(string $name): void
    {
        if (ob_start()) {
            $this->slotStack[] = $name;
        }
    }

    public function endSlot(): void
    {
        $name = array_pop($this->slotStack);
        $content = ob_get_clean();
        
        if (!empty($this->componentStack)) {
            $lastComponentIndex = count($this->componentStack) - 1;
            $this->componentStack[$lastComponentIndex]['slots'][$name] = $content;
        }
    }

    // Asset Management
    protected ?\Palet\Framework\Contracts\Asset\AssetManagerInterface $assetManager = null;

    public function setAssetManager(\Palet\Framework\Contracts\Asset\AssetManagerInterface $assetManager): void
    {
        $this->assetManager = $assetManager;
    }

    public function vite(string|array $assets): \Palet\Framework\Contracts\View\Html\HtmlStringInterface
    {
        if ($this->assetManager === null) {
            throw new \RuntimeException('Asset manager has not been configured on the view factory.');
        }

        return $this->assetManager->resolve($assets);
    }
}
