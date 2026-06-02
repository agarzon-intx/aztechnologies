#!/usr/bin/env bash
# Clone one league MySQL schema into another (e.g. nuestrodeporte -> demo / demomina).
#
# Local (XAMPP): start MySQL in XAMPP Control, then:
#   MYSQL_USER=root MYSQL_PASSWORD= bash tools/copy-site-database.sh
#
# Remote (cPanel host): if your IP is allowed for remote MySQL:
#   MYSQL_HOST=www.aztechnologies.tech MYSQL_USER=... MYSQL_PASSWORD=... \
#     bash tools/copy-site-database.sh
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

read -r -p "Replace ALL data in '$TARGET_SCHEMA'? [y/N] " confirm
if [[ ! "$confirm" =~ ^[Yy]$ ]]; then
	echo "Aborted."
	exit 0
fi

echo "Creating target schema (if missing)..."
"$MYSQL_BIN" "${mysql_args[@]}" -e "DROP DATABASE IF EXISTS \`$TARGET_SCHEMA\`; CREATE DATABASE \`$TARGET_SCHEMA\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

echo "Copying tables (this may take a few minutes)..."
"$DUMP_BIN" "${dump_args[@]}" \
	--single-transaction \
	--routines \
	--triggers \
	--events \
	--set-gtid-purged=OFF \
	"$SOURCE_SCHEMA" \
	| "$MYSQL_BIN" "${mysql_args[@]}" "$TARGET_SCHEMA"

table_count="$("$MYSQL_BIN" "${mysql_args[@]}" -Nse "SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = '$TARGET_SCHEMA'")"
echo ""
echo "Done. $TARGET_SCHEMA has $table_count table(s)."
echo "demo/ini/config.ini should use schema = $TARGET_SCHEMA (already set)."
if [[ "$MYSQL_HOST" == "127.0.0.1" || "$MYSQL_HOST" == "localhost" ]]; then
	echo "For local PHP, set servername = 127.0.0.1 in demo/ini/config.ini if not already."
fi
