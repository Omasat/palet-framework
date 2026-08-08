<?php

declare(strict_types=1);

namespace Tests\Generator\Scaffold;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Generator\Scaffold\BlueprintValidator;
use Palet\Framework\Contracts\Generator\Scaffold\BlueprintInterface;
use InvalidArgumentException;

class BlueprintValidatorTest extends TestCase
{
    public function test_validates_correct_blueprint()
    {
        $blueprint = new class implements BlueprintInterface {
            public function getName(): string { return 'test'; }
            public function getDescription(): string { return ''; }
            public function getSteps(): array { return ['module', 'entity']; }
            public function getDependencies(): array { return ['entity' => ['module']]; }
        };

        $validator = new BlueprintValidator();
        $this->assertTrue($validator->validate($blueprint));
    }
    
    public function test_throws_on_empty_steps()
    {
        $blueprint = new class implements BlueprintInterface {
            public function getName(): string { return 'test'; }
            public function getDescription(): string { return ''; }
            public function getSteps(): array { return []; }
            public function getDependencies(): array { return []; }
        };

        $validator = new BlueprintValidator();
        $this->expectException(InvalidArgumentException::class);
        $validator->validate($blueprint);
    }
    
    public function test_throws_on_self_circular_dependency()
    {
        $blueprint = new class implements BlueprintInterface {
            public function getName(): string { return 'test'; }
            public function getDescription(): string { return ''; }
            public function getSteps(): array { return ['entity']; }
            public function getDependencies(): array { return ['entity' => ['entity']]; }
        };

        $validator = new BlueprintValidator();
        $this->expectException(InvalidArgumentException::class);
        $validator->validate($blueprint);
    }
}
