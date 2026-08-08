<?php

declare(strict_types=1);

namespace Palet\Framework\View;

use Palet\Framework\Contracts\View\ViewInterface;
use Palet\Framework\Contracts\View\EngineInterface;

class View implements ViewInterface
{
    protected Factory $factory;
    protected EngineInterface $engine;
    protected string $view;
    protected string $path;
    protected array $data;

    public function __construct(Factory $factory, EngineInterface $engine, string $view, string $path, array $data = [])
    {
        $this->factory = $factory;
        $this->engine = $engine;
        $this->view = $view;
        $this->path = $path;
        $this->data = $data;
    }

    public function render(): string
    {
        $data = $this->gatherData();

        $contents = $this->engine->get($this->path, $data);

        return $contents;
    }

    public function name(): string
    {
        return $this->view;
    }

    public function with(string|array $key, mixed $value = null): self
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    protected function gatherData(): array
    {
        $data = array_merge($this->factory->getShared(), $this->data);
        $data['__env'] = $this->factory;
        return $data;
    }
}
