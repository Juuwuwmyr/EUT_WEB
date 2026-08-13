#!/usr/bin/env bash
# Print or apply Gmail SMTP settings to .env
#
# Usage:
#   ./scripts/cat-mail-env.sh              # print mail block
#   ./scripts/cat-mail-env.sh --apply      # update .env on server/local
#   ./scripts/cat-mail-env.sh --apply --clear-config
#
# First time: cp scripts/mail.env.example scripts/mail.env  (then edit password)

set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
BLOCK="$ROOT/scripts/mail.env"
ENV_FILE="$ROOT/.env"
APPLY=0
CLEAR=0

for arg in "$@"; do
  case "$arg" in
    --apply) APPLY=1 ;;
    --clear-config) CLEAR=1 ;;
  esac
done

if [[ ! -f "$BLOCK" ]]; then
  echo "Missing $BLOCK"
  echo "Run: cp scripts/mail.env.example scripts/mail.env"
  echo "Then set MAIL_PASSWORD to your Google App Password."
  exit 1
fi

echo "# -- Mail (Gmail SMTP) --"
cat "$BLOCK"

if [[ "$APPLY" -eq 0 ]]; then
  exit 0
fi

if [[ ! -f "$ENV_FILE" ]]; then
  echo "Creating $ENV_FILE from .env.example ..."
  cp "$ROOT/.env.example" "$ENV_FILE"
fi

TMP="$(mktemp)"
grep -Ev '^MAIL_|^# -- Mail' "$ENV_FILE" > "$TMP" || true
{
  cat "$TMP"
  echo ""
  echo "# -- Mail (Gmail SMTP) --"
  cat "$BLOCK"
} > "$ENV_FILE"
rm -f "$TMP"

echo ""
echo "Updated $ENV_FILE"

if [[ "$CLEAR" -eq 1 ]] && command -v php >/dev/null 2>&1; then
  (cd "$ROOT" && php artisan config:clear && php artisan cache:clear)
  echo "Laravel config cache cleared."
fi
