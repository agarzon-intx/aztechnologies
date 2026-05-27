<?php
/**
 * Smoke test: PDF first page -> PNG via Imagick.
 *
 * Usage:
 *   php tools/test_imagick_pdf_to_png.php [path/to/input.pdf] [path/to/output.png]
 *
 * Defaults (relative to this script's directory):
 *   input:  tools/document.pdf
 *   output: tools/output_page_1.png
 */
declare(strict_types=1);

$baseDir = __DIR__;
$inputPdf = $argv[1] ?? ($baseDir . DIRECTORY_SEPARATOR . 'document.pdf');
$outputPng = $argv[2] ?? ($baseDir . DIRECTORY_SEPARATOR . 'output_page_1.png');

if (!extension_loaded('imagick')) {
	$ini = php_ini_loaded_file() ?: '(no php.ini loaded)';
	$zts = defined('ZEND_THREAD_SAFE') && ZEND_THREAD_SAFE ? 'ZTS' : 'NTS';
	fwrite(STDERR, "ERROR: PHP Imagick extension is not loaded.\n\n");
	fwrite(STDERR, "The folder global/include/imagick-3.8.1 is PECL *source code* to build php_imagick —\n");
	fwrite(STDERR, "you cannot load it with require/include. You need a compiled php_imagick for this PHP:\n\n");
	fwrite(STDERR, '  PHP_VERSION=' . PHP_VERSION . "\n");
	fwrite(STDERR, "  Thread safe: {$zts}\n");
	fwrite(STDERR, "  php.ini: {$ini}\n\n");
	fwrite(STDERR, "Enable a matching php_imagick DLL in php.ini (extension=imagick or extension=php_imagick.dll),\n");
	fwrite(STDERR, "install ImageMagick on Windows and add its install folder to PATH (so CORE_RL_*.dll can load).\n");
	fwrite(STDERR, "Or run PHP via: tools\\php-with-imagemagick.cmd\n");
	fwrite(STDERR, "Details: tools/IMAGICK_WINDOWS_SETUP.md\n");
	fwrite(STDERR, "See: global/include/imagick-3.8.1/README_NOT_A_PHP_INCLUDE.txt\n");
	exit(1);
}

if (!is_readable($inputPdf)) {
	fwrite(STDERR, "ERROR: PDF not readable: {$inputPdf}\n");
	fwrite(STDERR, "Place a file named document.pdf next to this script, or pass a path as argv[1].\n");
	exit(1);
}

try {
	// 1. Create the Imagick object
	$imagick = new Imagick();

	// 2. Set resolution BEFORE reading the file for higher quality
	$imagick->setResolution(300, 300);

	// 3. Read the first page only (using [0]) to save memory
	$imagick->readImage($inputPdf . '[0]');

	// 4. (Optional) Set background to white (PDFs often have transparent backgrounds)
	$imagick->setImageBackgroundColor(new ImagickPixel('white'));
	$imagick->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
	$imagick = $imagick->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);

	// 5. Convert to PNG and save
	$imagick->setImageFormat('png');
	$imagick->writeImage($outputPng);

	// 6. Clean up
	$imagick->clear();
	$imagick->destroy();

	echo "OK: wrote {$outputPng}\n";
	exit(0);
} catch (ImagickException $e) {
	fwrite(STDERR, 'ImagickException: ' . $e->getMessage() . "\n");
	exit(1);
} catch (Throwable $e) {
	fwrite(STDERR, get_class($e) . ': ' . $e->getMessage() . "\n");
	exit(1);
}
