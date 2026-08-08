<?php

declare(strict_types=1);

namespace Palet\Framework\Http\Kernel;

use Palet\Framework\Contracts\Http\Message\RequestInterface;
use Palet\Framework\Support\Str;

final readonly class RequestContext
{
    public string $requestId;
    public string $traceId;
    public string $clientIp;
    public string $method;
    public string $path;
    public float $startTime;

    public function __construct(
        string $requestId,
        string $traceId,
        string $clientIp,
        string $method,
        string $path,
        float $startTime
    ) {
        $this->requestId = $requestId;
        $this->traceId = $traceId;
        $this->clientIp = $clientIp;
        $this->method = $method;
        $this->path = $path;
        $this->startTime = $startTime;
    }

    public static function fromRequest(RequestInterface $request): self
    {
        $server = $request->getServerParams();
        
        $traceId = $request->hasHeader('X-Trace-Id') 
            ? $request->getHeaderLine('X-Trace-Id') 
            : Str::uuid();
            
        $clientIp = $server['REMOTE_ADDR'] ?? '127.0.0.1';
        
        if ($request->hasHeader('X-Forwarded-For')) {
            $ips = explode(',', $request->getHeaderLine('X-Forwarded-For'));
            $clientIp = trim($ips[0]);
        }
        
        $startTime = isset($server['REQUEST_TIME_FLOAT']) 
            ? (float) $server['REQUEST_TIME_FLOAT'] 
            : microtime(true);

        return new self(
            Str::uuid(),
            $traceId,
            $clientIp,
            $request->getMethod(),
            $request->getUri()->getPath(),
            $startTime
        );
    }
}
