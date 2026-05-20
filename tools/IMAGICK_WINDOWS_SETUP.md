# Imagick on Windows (PHP 8.2 NTS x64)

## What was wrong

`extension_loaded('imagick')` was false because either:

1. **`php_imagick.dll` was missing** from `extension_dir` (`C:\php8.2\ext\`), or  
2. **`php_imagick.dll` could not load its dependencies** — it needs **ImageMagick’s own DLLs** (e.g. `CORE_RL_*.dll`) on the DLL search path. A plain “module not found” / “No se puede encontrar el módulo especificado” often means this, not a missing `php_imagick.dll`.

## What this repo expects

- **PECL DLL** for your exact PHP build (e.g. PHP 8.2 **NTS** x64, VS16):  
  https://windows.php.net/downloads/pecl/releases/imagick/3.8.1/php_imagick-3.8.1-8.2-nts-vs16-x64.zip  
  Unzip and copy **`php_imagick.dll`** into **`C:\php8.2\ext\`** (or your PHP `ext` folder).

- **`php.ini`** (e.g. `C:\php8.2\php.ini`):  
  `extension=imagick`  
  (Do **not** point `extension=` at a path under this git repo.)

- **ImageMagick** installed (e.g. via winget: `ImageMagick.ImageMagick`). Note the install folder, e.g.:  
  `C:\Program Files\ImageMagick-7.1.2-Q16-HDRI`

- **PATH**: that folder must be on **PATH** for any process that runs `php.exe`, **or** use the launcher below.

## Launcher (no global PATH change)

From the repo:

```bat
tools\php-with-imagemagick.cmd -m
tools\php-with-imagemagick.cmd tools\test_imagick_pdf_to_png.php
```

Edit `IMAGEMAGICK_HOME` inside `php-with-imagemagick.cmd` if your ImageMagick path differs.

## Permanent fix

**Settings → System → About → Advanced system settings → Environment Variables** → edit **Path** (User or System) → add:

`C:\Program Files\ImageMagick-7.1.2-Q16-HDRI`

Then **restart** terminals and Cursor so `php` picks up Imagick without the wrapper.

## Version mismatch warning

If you see a warning that Imagick was compiled against ImageMagick **1809** but **1810** is loaded, it is usually safe; for a quiet match, align ImageMagick minor versions with the PECL build notes or try another imagick PECL zip.

## PDF

Install **Ghostscript** and ensure its `bin` folder is on PATH so ImageMagick can read PDFs.
