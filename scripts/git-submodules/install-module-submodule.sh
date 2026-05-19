#!/usr/bin/env bash
# Replace a tracked module folder with a git submodule.
# Usage: ./scripts/git-submodules/install-module-submodule.sh Pos
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/../.." && pwd)"
cd "$ROOT"

MODULE_NAME="${1:?Module name required}"
MODULE_PATH="Modules/${MODULE_NAME}"
REPO_NAME="zeebroo-module-$(echo "${MODULE_NAME}" | tr '[:upper:]' '[:lower:]')"
ORG="Zeebroo-Team"

while [[ $# -gt 0 ]]; do
    case "$1" in
        --path) MODULE_PATH="$2"; shift 2 ;;
        --repo) REPO_NAME="$2"; shift 2 ;;
        --org) ORG="$2"; shift 2 ;;
        *) shift ;;
    esac
done

REMOTE="git@github.com:${ORG}/${REPO_NAME}.git"

if git submodule status "${MODULE_PATH}" &>/dev/null; then
    echo "Already a submodule: ${MODULE_PATH}"
    exit 0
fi

if [[ -d "${MODULE_PATH}" ]]; then
    echo "==> Removing tracked files at ${MODULE_PATH}"
    git rm -rf "${MODULE_PATH}"
    git commit -m "Remove ${MODULE_PATH} before adding submodule"
fi

echo "==> Adding submodule ${REMOTE} -> ${MODULE_PATH}"
git submodule add "${REMOTE}" "${MODULE_PATH}"
git submodule update --init --recursive "${MODULE_PATH}"

echo "Done. Commit and push:"
echo "  git commit -m \"Add ${MODULE_NAME} module as submodule\""
echo "  git push origin main"
