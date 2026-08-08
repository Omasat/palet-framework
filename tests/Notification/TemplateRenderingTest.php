<?php

declare(strict_types=1);

namespace Tests\Notification;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Notification\Templates\TemplateEngine;

class TemplateRenderingTest extends TestCase
{
    public function test_template_engine_replaces_variables()
    {
        $engine = new TemplateEngine();
        
        $engine->registerTemplate('welcome', 'Hello {{ name }}, your balance is {{balance}}.');
        
        $rendered = $engine->render('welcome', [
            'name' => 'John',
            'balance' => '500'
        ]);
        
        $this->assertEquals('Hello John, your balance is 500.', $rendered);
    }
}
