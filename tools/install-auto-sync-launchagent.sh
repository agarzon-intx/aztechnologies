#!/usr/bin/env bash
# Install a macOS LaunchAgent that automatically syncs Git + Production SFTP.
# It polls the repo once per minute and runs tools/auto-sync-git-sftp.sh when dirty.
set -euo pipefail

REPO="$(cd "$(dirname "$0")/.." && pwd)"
LABEL="tech.aztechnologies.web.autosync"
PLIST="$HOME/Library/LaunchAgents/${LABEL}.plist"
LOG="$REPO/.local/${LABEL}.log"
ERR="$REPO/.local/${LABEL}.err.log"

mkdir -p "$HOME/Library/LaunchAgents" "$REPO/.local"
chmod +x "$REPO/tools/auto-sync-poll.sh" "$REPO/tools/auto-sync-git-sftp.sh" "$REPO/tools/git-push-current.sh"

cat > "$PLIST" <<EOF
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN"
  "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
  <key>Label</key>
  <string>${LABEL}</string>
  <key>ProgramArguments</key>
  <array>
    <string>/bin/bash</string>
    <string>${REPO}/tools/auto-sync-poll.sh</string>
  </array>
  <key>WorkingDirectory</key>
  <string>${REPO}</string>
  <key>StartInterval</key>
  <integer>60</integer>
  <key>RunAtLoad</key>
  <true/>
  <key>StandardOutPath</key>
  <string>${LOG}</string>
  <key>StandardErrorPath</key>
  <string>${ERR}</string>
</dict>
</plist>
EOF

launchctl bootout "gui/$(id -u)" "$PLIST" >/dev/null 2>&1 || true
launchctl bootstrap "gui/$(id -u)" "$PLIST"
launchctl kickstart -k "gui/$(id -u)/${LABEL}"

echo "Installed and started: ${LABEL}"
echo "Plist: ${PLIST}"
echo "Logs:"
echo "  ${LOG}"
echo "  ${ERR}"
echo "  ${REPO}/.local/auto-sync-poll.log"
