<?php

declare(strict_types=1);

namespace Tests\View\Components;

use PHPUnit\Framework\TestCase;
use Palet\Framework\View\Compiler\Component\ComponentTagCompiler;

class ComponentTagCompilerTest extends TestCase
{
    protected ComponentTagCompiler $compiler;

    protected function setUp(): void
    {
        $this->compiler = new ComponentTagCompiler();
    }

    public function test_compiles_self_closing_tags()
    {
        $template = '<x-alert type="error" class="mt-4" />';
        $compiled = $this->compiler->compile($template);
        
        $expected = "<?php \$__env->startComponent('alert', ['type' => 'error', 'class' => 'mt-4', ]); ?>\n<?php echo \$__env->renderComponent(); ?>";
        $this->assertEquals($expected, $compiled);
    }

    public function test_compiles_opening_and_closing_tags()
    {
        $template = "<x-alert>\nSome content\n</x-alert>";
        $compiled = $this->compiler->compile($template);
        
        $expected = "<?php \$__env->startComponent('alert', []); ?>\nSome content\n<?php echo \$__env->renderComponent(); ?>";
        $this->assertEquals($expected, $compiled);
    }

    public function test_compiles_bound_attributes()
    {
        $template = '<x-alert :message="$message" />';
        $compiled = $this->compiler->compile($template);
        
        $expected = "<?php \$__env->startComponent('alert', ['message' => \$message, ]); ?>\n<?php echo \$__env->renderComponent(); ?>";
        $this->assertEquals($expected, $compiled);
    }

    public function test_compiles_slots()
    {
        $template = "<x-slot name=\"header\">\nTitle\n</x-slot>";
        $compiled = $this->compiler->compile($template);
        
        $expected = "<?php \$__env->slot('header'); ?>\nTitle\n<?php \$__env->endSlot(); ?>";
        $this->assertEquals($expected, $compiled);
    }
}
