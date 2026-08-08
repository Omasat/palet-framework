<?php

declare(strict_types=1);

namespace Palet\Framework\Log;

use Psr\Log\AbstractLogger;
use Palet\Framework\Contracts\Log\LogDriverInterface;
use Palet\Framework\Foundation\Exceptions\SecurityMasker;
use Stringable;

class LogManager extends AbstractLogger
{
    /**
     * @var array<string, LogDriverInterface>
     */
    protected array $channels = [];

    protected string $defaultChannel;
    protected LoggerFactory $factory;
    protected SecurityMasker $masker;

    public function __construct(string $defaultChannel, LoggerFactory $factory, ?SecurityMasker $masker = null)
    {
        $this->defaultChannel = $defaultChannel;
        $this->factory = $factory;
        $this->masker = $masker ?? new SecurityMasker();
    }

    /**
     * Logları asıl yazacak olan sürücüye gönderir.
     */
    public function log($level, string|Stringable $message, array $context = []): void
    {
        $driver = $this->channel();
        
        // Security Masking
        if (!empty($context)) {
            $context = $this->masker->mask($context);
        }

        $driver->write((string) $level, $message, $context);
    }

    /**
     * Get a log channel instance.
     */
    public function channel(?string $channel = null): LogDriverInterface
    {
        $channel = $channel ?? $this->defaultChannel;

        if (!isset($this->channels[$channel])) {
            $this->channels[$channel] = $this->factory->createDriver($channel);
        }

        return $this->channels[$channel];
    }
}
