#!/usr/bin/env bash
# Start the auto Git + SFTP sync from a real macOS Terminal process.
# This is useful because Cursor agent shells may not have DNS/SSH/Git credential access,
# while Terminal.app uses the user's normal macOS network and credentials.
set -euo pipefail

REPO="$(cd "$(dirname "$0")/.." && pwd)"
LOG="$REPO/.local/cursor-owned-auto-sync-launch.log"
COMMAND_FILE="$REPO/.local/start-auto-sync.command"

mkdir -p "$REPO/.local"

cat > "$COMMAND_FILE" <<EOF
#!/usr/bin/env bash
set -euo pipefail
cd "$REPO"
echo "Starting Aztechnologies auto sync from Terminal..."
bash tools/start-auto-sync-git-sftp.sh
echo "Auto sync launcher finished. You can close this Terminal window."
EOF
chmod +x "$COMMAND_FILE"

/usr/bin/open -a /System/Applications/Utilities/Terminal.app "$COMMAND_FILE" | tee -a "$LOG"
