<?php

declare(strict_types=1);

namespace Tests\Foundation\Exceptions;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Foundation\Exceptions\HtmlErrorRenderer;
use Palet\Framework\Foundation\Exceptions\CliErrorRenderer;
use Palet\Framework\Foundation\Exceptions\JsonErrorRenderer;
use RuntimeException;

class RendererEnvironmentTest extends TestCase
{
    public function test_html_renderer_production_mode()
    {
        $renderer = new HtmlErrorRenderer();
        $output = $renderer->render(new RuntimeException('Secret error message'), false);

        $this->assertStringContainsString('500 Server Error', $output);
        $this->assertStringNotContainsString('Secret error message', $output);
    }

    public function test_html_renderer_debug_mode()
    {
        $renderer = new HtmlErrorRenderer();
        $output = $renderer->render(new RuntimeException('Secret error message'), true);

        $this->assertStringContainsString('RuntimeException', $output);
        $this->assertStringContainsString('Secret error message', $output);
        $this->assertStringContainsString('Stack Trace', $output);
    }

    public function test_json_renderer_production_mode()
    {
        $renderer = new JsonErrorRenderer();
        $output = $renderer->render(new RuntimeException('Secret error message'), false);
        $json = json_decode($output, true);

        $this->assertEquals('500 Server Error', $json['error']['message']);
        $this->assertArrayNotHasKey('trace', $json['error']);
    }

    public function test_json_renderer_debug_mode()
    {
        $renderer = new JsonErrorRenderer();
        $output = $renderer->render(new RuntimeException('Secret error message'), true);
        $json = json_decode($output, true);

        $this->assertEquals(RuntimeException::class, $json['error']['type']);
        $this->assertEquals('Secret error message', $json['error']['message']);
        $this->assertIsArray($json['error']['trace']);
    }

    public function test_cli_renderer_production_mode()
    {
        $renderer = new CliErrorRenderer();
        $output = $renderer->render(new RuntimeException('Secret error message'), false);

        $this->assertStringContainsString('500 Server Error', $output);
        $this->assertStringNotContainsString('Secret error message', $output);
    }

    public function test_cli_renderer_debug_mode()
    {
        $renderer = new CliErrorRenderer();
        $output = $renderer->render(new RuntimeException('Secret error message'), true);

        $this->assertStringContainsString('RuntimeException', $output);
        $this->assertStringContainsString('Secret error message', $output);
    }
}
