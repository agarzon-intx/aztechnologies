<?php
require_once dirname(__DIR__) . '/global/include/flyer_ci_export.php';

echo 'PHP Imagick extension: ' . (extension_loaded('imagick') ? 'yes' : 'no') . PHP_EOL;
echo 'exec() available: ' . (az_flyer_ci_shell_function_available('exec') ? 'yes' : 'no') . PHP_EOL;
echo 'ImageMagick CLI: ' . (az_flyer_ci_resolve_imagemagick_cli() ?: '(not found)') . PHP_EOL;
echo 'Ghostscript CLI: ' . (az_flyer_ci_resolve_ghostscript_cli() ?: '(not found)') . PHP_EOL;
