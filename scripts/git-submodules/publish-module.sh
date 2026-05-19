#!/usr/bin/env bash
# Publish a Laravel module (or pos_desktop) to its own GitHub repo for use as a submodule.
# Usage:
#   ./scripts/git-submodules/publish-module.sh Pos
#   ./scripts/git-submodules/publish-module.sh pos_desktop --path pos_desktop --repo zeebroo-pos-desktop
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/../.." && pwd)"
cd "$ROOT"

MODULE_NAME="${1:?Module name required (e.g. Pos)}"
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

if [[ ! -d "$MODULE_PATH" ]]; then
    echo "Error: path not found: $MODULE_PATH" >&2
    exit 1
fi

BRANCH="submodule/${MODULE_NAME}"
REMOTE="git@github.com:${ORG}/${REPO_NAME}.git"

echo "==> Splitting ${MODULE_PATH} into branch ${BRANCH}"
git subtree split -P "${MODULE_PATH}" -b "${BRANCH}" --rejoin 2>/dev/null || git subtree split -P "${MODULE_PATH}" -b "${BRANCH}"

WORKDIR="$(mktemp -d)"
trap 'rm -rf "$WORKDIR"' EXIT

echo "==> Cloning split branch into ${WORKDIR}"
git clone -b "${BRANCH}" --single-branch "$ROOT" "$WORKDIR/out"

cd "$WORKDIR/out"
if [[ ! -f README.md ]]; then
    cat > README.md <<EOF
# ${REPO_NAME}

Socibiz module extracted from [\`${MODULE_PATH}\`](https://github.com/Zeebroo-Team/zeebroo-portal).

Install as a git submodule in the main app:

\`\`\`bash
git submodule add ${REMOTE} ${MODULE_PATH}
\`\`\`
EOF
    git add README.md
    git commit -m "Add module README for submodule consumers"
fi

git remote add origin "${REMOTE}" 2>/dev/null || git remote set-url origin "${REMOTE}"

echo "==> Pushing to ${REMOTE}"
if git push -u origin HEAD:main --force; then
    echo "Published: ${REMOTE}"
else
    echo ""
    echo "Push failed. Create an empty repo first, then re-run:"
    echo "  gh auth login"
    echo "  gh repo create ${ORG}/${REPO_NAME} --public"
    echo "  $0 ${MODULE_NAME}"
    exit 1
fi
