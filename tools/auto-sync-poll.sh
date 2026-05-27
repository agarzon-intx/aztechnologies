#!/usr/bin/env bash
# Polling entrypoint for macOS LaunchAgent.
# If the repo has changes, promote to Production, commit, and push.
set -euo pipefail

REPO="$(cd "$(dirname "$0")/.." && pwd)"
LOCK="$REPO/.local/auto-sync.lock"
LOG="$REPO/.local/auto-sync-poll.log"

mkdir -p "$REPO/.local"
cd "$REPO"

exec >> "$LOG" 2>&1

if ! mkdir "$LOCK" 2>/dev/null; then
	echo "[$(date)] Sync already running; skip."
	exit 0
fi
trap 'rmdir "$LOCK" 2>/dev/null || true' EXIT

if [[ -z "$(git status --porcelain)" ]]; then
	exit 0
fi

echo "[$(date)] Changes detected; running auto sync."
bash tools/auto-sync-git-sftp.sh "Auto sync $(date '+%Y-%m-%d %H:%M:%S')"
