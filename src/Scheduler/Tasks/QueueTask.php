<?php

declare(strict_types=1);

namespace Palet\Framework\Scheduler\Tasks;

use Palet\Framework\Contracts\Queue\QueueInterface;
use Palet\Framework\Contracts\Queue\JobInterface;

class QueueTask extends ScheduledTask
{
    public function __construct(
        protected QueueInterface $queue,
        protected JobInterface $job,
        protected string $queueName = 'default'
    ) {
        parent::__construct();
    }

    public function run(): void
    {
        $this->queue->push($this->job, $this->queueName);
    }
}
