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
import os, json, hashlib
REPO = os.environ['REPO']
STATE = os.path.join(REPO, '.local', 'promote-state.json')
SITES = ['elite','huskies','lidep','nuestrodeporte','vollidep','voleibalmetepec','aztflag','demo','candlesStore']
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

def digest(rel):
    h = hashlib.sha1()
    with open(os.path.join(REPO, rel), 'rb') as fh:
        for chunk in iter(lambda: fh.read(65536), b''):
            h.update(chunk)
    return h.hexdigest()

# Contents of everything successfully promoted so far, so a run only sends what
# actually changed instead of the whole dirty working tree.
try:
    with open(STATE) as fh:
        state = json.load(fh)
except Exception:
    state = {}
promoted = state.get('uploaded', {})
removed = set(state.get('deleted', []))

# The working tree is the source of truth, not `git status`: an auto-commit job
# can clear the dirty list at any moment and would otherwise hide pending work.
on_disk = set()
pending_upload = []
for root, dirs, files in os.walk(REPO):
    dirs[:] = [d for d in dirs if d not in ('.git', '.local', '.cursor', 'node_modules')]
    for name in files:
        rel = os.path.relpath(os.path.join(root, name), REPO).replace('\\', '/')
        if not should_deploy(rel):
            continue
        on_disk.add(rel)
        try:
            if promoted.get(rel) != digest(rel):
                pending_upload.append(rel)
        except OSError:
            continue
pending_upload.sort()

# Anything previously promoted that no longer exists locally should go away remotely.
pending_delete = sorted(
    rel for rel in promoted
    if rel not in on_disk and rel not in removed and not is_junction_path(rel)
)

for kind, paths in [('UPLOAD', pending_upload), ('DELETE', pending_delete)]:
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
batch_out="$(mktemp "${TMPDIR:-/tmp}/az-production-out.XXXXXX")"
trap 'rm -f "$batch_file" "$batch_out" /tmp/az-promote-files.txt' EXIT
if [[ ${#upload_files[@]} -eq 0 && ${#delete_files[@]} -eq 0 ]]; then
	echo "Nothing to promote."
	exit 0
fi

for rel in ${upload_files[@]+"${upload_files[@]}"}; do
	printf 'UPLOAD\t%s\n' "$rel" >> "$batch_file"
done
for rel in ${delete_files[@]+"${delete_files[@]}"}; do
	printf 'DELETE\t%s\n' "$rel" >> "$batch_file"
done

batch_status=0
python3 "$REPO/tools/deploy-production-sftp.py" batch --repo "$REPO" --batch-file "$batch_file" | tee "$batch_out" || batch_status=1

# Record what is now live so the next run skips it.
BATCH_OUT="$batch_out" python3 << 'PY'
import os, json, hashlib

REPO = os.environ['REPO']
STATE = os.path.join(REPO, '.local', 'promote-state.json')

try:
    with open(STATE) as fh:
        state = json.load(fh)
except Exception:
    state = {}
uploaded = state.get('uploaded', {})
deleted = set(state.get('deleted', []))

with open(os.environ['BATCH_OUT'], errors='replace') as fh:
    lines = fh.read().splitlines()

for line in lines:
    if line.startswith('OK upload '):
        rel = line[len('OK upload '):].strip()
        try:
            h = hashlib.sha1()
            with open(os.path.join(REPO, rel), 'rb') as f:
                for chunk in iter(lambda: f.read(65536), b''):
                    h.update(chunk)
            uploaded[rel] = h.hexdigest()
        except OSError:
            pass
    elif line.startswith('OK delete '):
        rel = line[len('OK delete '):].strip()
        deleted.add(rel)
        uploaded.pop(rel, None)
    elif line.startswith('FAIL delete ') and 'No such file' in line:
        # Already absent on the server; stop retrying it on every run.
        rel = line[len('FAIL delete '):].split(':', 1)[0].strip()
        deleted.add(rel)
        uploaded.pop(rel, None)

os.makedirs(os.path.dirname(STATE), exist_ok=True)
with open(STATE, 'w') as fh:
    json.dump({'uploaded': uploaded, 'deleted': sorted(deleted)}, fh, indent=1, sort_keys=True)
PY

if [[ "$batch_status" -ne 0 ]]; then
	echo "Completed with errors."
	exit 1
fi
echo "Production promote completed successfully."
} 2>&1 | tee "$REPORT"
exit "${PIPESTATUS[0]}"
