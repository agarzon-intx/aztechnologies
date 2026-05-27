@echo off
REM Put ImageMagick on PATH before starting PHP so php_imagick.dll can resolve CORE_RL_*.dll.
REM Adjust IMAGEMAGICK_HOME if your install folder name/version differs.
set "IMAGEMAGICK_HOME=C:\Program Files\ImageMagick-7.1.2-Q16-HDRI"
if not exist "%IMAGEMAGICK_HOME%\magick.exe" (
  echo ERROR: ImageMagick not found at: %IMAGEMAGICK_HOME%
  echo Edit IMAGEMAGICK_HOME in this script or add ImageMagick to your system PATH.
  exit /b 1
)
set "PATH=%IMAGEMAGICK_HOME%;%PATH%"
"C:\php8.2\php.exe" %*
