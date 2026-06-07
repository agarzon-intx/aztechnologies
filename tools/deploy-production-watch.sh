#!/usr/bin/env bash
# Auto-deploy to Production on file save (SFTP). macOS replacement for deploy-production-watch.ps1
# Requires: fswatch (brew install fswatch), paramiko (pip3 install --user paramiko)
set -euo pipefail

REPO="$(cd "$(dirname "$0")/.." && pwd)"
DEPLOY="$REPO/tools/deploy-production-files.sh"
DEBOUNCE="${1:-3}"

if ! command -v fswatch >/dev/null 2>&1; then
	echo "error: fswatch not found. Install: brew install fswatch" >&2
	exit 1
fi
if ! python3 -c "import paramiko" 2>/dev/null; then
	echo "error: pip3 install --user paramiko" >&2
	exit 1
fi

SITES=(elite huskies lidep nuestrodeporte vollidep voleibalmetepec aztflag demo candlesStore)
WATCH=("$REPO/global")
for s in "${SITES[@]}"; do
	[[ -d "$REPO/$s" ]] && WATCH+=("$REPO/$s")
done

pending=""
timer_pid=""

flush() {
	if [[ -z "$pending" ]]; then return; fi
	local -a files
	read -ra files <<< "$pending"
	pending=""
	echo "[$(date +%H:%M:%S)] Deploy ${#files[@]} file(s)..."
	bash "$DEPLOY" "${files[@]}" || true
}

schedule() {
	local rel="$1"
	# promotable paths only (same rules as deploy-production-files.sh)
	local ok=0
	if [[ "$rel" == global/* ]]; then ok=1; fi
	for s in "${SITES[@]}"; do
		if [[ "$rel" == "$s/"* ]]; then
			[[ "$rel" == "$s/ini/"* || "$rel" == "$s/ini" ]] && return
			[[ "$rel" == "$s/imagenes/"* || "$rel" == "$s/imagenes" ]] && return
			ok=1
		fi
	done
	[[ $ok -eq 1 ]] || return
	if [[ " $pending " != *" $rel "* ]]; then
		pending="$pending $rel"
	fi
	[[ -n "$timer_pid" ]] && kill "$timer_pid" 2>/dev/null || true
	( sleep "$DEBOUNCE"; flush ) &
	timer_pid=$!
}

echo "Production auto-deploy (SFTP) — debounce ${DEBOUNCE}s"
echo "  Repo: $REPO"
echo "  Ctrl+C to stop"
echo ""

fswatch -0 -r --exclude='\.git/' --exclude='/ini/' --exclude='/imagenes/' --exclude='/tmp/' \
	"${WATCH[@]}" | while IFS= read -r -d '' path; do
	[[ -f "$path" ]] || continue
	rel="${path#"$REPO"/}"
	schedule "$rel"
done
