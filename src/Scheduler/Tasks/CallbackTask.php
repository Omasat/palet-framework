<?php

declare(strict_types=1);

namespace Palet\Framework\Scheduler\Tasks;

class CallbackTask extends ScheduledTask
{
    /** @var callable */
    protected $callback;

    public function __construct(callable $callback)
    {
        parent::__construct();
        $this->callback = $callback;
    }

    public function run(): void
    {
        call_user_func($this->callback);
    }
}
