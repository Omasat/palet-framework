<?php

declare(strict_types=1);

namespace Palet\Framework\Scaffold\Templates;

class ApiTemplate extends BaseTemplate
{
    public function getName(): string
    {
        return 'api';
    }

    public function getDirectoryStructure(): array
    {
        $directories = parent::getDirectoryStructure();
        
        // Remove views and public assets related dirs for pure API
        $directories = array_filter($directories, function ($dir) {
            return !in_array($dir, [
                'resources/views',
                'storage/framework/views'
            ]);
        });
        
        return $directories;
    }

    public function getFiles(): array
    {
        return array_merge(parent::getFiles(), [
            'routes/api.php' => $this->getApiRoutes(),
        ]);
    }

    protected function getApiRoutes(): string
    {
        return <<<PHP
<?php

use Palet\Framework\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'framework' => 'Palet']);
});
PHP;
    }
}
