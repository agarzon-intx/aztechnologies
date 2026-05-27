#!/usr/bin/env bash
# Fix XAMPP 403 on macOS when the project lives under ~/...
# Apache must be allowed to *traverse* each directory in the path.
#
# Run in Terminal (not Cursor sandbox):
#   bash tools/xampp-fix-403-macos.sh
set -euo pipefail

HOME_DIR="${HOME:-/Users/aztechnologies}"
PATHS=(
	"$HOME_DIR"
	"$HOME_DIR/Desarrollo"
	"$HOME_DIR/Desarrollo/Aztechnologies"
	"$HOME_DIR/Desarrollo/Aztechnologies/WEB"
)

echo "Home folder is usually drwx------ for 'others' — Apache runs as a different user."
echo "Adding traverse (o+x) so Apache can reach your WEB folder..."
echo ""

for p in "${PATHS[@]}"; do
	if [[ ! -d "$p" ]]; then
		echo "skip (missing): $p"
		continue
	fi
	before=$(ls -ld "$p")
	chmod o+x "$p" || {
		echo "FAILED: $p — run this script in Terminal.app (may need Full Disk Access for Terminal)"
		exit 1
	}
	echo "OK: $p"
	echo "    was: $before"
done

echo ""
echo "Restart Apache in XAMPP, then open http://elite.test"
echo "If still 403: System Settings → Privacy & Security → Full Disk Access → enable XAMPP (or Terminal)."
