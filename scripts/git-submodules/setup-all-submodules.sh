#!/usr/bin/env bash
# Publish all modules + pos_desktop to GitHub, then install as submodules in this repo.
# Requires: gh auth login, empty repos or publish-module.sh will create via push
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/../.." && pwd)"
cd "$ROOT"
SCRIPT_DIR="$(dirname "$0")"

MODULES=(Auth Theme Business Account Settings Transaction HRManagement AIBot AppConnection Modification Product FileManager Purchase Pos)

echo "=== Phase 1: Publish module repos ==="
for mod in "${MODULES[@]}"; do
    echo "--- ${mod} ---"
    "${SCRIPT_DIR}/publish-module.sh" "${mod}" || echo "WARN: failed ${mod}"
done

echo "--- pos_desktop ---"
"${SCRIPT_DIR}/publish-module.sh" pos_desktop --path pos_desktop --repo zeebroo-pos-desktop || echo "WARN: failed pos_desktop"

echo ""
echo "=== Phase 2: Install submodules in main repo ==="
for mod in "${MODULES[@]}"; do
    echo "--- ${mod} ---"
    "${SCRIPT_DIR}/install-module-submodule.sh" "${mod}" || echo "WARN: install failed ${mod}"
done

"${SCRIPT_DIR}/install-module-submodule.sh" pos_desktop --path pos_desktop --repo zeebroo-pos-desktop || true

echo ""
echo "=== Update submodules after clone ==="
echo "  git submodule update --init --recursive"
