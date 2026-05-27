#!/usr/bin/env bash
# Link each league site to Valet (*.test). Requires: brew php, composer, valet install.
set -euo pipefail

export PATH="/opt/homebrew/bin:/usr/local/bin:$HOME/.composer/vendor/bin:$PATH"

if ! command -v valet >/dev/null 2>&1; then
	echo "error: valet not found. Install first (see tools/MACOS-DEV-SETUP.md)." >&2
	exit 1
fi

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
SITES=(elite huskies lidep nuestrodeporte vollidep voleibalmetepec)

for site in "${SITES[@]}"; do
	dir="$ROOT/$site"
	if [[ ! -d "$dir" ]]; then
		echo "warn: skip missing $dir" >&2
		continue
	fi
	# Unlink stale name if present, then link this directory -> {site}.test
	( cd "$dir" && valet unlink "$site" 2>/dev/null || true )
	( cd "$dir" && valet link "$site" )
	echo "linked: http://${site}.test -> $dir"
done

echo ""
echo "Done. Run: valet links"
echo "Open: http://elite.test (and other sites)"
