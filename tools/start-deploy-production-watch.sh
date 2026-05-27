#!/usr/bin/env bash
# Start deploy-production-watch.sh in background. Logs to .local/deploy-production-watch.log
set -euo pipefail
REPO="$(cd "$(dirname "$0")/.." && pwd)"
PIDFILE="$REPO/.local/deploy-production-watch.pid"
LOG="$REPO/.local/deploy-production-watch.log"
SCRIPT="$REPO/tools/deploy-production-watch.sh"

if [[ -f "$PIDFILE" ]]; then
	old=$(cat "$PIDFILE" 2>/dev/null || true)
	if [[ -n "$old" ]] && kill -0 "$old" 2>/dev/null; then
		echo "Watch already running (pid $old). Stop: kill $old"
		exit 0
	fi
fi

# Keep one watcher instance to avoid duplicate deploys.
nohup bash "$SCRIPT" >> "$LOG" 2>&1 &
echo $! > "$PIDFILE"
echo "Started deploy watch pid $(cat "$PIDFILE") — log: $LOG"
