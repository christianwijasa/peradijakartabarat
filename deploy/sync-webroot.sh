#!/usr/bin/env bash
# Run from deploy root (parent of src/). Example:
#   cd /home/n1542665/public_html/laravel/peradijakartabarat && bash src/deploy/sync-webroot.sh

set -euo pipefail

DEPLOY="$(cd "$(dirname "$0")" && pwd)"
ROOT="$(cd "$DEPLOY/.." && pwd)"
WEBROOT="$(cd "$ROOT/.." && pwd)"

cp "$DEPLOY/index.php" "$WEBROOT/index.php"
cp "$DEPLOY/.htaccess.example" "$WEBROOT/.htaccess"

if [[ -d "$ROOT/public/build" ]]; then
  rm -rf "$WEBROOT/build"
  cp -a "$ROOT/public/build" "$WEBROOT/build"
fi

if [[ -f "$ROOT/public/robots.txt" ]]; then
  cp "$ROOT/public/robots.txt" "$WEBROOT/robots.txt"
fi

echo "Synced index.php, .htaccess, build/, robots.txt to $WEBROOT"
echo "Edit RewriteBase in $WEBROOT/.htaccess before going live."
