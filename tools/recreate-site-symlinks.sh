#!/usr/bin/env bash
# Recreate site -> global symlinks (macOS / Linux). Windows equivalent: recreate-site-junctions.ps1
#
# After clone/pull with core.symlinks=false, Git may not check out links; run:
#   bash tools/recreate-site-symlinks.sh
#
# Does not touch imagenes/ (see tools/IMAGENES-SYMLINK-REQUIRED.txt).
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
GLOBAL="$ROOT/global"

if [[ ! -d "$GLOBAL/ajax" ]]; then
	echo "error: global/ajax not found (expected repo root: $ROOT)" >&2
	exit 1
fi

SITES=(elite huskies lidep nuestrodeporte vollidep voleibalmetepec aztflag)
LINKS=(ajax assets config css Form include javascript languages objects)

remove_path() {
	local p="$1"
	if [[ -L "$p" ]]; then
		rm "$p"
	elif [[ -e "$p" ]]; then
		rm -rf "$p"
	fi
}

created=0
skipped=0

for site in "${SITES[@]}"; do
	site_dir="$ROOT/$site"
	if [[ ! -d "$site_dir" ]]; then
		echo "warn: skip missing site: $site_dir" >&2
		continue
	fi
	for link in "${LINKS[@]}"; do
		target_global="$GLOBAL/$link"
		link_path="$site_dir/$link"
		if [[ ! -e "$target_global" ]]; then
			echo "warn: skip missing target: $target_global" >&2
			((skipped++)) || true
			continue
		fi
		remove_path "$link_path"
		# Relative path matches Git (mode 120000 -> ../global/...)
		ln -s "../global/$link" "$link_path"
		((created++)) || true
	done
done

echo "Symlinks OK: $created link(s) under $ROOT ($skipped skipped)"
