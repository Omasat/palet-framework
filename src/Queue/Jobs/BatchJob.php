<?php

declare(strict_types=1);

namespace Palet\Framework\Queue\Jobs;

use Palet\Framework\Queue\QueueManager;

class BatchJob extends QueueableJob
{
    /** @var QueueableJob[] */
    protected array $jobs = [];
    
    /** @var QueueManager|null */
    protected ?QueueManager $manager = null;

    public function __construct(array $jobs)
    {
        parent::__construct();
        $this->jobs = $jobs;
    }
    
    public function setManager(QueueManager $manager): void
    {
        $this->manager = $manager;
    }

    public function handle(): void
    {
        if (!$this->manager) {
            return;
        }

        foreach ($this->jobs as $job) {
            $this->manager->push($job, $this->queue);
        }
    }
}
