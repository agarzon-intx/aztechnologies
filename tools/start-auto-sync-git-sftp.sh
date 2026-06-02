#!/usr/bin/env bash
# Watch app folders and run auto-sync-git-sftp.sh after a debounce.
# Requires: fswatch (brew install fswatch), paramiko, and GitHub credentials.
set -euo pipefail

REPO="$(cd "$(dirname "$0")/.." && pwd)"
PIDFILE="$REPO/.local/auto-sync-git-sftp.pid"
LOG="$REPO/.local/auto-sync-git-sftp.log"

if ! command -v fswatch >/dev/null 2>&1; then
	echo "error: fswatch not found. Install: brew install fswatch" >&2
	exit 1
fi

if [[ -f "$PIDFILE" ]]; then
	old="$(cat "$PIDFILE" 2>/dev/null || true)"
	if [[ -n "$old" ]] && kill -0 "$old" 2>/dev/null; then
		echo "Auto sync already running (pid $old). Stop: kill $old"
		exit 0
	fi
fi

nohup bash -c '
	set -euo pipefail
	cd "$1"
	debounce="${2:-5}"
	pending=0
	timer_pid=""
	run_sync() {
		pending=0
		echo "[$(date)] Auto sync triggered"
		bash tools/auto-sync-git-sftp.sh || true
	}
	schedule() {
		pending=1
		[[ -n "$timer_pid" ]] && kill "$timer_pid" 2>/dev/null || true
		( sleep "$debounce"; run_sync ) &
		timer_pid=$!
	}
	fswatch -0 -r \
		--exclude="\\.git/" \
		--exclude="\\.local/" \
		--exclude="/logs/" \
		--exclude="/tmp/" \
		--exclude="/imagenes/" \
		--exclude="/ini/" \
		global elite huskies lidep nuestrodeporte vollidep voleibalmetepec aztflag demo tools \
		| while IFS= read -r -d "" _path; do
			schedule
		done
' _ "$REPO" "${1:-5}" >> "$LOG" 2>&1 &

echo $! > "$PIDFILE"
echo "Started auto sync pid $(cat "$PIDFILE") — log: $LOG"
