<?php

declare(strict_types=1);

namespace Palet\Framework\Foundation\Kernel;

use Palet\Framework\Contracts\Foundation\ApplicationInterface;
use Palet\Framework\Contracts\Foundation\Kernel\BootableInterface;
use Palet\Framework\Pipeline\Pipeline;
use Throwable;
use RuntimeException;

class BootSequence
{
    protected ApplicationInterface $app;
    
    /**
     * The bootstrap classes for the application.
     * @var array<int, class-string<BootableInterface>>
     */
    protected array $bootstrappers = [];

    public function __construct(ApplicationInterface $app, array $bootstrappers = [])
    {
        $this->app = $app;
        $this->bootstrappers = $bootstrappers;
    }

    public function run(): void
    {
        try {
            $pipeline = new Pipeline($this->app);
            
            $pipeline->send($this->app)
                ->through($this->bootstrappers)
                ->via('bootstrap')
                ->thenReturn();
                
        } catch (Throwable $e) {
            throw new RuntimeException("Failed to bootstrap the application: " . $e->getMessage(), 0, $e);
        }
    }
}
