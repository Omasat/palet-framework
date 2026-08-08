<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Commands\Generator\Entity;

use Palet\Framework\Console\Command;
use Palet\Framework\Console\Attributes\AsCommand;
use Palet\Framework\Generator\CodeGenerator;
use Palet\Framework\Generator\TemplateEngine;
use Palet\Framework\Generator\PlaceholderResolver;
use Palet\Framework\Generator\FileGenerator;
use Palet\Framework\Generator\Entity\EntityGenerator;
use Palet\Framework\Generator\Entity\NamingConventionResolver;
use Palet\Framework\Generator\Entity\DomainNamespaceResolver;
use Palet\Framework\Generator\Entity\GeneratorProfileManager;
use Palet\Framework\Generator\Entity\DDDProfile;
use RuntimeException;

#[AsCommand('make:entity', 'Create a new Entity and associated domain files')]
class MakeEntityCommand extends Command
{
    protected function execute(): int
    {
        $name = $this->argument('0') ?? $this->argument('name');
        
        if (!$name) {
            $this->error('Entity name is required.');
            return 1;
        }

        $all = $this->option('all') ?? false;
        $dryRun = $this->option('dry-run') ?? false;
        $force = $this->option('force') ?? false;

        $profileManager = new GeneratorProfileManager();
        $profileManager->register(new DDDProfile());

        $components = ['entity'];
        
        if ($all) {
            $components = $profileManager->getProfile('ddd')->getDefaultComponents();
        } else {
            if ($this->option('repository')) $components[] = 'repository';
            if ($this->option('service')) $components[] = 'service';
            if ($this->option('dto')) $components[] = 'dto';
            if ($this->option('repository-interface') || $this->option('repository')) {
                // If repository is chosen, interface is typically needed in DDD
                if (!in_array('repository_interface', $components)) {
                    $components[] = 'repository_interface';
                }
            }
        }

        $basePath = getcwd() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Domain';

        $codeGenerator = new CodeGenerator(
            new TemplateEngine(new PlaceholderResolver()),
            new FileGenerator()
        );

        $generator = new EntityGenerator(
            $codeGenerator,
            new NamingConventionResolver(),
            new DomainNamespaceResolver('App\\Domain'),
            $basePath
        );

        try {
            $generator->generateBulk($name, [
                'components' => $components,
                'dryRun' => $dryRun,
                'force' => $force
            ]);
            
            if ($dryRun) {
                $this->info("DRY RUN: Domain files would be created for {$name}.");
            } else {
                $this->info("Domain components created successfully for {$name}.");
            }
            
            return 0;
            
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());
            return 1;
        }
    }
}
