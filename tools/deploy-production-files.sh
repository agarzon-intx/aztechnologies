#!/usr/bin/env bash
# Upload specific repo files to Production (SFTP). macOS/Linux.
# Requires: python3 + paramiko  →  pip3 install --user paramiko
#
# Usage:
#   bash tools/deploy-production-files.sh global/ajax/Admin/GamesCoach/changeWeeksAdmin.php
set -euo pipefail

REPO="$(cd "$(dirname "$0")/.." && pwd)"
SFTP_PY="$REPO/tools/deploy-production-sftp.py"

if ! python3 -c "import paramiko" 2>/dev/null; then
	echo "error: paramiko not installed. Run: pip3 install --user paramiko" >&2
	exit 1
fi

should_deploy() {
	local rel="$1"
	[[ "$rel" == global/* ]] && return 0
	local site
	for site in elite huskies lidep nuestrodeporte vollidep voleibalmetepec; do
		if [[ "$rel" == "$site"/* ]]; then
			[[ "$rel" == "$site/ini"* ]] && return 1
			[[ "$rel" == "$site/imagenes"* ]] && return 1
			return 0
		fi
	done
	return 1
}

if [[ $# -lt 1 ]]; then
	echo "usage: $0 <repo-relative-file> [...]" >&2
	exit 1
fi

fail=0
for arg in "$@"; do
	rel="${arg//\\//}"
	rel="${rel#/}"
	if ! should_deploy "$rel"; then
		echo "SKIP (excluded): $rel"
		continue
	fi
	if python3 "$SFTP_PY" upload "$rel" --repo "$REPO"; then
		:
	else
		fail=1
	fi
done
exit $fail
