<?php

declare(strict_types=1);

namespace Palet\Framework\Diagnostics\IDE;

use Palet\Framework\Contracts\Diagnostics\IDEIntegrationInterface;

class IDEHelperGenerator implements IDEIntegrationInterface
{
    public function generate(string $basePath): void
    {
        $content = "<?php\n\n// @formatter:off\n// phpcs:ignoreFile\n\n";
        $content .= "/**\n";
        $content .= " * A helper file for Palet Framework, to provide autocomplete information to your IDE.\n";
        $content .= " * Generated for PhpStorm and VSCode.\n";
        $content .= " *\n";
        $content .= " * @author Palet Framework\n";
        $content .= " */\n\n";
        
        $content .= "namespace Palet\\Framework\\Support\\Facades {\n";
        $content .= "    class Cache {\n";
        $content .= "        public static function get(\$key, \$default = null) {}\n";
        $content .= "        public static function put(\$key, \$value, \$ttl = null) {}\n";
        $content .= "    }\n";
        
        $content .= "    class Route {\n";
        $content .= "        public static function get(\$uri, \$action) {}\n";
        $content .= "        public static function post(\$uri, \$action) {}\n";
        $content .= "    }\n";
        $content .= "}\n";

        $filePath = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '_ide_helper.php';
        file_put_contents($filePath, $content);
    }
}
