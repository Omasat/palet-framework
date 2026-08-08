<?php

declare(strict_types=1);

namespace Tests\Foundation\Exceptions;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Foundation\Application;
use Palet\Framework\Foundation\Exceptions\Handler;
use RuntimeException;

class HandlerTest extends TestCase
{
    public function test_should_report_returns_true_for_default_exceptions()
    {
        $app = new Application(__DIR__);
        $handler = new class($app) extends Handler {
            public function testShouldReport($e) {
                return $this->shouldReport($e);
            }
        };

        $this->assertTrue($handler->testShouldReport(new RuntimeException()));
    }

    public function test_should_report_returns_false_for_dont_report_exceptions()
    {
        $app = new Application(__DIR__);
        $handler = new class($app) extends Handler {
            protected array $dontReport = [
                RuntimeException::class
            ];
            
            public function testShouldReport($e) {
                return $this->shouldReport($e);
            }
        };

        $this->assertFalse($handler->testShouldReport(new RuntimeException()));
    }

    public function test_is_fatal_detects_fatal_errors()
    {
        $app = new Application(__DIR__);
        $handler = new class($app) extends Handler {
            public function testIsFatal($type) {
                return $this->isFatal($type);
            }
        };

        $this->assertTrue($handler->testIsFatal(E_ERROR));
        $this->assertTrue($handler->testIsFatal(E_PARSE));
        $this->assertTrue($handler->testIsFatal(E_CORE_ERROR));
        $this->assertTrue($handler->testIsFatal(E_COMPILE_ERROR));

        $this->assertFalse($handler->testIsFatal(E_WARNING));
        $this->assertFalse($handler->testIsFatal(E_NOTICE));
    }
}
