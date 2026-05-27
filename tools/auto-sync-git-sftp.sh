#!/usr/bin/env bash
# Full automatic sync:
# 1) SFTP promote current git M/??/D promotable files to Production
# 2) Commit all current repo changes
# 3) Push the current branch
#
# This intentionally runs from Terminal.app / a real shell, not the Cursor
# agent sandbox, because SSH/DNS and Git credentials may be unavailable there.
set -euo pipefail

REPO="$(cd "$(dirname "$0")/.." && pwd)"
cd "$REPO"

msg="${1:-Auto sync $(date '+%Y-%m-%d %H:%M:%S')}"

echo "== SFTP promote =="
bash tools/deploy-production-promote.sh

echo ""
echo "== Git commit =="
git add -A -- .gitignore global elite huskies lidep nuestrodeporte vollidep voleibalmetepec tools
if git diff --cached --quiet; then
	echo "No git changes to commit."
else
	git commit -m "$msg"
fi

echo ""
echo "== Git push =="
bash tools/git-push-current.sh

echo ""
echo "Auto sync completed."
