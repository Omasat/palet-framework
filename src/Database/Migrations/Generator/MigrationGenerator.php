<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Migrations\Generator;

use Palet\Framework\Contracts\Database\Migrations\Generator\MigrationGeneratorInterface;
use Palet\Framework\Contracts\Database\Migrations\Generator\MigrationNamingStrategyInterface;
use Palet\Framework\Generator\CodeGenerator;
use Palet\Framework\Generator\GeneratorContext;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Database\Migrations\Events\MigrationGenerating;
use Palet\Framework\Database\Migrations\Events\MigrationGenerated;
use Palet\Framework\Database\Migrations\Events\MigrationGenerationFailed;

class MigrationGenerator implements MigrationGeneratorInterface
{
    protected CodeGenerator $codeGenerator;
    protected MigrationNamingStrategyInterface $nameResolver;
    protected ?EventDispatcherInterface $events = null;

    public function __construct(CodeGenerator $codeGenerator, ?MigrationNamingStrategyInterface $nameResolver = null)
    {
        $this->codeGenerator = $codeGenerator;
        $this->nameResolver = $nameResolver ?? new MigrationNameResolver();
    }

    public function setEventDispatcher(EventDispatcherInterface $events): void
    {
        $this->events = $events;
        // Delegate to underlying code generator
        $this->codeGenerator->setEventDispatcher($events);
    }

    public function generate(string $name, string $destinationDir, ?string $table = null, bool $create = false, bool $dryRun = false): ?string
    {
        if ($this->events) {
            $this->events->dispatch(new MigrationGenerating($name, $table, $create));
        }

        try {
            // Determine table and create from name if not explicitly provided
            if (!$table) {
                $analysis = $this->nameResolver->analyze($name);
                $table = $analysis['table'];
                if (!$create && $analysis['create']) {
                    $create = true;
                }
            }

            // Determine stub
            $stubName = 'migration.blank.stub';
            if ($table) {
                $stubName = $create ? 'migration.create.stub' : 'migration.update.stub';
            }

            $stubPath = __DIR__ . '/../Stubs/' . $stubName;

            // Generate filename with timestamp
            $timestamp = date('Y_m_d_His');
            $fileName = $timestamp . '_' . $name . '.php';
            $destinationPath = $destinationDir . DIRECTORY_SEPARATOR . $fileName;

            // Prepare Class Name (CamelCase version of name)
            $className = str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));

            $variables = [
                'ClassName' => $className,
                'TableName' => $table ?? '',
            ];

            $context = new GeneratorContext($stubPath, $destinationPath, $variables, false, $dryRun);
            
            $this->codeGenerator->generate($context);

            if ($this->events) {
                $this->events->dispatch(new MigrationGenerated($destinationPath, $dryRun));
            }

            return $dryRun ? null : $destinationPath;

        } catch (\Throwable $e) {
            if ($this->events) {
                $this->events->dispatch(new MigrationGenerationFailed($name, $e));
            }
            throw $e;
        }
    }
}
