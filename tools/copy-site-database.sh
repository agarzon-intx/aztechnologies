#!/usr/bin/env bash
# Clone one league MySQL schema into another (tables + functions + procedures + triggers).
#
# Local (XAMPP): start MySQL in XAMPP Control, then:
#   MYSQL_USER=root MYSQL_PASSWORD= bash tools/copy-site-database.sh
#
# Remote (cPanel host): if your IP is allowed for remote MySQL:
#   MYSQL_HOST=www.aztechnologies.tech MYSQL_USER=... MYSQL_PASSWORD=... \
#     bash tools/copy-site-database.sh
#
# Non-interactive: NON_INTERACTIVE=1 bash tools/copy-site-database.sh
#
# Defaults: aztechn1_nuestrodeporte -> aztechn1_demomina
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
SOURCE_SCHEMA="${SOURCE_SCHEMA:-aztechn1_nuestrodeporte}"
TARGET_SCHEMA="${TARGET_SCHEMA:-aztechn1_demomina}"
MYSQL_HOST="${MYSQL_HOST:-127.0.0.1}"
MYSQL_PORT="${MYSQL_PORT:-3306}"
MYSQL_USER="${MYSQL_USER:-root}"
MYSQL_PASSWORD="${MYSQL_PASSWORD:-}"
NON_INTERACTIVE="${NON_INTERACTIVE:-0}"

# Prefer XAMPP client on macOS when present
if [[ -x /Applications/XAMPP/xamppfiles/bin/mysql ]]; then
	MYSQL_BIN="/Applications/XAMPP/xamppfiles/bin/mysql"
	DUMP_BIN="/Applications/XAMPP/xamppfiles/bin/mysqldump"
elif command -v mysql >/dev/null 2>&1; then
	MYSQL_BIN="$(command -v mysql)"
	DUMP_BIN="$(command -v mysqldump)"
else
	echo "error: mysql client not found (install XAMPP MySQL or brew install mysql)" >&2
	exit 1
fi

mysql_args=(-h "$MYSQL_HOST" -P "$MYSQL_PORT" -u "$MYSQL_USER")
dump_args=(-h "$MYSQL_HOST" -P "$MYSQL_PORT" -u "$MYSQL_USER")
if [[ -n "$MYSQL_PASSWORD" ]]; then
	export MYSQL_PWD="$MYSQL_PASSWORD"
fi

count_tables() {
	local schema="$1"
	"$MYSQL_BIN" "${mysql_args[@]}" -Nse \
		"SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = '$schema' AND TABLE_TYPE = 'BASE TABLE'"
}

count_routines() {
	local schema="$1"
	local type="$2"
	"$MYSQL_BIN" "${mysql_args[@]}" -Nse \
		"SELECT COUNT(*) FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = '$schema' AND ROUTINE_TYPE = '$type'"
}

print_schema_summary() {
	local label="$1"
	local schema="$2"
	local tables funcs procs triggers
	tables="$(count_tables "$schema")"
	funcs="$(count_routines "$schema" FUNCTION)"
	procs="$(count_routines "$schema" PROCEDURE)"
	triggers="$("$MYSQL_BIN" "${mysql_args[@]}" -Nse \
		"SELECT COUNT(*) FROM information_schema.TRIGGERS WHERE TRIGGER_SCHEMA = '$schema'")"
	echo "  $label"
	echo "    tables:      $tables"
	echo "    functions:   $funcs"
	echo "    procedures:  $procs"
	echo "    triggers:    $triggers"
}

echo "Copy includes: tables, stored FUNCTIONS, stored PROCEDURES, triggers, events"
echo ""
echo "Source: $SOURCE_SCHEMA"
echo "Target: $TARGET_SCHEMA"
echo "Host:   $MYSQL_HOST:$MYSQL_PORT"
echo ""

