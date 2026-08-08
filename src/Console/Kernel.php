<?php

declare(strict_types=1);

namespace Palet\Framework\Console;

use Palet\Framework\Contracts\Console\KernelInterface;
use Palet\Framework\Contracts\Console\InputInterface;
use Palet\Framework\Contracts\Console\OutputInterface;
use Palet\Framework\Console\Input\ArgvInput;
use Palet\Framework\Console\Output\ConsoleOutput;
use Throwable;

class Kernel implements KernelInterface
{
    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function handle(InputInterface $input, ?OutputInterface $output = null): int
    {
        $output = $output ?? new ConsoleOutput();
        
        try {
            return $this->app->run($input, $output);
        } catch (Throwable $e) {
            return $this->renderException($e, $output);
        }
    }

    public function terminate(InputInterface $input, int $status): void
    {
        exit($status);
    }

    protected function renderException(Throwable $e, OutputInterface $output): int
    {
        $output->writeln("<error>{$e->getMessage()}</error>");
        
        return 1;
    }
}
