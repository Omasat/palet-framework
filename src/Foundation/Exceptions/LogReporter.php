<?php

declare(strict_types=1);

namespace Palet\Framework\Foundation\Exceptions;

use Palet\Framework\Contracts\Debug\ExceptionReporterInterface;
use Throwable;

class LogReporter implements ExceptionReporterInterface
{
    protected ?\Psr\Log\LoggerInterface $logger;

    public function __construct(?\Psr\Log\LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * Report or log an exception.
     */
    public function report(Throwable $e): void
    {
        $message = sprintf(
            "[%s] %s in %s:%d",
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        );

        if ($this->logger !== null) {
            $this->logger->error($message, ['exception' => $e]);
        } else {
            error_log($message);
        }
    }
}
