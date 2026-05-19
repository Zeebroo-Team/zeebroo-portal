#!/usr/bin/env bash
# Create empty public repos on GitHub for all modules (requires: gh auth login).
set -euo pipefail

ORG="Zeebroo-Team"

if ! gh auth status &>/dev/null; then
    echo "Run: gh auth login" >&2
    exit 1
fi

MODULES=(Auth Theme Business Account Settings Transaction HRManagement AIBot AppConnection Modification Product FileManager Purchase Pos)

for mod in "${MODULES[@]}"; do
    repo="zeebroo-module-$(echo "${mod}" | tr '[:upper:]' '[:lower:]')"
    if gh repo view "${ORG}/${repo}" &>/dev/null; then
        echo "Exists: ${ORG}/${repo}"
    else
        gh repo create "${ORG}/${repo}" --public --description "Socibiz ${mod} module (submodule)"
        echo "Created: ${ORG}/${repo}"
    fi
done

if gh repo view "${ORG}/zeebroo-pos-desktop" &>/dev/null; then
    echo "Exists: ${ORG}/zeebroo-pos-desktop"
else
    gh repo create "${ORG}/zeebroo-pos-desktop" --public --description "Socibiz POS desktop (Qt)"
    echo "Created: ${ORG}/zeebroo-pos-desktop"
fi

echo "Done. Run: ./scripts/git-submodules/setup-all-submodules.sh"
