<?php

declare(strict_types=1);

namespace Tests\Generator\Scaffold;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Generator\Scaffold\GenerationPipeline;
use Palet\Framework\Generator\Scaffold\ScaffoldContext;

class GenerationPipelineTest extends TestCase
{
    public function test_processes_steps_in_order()
    {
        $context = new ScaffoldContext('test_blueprint');
        $pipeline = new GenerationPipeline($context);
        
        $executed = [];
        
        $pipeline->registerHandler('step1', function($ctx) use (&$executed) {
            $executed[] = 'step1';
            return ['file1.php'];
        });
        
        $pipeline->registerHandler('step2', function($ctx) use (&$executed) {
            $executed[] = 'step2';
            return ['file2.php'];
        });
        
        $pipeline->process(['step1', 'step2']);
        
        $this->assertEquals(['step1', 'step2'], $executed);
        
        $files = $context->getGeneratedFiles();
        $this->assertContains('file1.php', $files);
        $this->assertContains('file2.php', $files);
    }
}
