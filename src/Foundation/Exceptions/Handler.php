<?php

declare(strict_types=1);

namespace Palet\Framework\Foundation\Exceptions;

use Palet\Framework\Contracts\Debug\ExceptionHandlerInterface;
use Palet\Framework\Contracts\Debug\ExceptionReporterInterface;
use Palet\Framework\Contracts\Foundation\ApplicationInterface;
use ErrorException;
use Throwable;

class Handler implements ExceptionHandlerInterface
{
    protected ApplicationInterface $app;
    
    /**
     * A list of the exception types that should not be reported.
     * @var array<int, class-string<Throwable>>
     */
    protected array $dontReport = [];

    public function __construct(ApplicationInterface $app)
    {
        $this->app = $app;
    }

    /**
     * Register the error handling callbacks for the application.
     */
    public function register(): void
    {
        error_reporting(-1);

        set_error_handler(function (int $level, string $message, string $file = '', int $line = 0): bool {
            if (error_reporting() & $level) {
                throw new ErrorException($message, 0, $level, $file, $line);
            }
            return true;
        });

        set_exception_handler(function (Throwable $e) {
            $this->handleException($e);
        });

        register_shutdown_function(function () {
            $this->handleShutdown();
        });
    }

    /**
     * Unregister the error handling callbacks.
     */
    public function unregister(): void
    {
        restore_error_handler();
        restore_exception_handler();
    }

    /**
     * Handle an uncaught exception.
     */
    public function handleException(Throwable $e): void
    {
        try {
            $this->report($e);
        } catch (Throwable $reportError) {
            // Ignored if reporter fails during exception handling
        }

        if (php_sapi_name() === 'cli' || php_sapi_name() === 'phpdbg') {
            $this->renderForConsole($e);
        } else {
            $output = $this->render($e);
            
            if (is_array($output)) {
                header('Content-Type: application/json');
                echo json_encode($output);
            } else {
                echo $output;
            }
        }
    }

    /**
     * Handle the PHP shutdown event.
     */
    public function handleShutdown(): void
    {
        $error = error_get_last();

        if ($error !== null && $this->isFatal($error['type'])) {
            $this->handleException(new ErrorException(
                $error['message'], 0, $error['type'], $error['file'], $error['line']
            ));
        }
    }

    /**
     * Determine if the error type is fatal.
     */
    protected function isFatal(int $type): bool
    {
        return in_array($type, [E_COMPILE_ERROR, E_CORE_ERROR, E_ERROR, E_PARSE]);
    }

    /**
     * Determine if the exception should be reported.
     */
    protected function shouldReport(Throwable $e): bool
    {
        foreach ($this->dontReport as $type) {
            if ($e instanceof $type) {
                return false;
            }
        }

        return true;
    }

    /**
     * Report or log an exception.
     */
    public function report(Throwable $e): void
    {
        if ($this->shouldReport($e)) {
            $logger = null;
            if (method_exists($this->app, 'make') && $this->app->has('log')) {
                $logger = $this->app->make('log');
            }
            
            $reporter = new LogReporter($logger);
            $reporter->report($e);
        }
    }

    /**
     * Determine if the application is in debug mode.
     */
    protected function isDebugMode(): bool
    {
        if (method_exists($this->app, 'make') && $this->app->has('config')) {
            return (bool) $this->app->make('config')->get('app.debug', false);
        }

        // Default fallback if config is not booted
        return false;
    }

    /**
     * Render an exception into an HTTP response string or array.
     */
    public function render(Throwable $e): string|array
    {
        // For API/JSON requests (simple check based on Accept header for now)
        $wantsJson = isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json');

        if ($wantsJson) {
            $renderer = new JsonErrorRenderer();
            return json_decode($renderer->render($e, $this->isDebugMode()), true);
        }

        $renderer = new HtmlErrorRenderer();
        return $renderer->render($e, $this->isDebugMode());
    }

    /**
     * Render an exception to the console.
     */
    public function renderForConsole(Throwable $e): void
    {
        $renderer = new CliErrorRenderer();
        echo $renderer->render($e, $this->isDebugMode());
    }
}
