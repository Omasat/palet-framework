<?php

declare(strict_types=1);

namespace Palet\Framework\Scaffold;

class EnvironmentInitializer
{
    public function initialize(string $targetPath): void
    {
        $envExample = <<<ENV
APP_NAME=Palet
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=palet
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
ENV;

        $envFile = $targetPath . DIRECTORY_SEPARATOR . '.env';
        $envExampleFile = $targetPath . DIRECTORY_SEPARATOR . '.env.example';

        file_put_contents($envExampleFile, $envExample);

        // Generate a simple app key for demonstration
        $appKey = 'base64:' . base64_encode(random_bytes(32));
        $envContent = str_replace('APP_KEY=', 'APP_KEY=' . $appKey, $envExample);
        
        file_put_contents($envFile, $envContent);
    }
}
