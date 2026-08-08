<?php

declare(strict_types=1);

namespace Palet\Framework\Scaffold\Templates;

class WebTemplate extends BaseTemplate
{
    public function getName(): string
    {
        return 'web';
    }

    public function getFiles(): array
    {
        return array_merge(parent::getFiles(), [
            'routes/web.php' => $this->getWebRoutes(),
            'resources/views/welcome.palet.php' => $this->getWelcomeView(),
        ]);
    }

    protected function getWebRoutes(): string
    {
        return <<<PHP
<?php

use Palet\Framework\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
PHP;
    }

    protected function getWelcomeView(): string
    {
        return <<<HTML
<!DOCTYPE html>
<html>
    <head>
        <title>Palet Framework</title>
        <style>
            body { font-family: sans-serif; text-align: center; margin-top: 20%; background-color: #f8f9fa; color: #333; }
            h1 { font-size: 3rem; margin-bottom: 10px; }
            p { font-size: 1.2rem; color: #666; }
        </style>
    </head>
    <body>
        <h1>Palet Framework</h1>
        <p>Your web application is ready.</p>
    </body>
</html>
HTML;
    }
}
