Do not configure php.ini to load php_imagick.dll from this project path.

Reasons:
- PHP + Windows need ImageMagick's own DLLs (CORE_RL_*.dll, etc.) on PATH or next to php.exe;
  a random folder under the repo is a common cause of "No se puede encontrar el módulo especificado".
- Repo-relative paths break on other machines and after deploy.

Correct setup:
1. Copy php_imagick.dll into your PHP ext folder (e.g. C:\php8.2\ext\).
2. In php.ini use:  extension=imagick   (or extension=php_imagick.dll)
3. Install ImageMagick for Windows and add its bin directory to the system PATH.
4. For PDF support, install Ghostscript and ensure it is on PATH.

See: tools/test_imagick_pdf_to_png.php
