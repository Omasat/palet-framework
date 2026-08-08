<?php

declare(strict_types=1);

namespace Palet\Framework\Foundation\Bootstrap;

use Palet\Framework\Contracts\Foundation\ApplicationInterface;

class RegisterExceptionHandler implements BootstrapperInterface
{
    public function bootstrap(ApplicationInterface $app): void
    {
        error_reporting(-1);

        set_error_handler(function (int $level, string $message, string $file = '', int $line = 0) {
            if (error_reporting() & $level) {
                throw new \ErrorException($message, 0, $level, $file, $line);
            }
        });

        set_exception_handler(function (\Throwable $e) {
            $this->handleException($e);
        });

        register_shutdown_function(function () {
            $error = error_get_last();
            if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE], true)) {
                $this->handleException(new \ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']));
            }
        });
    }

    protected function handleException(\Throwable $e): void
    {
        if (php_sapi_name() === 'cli') {
            echo "Fatal Error: " . $e->getMessage() . "\n";
            echo "In " . $e->getFile() . " on line " . $e->getLine() . "\n";
            echo $e->getTraceAsString();
        } else {
            http_response_code(500);
            echo "<h1>Internal Server Error</h1>";
            echo "<p><strong>" . get_class($e) . "</strong>: " . $e->getMessage() . "</p>";
            echo "<p>In " . $e->getFile() . " on line " . $e->getLine() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }
    }
}
