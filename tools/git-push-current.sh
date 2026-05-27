#!/usr/bin/env bash
# Push current branch without changing git config.
# If .local/git.env or .local/sftp-development.env contains GITHUB_TOKEN,
# it is used for this invocation only.
set -euo pipefail

REPO="$(cd "$(dirname "$0")/.." && pwd)"
cd "$REPO"

load_token_file() {
	local file="$1"
	[[ -f "$file" ]] || return 0
	while IFS= read -r line || [[ -n "$line" ]]; do
		line="${line//$'\r'/}"
		[[ "$line" =~ ^GITHUB_TOKEN= ]] || continue
		export GITHUB_TOKEN="${line#*=}"
	done < "$file"
}

load_token_file "$REPO/.local/git.env"
load_token_file "$REPO/.local/sftp-development.env"

branch="$(git branch --show-current)"
if [[ -z "$branch" ]]; then
	echo "error: not on a branch" >&2
	exit 1
fi

if [[ -n "${GITHUB_TOKEN:-}" ]]; then
	remote="$(git remote get-url origin)"
	if [[ "$remote" == https://github.com/* ]]; then
		remote="https://x-access-token:${GITHUB_TOKEN}@${remote#https://}"
	fi
	if [[ "${1:-}" == "--dry-run" ]]; then
		git push --dry-run "$remote" "HEAD:${branch}"
	else
		git push "$remote" "HEAD:${branch}"
	fi
else
	if [[ "${1:-}" == "--dry-run" ]]; then
		git push --dry-run origin "HEAD:${branch}"
	else
		git push origin "HEAD:${branch}"
	fi
fi
