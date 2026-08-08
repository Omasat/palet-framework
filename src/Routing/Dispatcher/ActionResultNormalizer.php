<?php

declare(strict_types=1);

namespace Palet\Framework\Routing\Dispatcher;

use Palet\Framework\Contracts\Http\Message\ResponseInterface;
use Palet\Framework\Http\Message\Response;

class ActionResultNormalizer
{
    public function normalize(mixed $result): ResponseInterface
    {
        if ($result instanceof ResponseInterface) {
            return $result;
        }

        if ($result instanceof \Palet\Framework\Contracts\View\ViewInterface) {
            $result = $result->render();
        } elseif ($result instanceof \Stringable) {
            $result = (string) $result;
        }

        if (is_array($result) || (is_object($result) && !($result instanceof \Palet\Framework\Contracts\View\ViewInterface) && !($result instanceof \Stringable))) {
            // @todo Use a real JsonResponse class in the future
            return new Response(
                200, 
                ['Content-Type' => 'application/json'], 
                json_encode($result)
            );
        }

        if (is_string($result)) {
            return new Response(200, ['Content-Type' => 'text/html; charset=UTF-8'], $result);
        }

        if ($result === null) {
            return new Response(204);
        }
        
        // Fallback for integers, floats, booleans, etc.
        return new Response(200, ['Content-Type' => 'text/html; charset=UTF-8'], (string) $result);
    }
}
