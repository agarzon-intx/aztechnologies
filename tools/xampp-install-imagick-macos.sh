#!/usr/bin/env bash
# Build and enable PHP Imagick for XAMPP on macOS (Option B).
# Run in Terminal.app (needs admin password for copying into XAMPP):
#   bash tools/xampp-install-imagick-macos.sh
set -euo pipefail

XAMPP_ROOT="/Applications/XAMPP/xamppfiles"
XAMPP_PHP="${XAMPP_ROOT}/bin/php"
XAMPP_PECL="${XAMPP_ROOT}/bin/pecl"
XAMPP_PHPIZE="${XAMPP_ROOT}/bin/phpize"
XAMPP_PHP_CONFIG="${XAMPP_ROOT}/bin/php-config"
EXT_DIR="${XAMPP_ROOT}/lib/php/extensions/no-debug-non-zts-20220829"
PHP_INI="${XAMPP_ROOT}/etc/php.ini"

if [[ ! -x "$XAMPP_PHP" ]]; then
	echo "ERROR: XAMPP PHP not found at $XAMPP_PHP"
	exit 1
fi

PHP_MACH="$(file "$XAMPP_PHP" | awk -F': ' '{print $2}')"
echo "XAMPP PHP: $("$XAMPP_PHP" -r 'echo PHP_VERSION;') ($PHP_MACH)"

if echo "$PHP_MACH" | grep -q x86_64; then
	ARCH_PREFIX=(arch -x86_64)
	PHP_ARCH=x86_64
elif echo "$PHP_MACH" | grep -q arm64; then
	ARCH_PREFIX=()
	PHP_ARCH=arm64
else
	echo "ERROR: Unrecognized PHP architecture: $PHP_MACH"
	exit 1
fi

IM_PREFIX=""

install_imagemagick_brew() {
	if [[ "$PHP_ARCH" == x86_64 ]]; then
		if [[ ! -x /usr/local/bin/brew ]]; then
			echo ""
			echo "XAMPP uses Intel (x86_64) PHP. Install Homebrew for Rosetta first (one time, asks password):"
			echo ""
			echo '  arch -x86_64 /bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"'
			echo ""
			echo "Then run this script again."
			exit 1
		fi
		echo "Installing ImageMagick (x86_64 via /usr/local Homebrew)..."
		arch -x86_64 /usr/local/bin/brew install imagemagick pkg-config
		IM_PREFIX="$(arch -x86_64 /usr/local/bin/brew --prefix imagemagick)"
	else
		if ! command -v brew >/dev/null 2>&1; then
			eval "$(/opt/homebrew/bin/brew shellenv)" 2>/dev/null || true
		fi
		if ! command -v brew >/dev/null 2>&1; then
			echo "Install Homebrew first: https://brew.sh"
			exit 1
		fi
		echo "Installing ImageMagick (arm64)..."
		brew install imagemagick pkg-config
		IM_PREFIX="$(brew --prefix imagemagick)"
	fi
	export PKG_CONFIG_PATH="${IM_PREFIX}/lib/pkgconfig:${PKG_CONFIG_PATH:-}"
	echo "ImageMagick prefix: $IM_PREFIX"
}

build_imagick() {
	WORK="$(mktemp -d)"
	trap 'rm -rf "$WORK"' EXIT
	cd "$WORK"

	echo "Downloading imagick PECL source..."
	"${ARCH_PREFIX[@]}" "$XAMPP_PECL" download imagick
	tar xzf imagick-*.tgz
	cd imagick-*

	echo "Building imagick for XAMPP PHP ($PHP_ARCH)..."
	"${ARCH_PREFIX[@]}" "$XAMPP_PHPIZE"
	"${ARCH_PREFIX[@]}" ./configure \
		--with-php-config="$XAMPP_PHP_CONFIG" \
		--with-imagick="$IM_PREFIX"
	"${ARCH_PREFIX[@]}" make -j"$(sysctl -n hw.ncpu 2>/dev/null || echo 2)"

	echo "Installing imagick.so (sudo password required)..."
	sudo "${ARCH_PREFIX[@]}" make install

	if [[ ! -f "$EXT_DIR/imagick.so" ]]; then
		echo "ERROR: imagick.so not found in $EXT_DIR after install"
		exit 1
	fi
}

enable_ini() {
	if grep -qE '^[;[:space:]]*extension=imagick' "$PHP_INI" 2>/dev/null; then
		sudo sed -i '' 's/^[;[:space:]]*extension=imagick.*/extension=imagick/' "$PHP_INI" || true
	elif grep -q 'xampp-php-ext/imagick.so' "$PHP_INI" 2>/dev/null; then
		: # already custom path
	else
		echo "" | sudo tee -a "$PHP_INI" >/dev/null
		echo "; Imagick (tools/xampp-install-imagick-macos.sh)" | sudo tee -a "$PHP_INI" >/dev/null
		echo "extension=imagick" | sudo tee -a "$PHP_INI" >/dev/null
	fi
}

verify() {
	echo ""
	echo "Verifying..."
	"${ARCH_PREFIX[@]}" "$XAMPP_PHP" -m 2>&1 | grep -i imagick || {
		echo "ERROR: imagick did not load. Check warnings above."
		exit 1
	}
	"${ARCH_PREFIX[@]}" "$XAMPP_PHP" -r 'echo Imagick::getVersion()["versionString"], "\n";' 2>&1
	echo ""
	echo "OK. Restart Apache in XAMPP Control, then test flyers or:"
	echo "  ${ARCH_PREFIX[*]} $XAMPP_PHP tools/test-flyer-png-backend.php"
}

install_imagemagick_brew
build_imagick
enable_ini
verify
