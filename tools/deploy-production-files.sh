#!/usr/bin/env bash
# Upload specific repo files to Production (SFTP). macOS/Linux.
# Requires: python3 + paramiko  →  pip3 install --user paramiko
#
# Usage:
#   bash tools/deploy-production-files.sh global/ajax/Admin/GamesCoach/changeWeeksAdmin.php
#
# Multiple files use one SFTP session (batch) to avoid Bluehost SSH rate limits.
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
	for site in elite huskies lidep nuestrodeporte vollidep voleibalmetepec voleyMVP candlesStore; do
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

batch="$(mktemp "${TMPDIR:-/tmp}/az-deploy-XXXXXX.tsv")"
trap 'rm -f "$batch"' EXIT

queued=0
for arg in "$@"; do
	rel="${arg//\\//}"
	rel="${rel#/}"
	if ! should_deploy "$rel"; then
		echo "SKIP (excluded): $rel"
		continue
	fi
	printf 'UPLOAD\t%s\n' "$rel" >>"$batch"
	queued=$((queued + 1))
done

if [[ "$queued" -eq 0 ]]; then
	exit 0
fi

python3 "$SFTP_PY" batch --batch-file "$batch" --repo "$REPO"
