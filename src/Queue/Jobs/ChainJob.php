<?php

declare(strict_types=1);

namespace Palet\Framework\Queue\Jobs;

use Palet\Framework\Contracts\Queue\QueueManagerInterface;
use Palet\Framework\Queue\QueueManager;

class ChainJob extends QueueableJob
{
    /** @var QueueableJob[] */
    protected array $chain = [];
    
    /** @var QueueManager|null */
    protected ?QueueManager $manager = null;

    public function __construct(array $chain)
    {
        parent::__construct();
        $this->chain = $chain;
    }
    
    public function setManager(QueueManager $manager): void
    {
        $this->manager = $manager;
    }

    public function handle(): void
    {
        if (empty($this->chain)) {
            return;
        }

        // Execute the first job
        $currentJob = array_shift($this->chain);
        $currentJob->handle();

        // Dispatch the rest of the chain as a new chain job
        if (!empty($this->chain) && $this->manager) {
            $nextChain = new self($this->chain);
            $nextChain->setManager($this->manager);
            $this->manager->push($nextChain, $this->queue);
        }
    }
}
