<?php

declare(strict_types=1);

namespace Palet\Framework\Generator\Module;

use Palet\Framework\Generator\CodeGenerator;
use Palet\Framework\Generator\GeneratorContext;

class ModuleManifestGenerator
{
    protected CodeGenerator $codeGenerator;

    public function __construct(CodeGenerator $codeGenerator)
    {
        $this->codeGenerator = $codeGenerator;
    }

    public function generate(string $moduleName, string $modulePath, bool $dryRun = false): void
    {
        $stubPath = __DIR__ . '/Stubs/module.json.stub';
        $destinationPath = $modulePath . DIRECTORY_SEPARATOR . 'module.json';

        $variables = [
            'ModuleName' => $moduleName,
            'ModuleVersion' => '1.0.0'
        ];

        $context = new GeneratorContext($stubPath, $destinationPath, $variables, false, $dryRun);
        $this->codeGenerator->generate($context);
        
        $providerStub = __DIR__ . '/Stubs/module_service_provider.stub';
        $providerDestination = $modulePath . DIRECTORY_SEPARATOR . 'Providers' . DIRECTORY_SEPARATOR . $moduleName . 'ServiceProvider.php';
        
        $variables['Namespace'] = "Modules\\{$moduleName}\\Providers";
        $variables['ClassName'] = $moduleName . 'ServiceProvider';
        
        $providerContext = new GeneratorContext($providerStub, $providerDestination, $variables, false, $dryRun);
        $this->codeGenerator->generate($providerContext);
    }
}
