#!/usr/bin/env bash
# ==============================================================================
# Lint only git-modified/staged PHP files (fast pre-commit / phase verification)
# ==============================================================================
set -euo pipefail

CHANGED_FILES=$(git diff --name-only --diff-filter=ACMR HEAD -- '*.php' || true)
if [[ -z "$CHANGED_FILES" ]]; then
	CHANGED_FILES=$(git diff --name-only --cached --diff-filter=ACMR -- '*.php' || true)
fi

if [[ -z "$CHANGED_FILES" ]]; then
	echo "No changed PHP files detected compared to HEAD."
	exit 0
fi

echo "Running PHP syntax & PHPCS on changed files:"
echo "$CHANGED_FILES"

for f in $CHANGED_FILES; do
	if [[ -f "$f" ]]; then
		php -l "$f"
	fi
done

# shellcheck disable=SC2086
./vendor/bin/phpcs $CHANGED_FILES
