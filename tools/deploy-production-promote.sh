#!/usr/bin/env bash
# Sync Production via SFTP: upload new/edited promotable git files, delete promotable removals.
# Skips site junction paths (elite/ajax etc.) and ini/imagenes/tools/.local.
#
# Usage: bash tools/deploy-production-promote.sh
set -euo pipefail
set -o pipefail

REPO="$(cd "$(dirname "$0")/.." && pwd)"
ENV_FILE="$REPO/.local/sftp-development.env"
REPORT="$REPO/.local/deploy-production-promote-$(date +%Y%m%d-%H%M%S).txt"

cd "$REPO"

if ! python3 -c "import paramiko" 2>/dev/null; then
	echo "error: paramiko not installed. Run: pip3 install --user paramiko" >&2
	exit 1
fi

{
echo "Production promote (SFTP) — $(date)"
echo "Report: $REPORT"
echo ""

while IFS= read -r line; do
	line="${line//$'\r'/}"
	[[ "$line" =~ ^SFTP_(HOST|USER|PASSWORD|PRODUCTION_BASE)= ]] || continue
	export "${line%%=*}"="${line#*=}"
done < "$ENV_FILE"

export REPO
python3 << 'PY' > /tmp/az-promote-files.txt
import subprocess, os
REPO = os.environ['REPO']
SITES = ['elite','huskies','lidep','nuestrodeporte','vollidep','voleibalmetepec']
JUNCTIONS = {'ajax','assets','config','css','Form','include','javascript','languages','objects'}

def should_deploy(rel):
    rel = rel.replace('\\','/').strip('/')
    if not rel: return False
    if any(x in rel for x in ('.git/','.local/','.cursor/','tools/','logs/','tmp/')):
        return False
    if rel == '.gitignore': return False
    if rel.startswith('global/'): return True
    for s in SITES:
        if rel == s or rel.startswith(s + '/'):
            if rel.startswith(f'{s}/ini') or rel.startswith(f'{s}/ini/'): return False
            if rel.startswith(f'{s}/imagenes') or '/imagenes/' in rel: return False
            return True
    return False

def is_junction_path(rel):
    rel = rel.replace('\\','/').strip('/')
    parts = rel.split('/')
    return len(parts) >= 2 and parts[0] in SITES and parts[1] in JUNCTIONS

out = subprocess.check_output(['git','status','--porcelain'], text=True, errors='replace')
upload, delete = [], []
for line in out.splitlines():
    if len(line) < 4: continue
    xy, path = line[:2], line[3:].strip().strip('"')
    path = path.split(' -> ', 1)[0].replace('\\', '/')
    st = xy.strip()
    if xy.strip() == 'D' or xy == ' D' or st == 'D':
        if should_deploy(path) and not is_junction_path(path):
            delete.append(path)
    elif st in ('M', 'A', '??') or xy.strip() in ('M', 'A') or xy == '??':
        if should_deploy(path) and os.path.isfile(os.path.join(REPO, path)):
            upload.append(path)

for kind, paths in [('UPLOAD', sorted(set(upload))), ('DELETE', sorted(set(delete)))]:
    print(f'#{kind} {len(paths)}')
    for p in paths:
        print(p)
PY

upload_files=()
delete_files=()
section=""
while IFS= read -r line; do
	[[ -z "$line" ]] && continue
	if [[ "$line" == \#UPLOAD* ]]; then section=upload; continue; fi
	if [[ "$line" == \#DELETE* ]]; then section=delete; continue; fi
	if [[ "$section" == upload ]]; then upload_files+=("$line"); fi
	if [[ "$section" == delete ]]; then delete_files+=("$line"); fi
done < /tmp/az-promote-files.txt

echo "Upload: ${#upload_files[@]} | Delete: ${#delete_files[@]}"
echo ""

ftp_upload() {
	local rel="$1"
	python3 "$REPO/tools/deploy-production-sftp.py" upload "$rel" --repo "$REPO"
}

ftp_delete() {
	local rel="$1"
	python3 "$REPO/tools/deploy-production-sftp.py" delete "$rel" --repo "$REPO"
}

batch_file="$(mktemp "${TMPDIR:-/tmp}/az-production-batch.XXXXXX")"
trap 'rm -f "$batch_file" /tmp/az-promote-files.txt' EXIT
for rel in "${upload_files[@]}"; do
	printf 'UPLOAD\t%s\n' "$rel" >> "$batch_file"
done
for rel in "${delete_files[@]}"; do
	printf 'DELETE\t%s\n' "$rel" >> "$batch_file"
done

if [[ ! -s "$batch_file" ]]; then
	echo "Nothing to promote."
	exit 0
fi

if ! python3 "$REPO/tools/deploy-production-sftp.py" batch --repo "$REPO" --batch-file "$batch_file"; then
	echo "Completed with errors."
	exit 1
fi
echo "Production promote completed successfully."
} 2>&1 | tee "$REPORT"
exit "${PIPESTATUS[0]}"
