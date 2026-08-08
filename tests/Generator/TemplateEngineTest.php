<?php

declare(strict_types=1);

namespace Tests\Generator;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Generator\TemplateEngine;
use Palet\Framework\Generator\PlaceholderResolver;

class TemplateEngineTest extends TestCase
{
    public function test_compiles_template_with_if_blocks()
    {
        $engine = new TemplateEngine(new PlaceholderResolver());
        
        $template = <<<STUB
<?php
@if(HasNamespace)
namespace {{ Namespace }};
@endif

class {{ ClassName }} {}
STUB;

        $variables = [
            'HasNamespace' => true,
            'Namespace' => 'App\\Tests',
            'ClassName' => 'Sample'
        ];
        
        $compiled = $engine->compile($template, $variables);
        
        $this->assertStringContainsString('namespace App\\Tests;', $compiled);
        $this->assertStringContainsString('class Sample {}', $compiled);
        
        // Test false condition
        $variables['HasNamespace'] = false;
        $compiled2 = $engine->compile($template, $variables);
        
        $this->assertStringNotContainsString('namespace', $compiled2);
        $this->assertStringContainsString('class Sample {}', $compiled2);
    }
}
