<?php

declare(strict_types=1);

namespace Palet\Framework\Scaffold\Templates;

use Palet\Framework\Contracts\Scaffold\TemplateInterface;

abstract class BaseTemplate implements TemplateInterface
{
    public function getDirectoryStructure(): array
    {
        return [
            'app',
            'app/Console',
            'app/Console/Commands',
            'app/Exceptions',
            'app/Http',
            'app/Http/Controllers',
            'app/Http/Middleware',
            'app/Models',
            'app/Providers',
            'bootstrap',
            'bootstrap/cache',
            'config',
            'database',
            'database/factories',
            'database/migrations',
            'database/seeders',
            'public',
            'resources',
            'resources/views',
            'routes',
            'storage',
            'storage/app',
            'storage/app/public',
            'storage/framework',
            'storage/framework/cache',
            'storage/framework/sessions',
            'storage/framework/views',
            'storage/logs',
            'tests',
            'tests/Feature',
            'tests/Unit',
        ];
    }

    public function getFiles(): array
    {
        return [
            'public/index.php' => $this->getIndexPhpContent(),
            'tests/CreatesApplication.php' => $this->getCreatesApplicationTrait(),
            'tests/TestCase.php' => $this->getTestCaseClass(),
        ];
    }

    protected function getIndexPhpContent(): string
    {
        return <<<PHP
<?php

define('PALET_START', microtime(true));

// require __DIR__.'/../vendor/autoload.php';

\$app = require_once __DIR__.'/../bootstrap/app.php';

\$kernel = \$app->make(Palet\Framework\Contracts\Http\KernelInterface::class);

\$response = \$kernel->handle(
    \$request = Palet\Framework\Http\Request::capture()
);

\$response->send();

\$kernel->terminate(\$request, \$response);
PHP;
    }

    protected function getCreatesApplicationTrait(): string
    {
        return <<<PHP
<?php

namespace Tests;

use Palet\Framework\Contracts\Console\KernelInterface;

trait CreatesApplication
{
    public function createApplication()
    {
        \$app = require __DIR__.'/../bootstrap/app.php';

        \$app->make(KernelInterface::class)->bootstrap();

        return \$app;
    }
}
PHP;
    }

    protected function getTestCaseClass(): string
    {
        return <<<PHP
<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
}
PHP;
    }
}