if ! "$MYSQL_BIN" "${mysql_args[@]}" -e "SELECT 1" >/dev/null 2>&1; then
	echo "error: cannot connect to MySQL. Start XAMPP MySQL or check MYSQL_HOST / MYSQL_USER / MYSQL_PASSWORD." >&2
	exit 1
fi

if ! "$MYSQL_BIN" "${mysql_args[@]}" -Nse "SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = '$SOURCE_SCHEMA'" | grep -qx "$SOURCE_SCHEMA"; then
	echo "error: source schema '$SOURCE_SCHEMA' does not exist on this server." >&2
	echo "  Local: import nuestrodeporte dump first, or point MYSQL_HOST at production." >&2
	exit 1
fi

echo "Source inventory:"
print_schema_summary "$SOURCE_SCHEMA" "$SOURCE_SCHEMA"
echo ""

if [[ "$NON_INTERACTIVE" != "1" ]]; then
	read -r -p "Replace ALL data in '$TARGET_SCHEMA' (tables + routines)? [y/N] " confirm
	if [[ ! "$confirm" =~ ^[Yy]$ ]]; then
		echo "Aborted."
		exit 0
	fi
fi

# Helps import user-defined functions that touch data (common on league DBs)
"$MYSQL_BIN" "${mysql_args[@]}" -e "SET GLOBAL log_bin_trust_function_creators = 1;" 2>/dev/null || true

echo "Recreating target schema..."
"$MYSQL_BIN" "${mysql_args[@]}" -e "DROP DATABASE IF EXISTS \`$TARGET_SCHEMA\`; CREATE DATABASE \`$TARGET_SCHEMA\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

echo "Dumping and importing (tables + routines + triggers + events)..."
# --routines = stored procedures AND functions
# Strip DEFINER so import works when target server uses different MySQL users
"$DUMP_BIN" "${dump_args[@]}" \
	--single-transaction \
	--routines \
	--triggers \
	--events \
	--no-tablespaces \
	--set-gtid-purged=OFF \
	"$SOURCE_SCHEMA" \
	| sed -E 's/DEFINER=`[^`]+`@`[^`]+`/DEFINER=CURRENT_USER/g' \
	| "$MYSQL_BIN" "${mysql_args[@]}" "$TARGET_SCHEMA"

echo ""
echo "Target inventory:"
print_schema_summary "$TARGET_SCHEMA" "$TARGET_SCHEMA"

src_tables="$(count_tables "$SOURCE_SCHEMA")"
tgt_tables="$(count_tables "$TARGET_SCHEMA")"
src_funcs="$(count_routines "$SOURCE_SCHEMA" FUNCTION)"
tgt_funcs="$(count_routines "$TARGET_SCHEMA" FUNCTION)"
src_procs="$(count_routines "$SOURCE_SCHEMA" PROCEDURE)"
tgt_procs="$(count_routines "$TARGET_SCHEMA" PROCEDURE)"

ok=1
if [[ "$src_tables" != "$tgt_tables" ]]; then
	echo "warn: table count mismatch (source $src_tables, target $tgt_tables)" >&2
	ok=0
fi
if [[ "$src_funcs" != "$tgt_funcs" ]]; then
	echo "warn: function count mismatch (source $src_funcs, target $tgt_funcs)" >&2
	ok=0
fi
if [[ "$src_procs" != "$tgt_procs" ]]; then
	echo "warn: procedure count mismatch (source $src_procs, target $tgt_procs)" >&2
	ok=0
fi

if [[ "$ok" -eq 1 ]]; then
	echo ""
	echo "OK: tables, functions, and procedures match source counts."
else
	echo ""
	echo "Copy finished with warnings — check MySQL error output above or re-run as a user with ROUTINE privileges."
	exit 1
fi

echo "demo/ini/config.ini should use schema = $TARGET_SCHEMA (already set)."
if [[ "$MYSQL_HOST" == "127.0.0.1" || "$MYSQL_HOST" == "localhost" ]]; then
	echo "For local PHP, set servername = 127.0.0.1 in demo/ini/config.ini if not already."
fi
