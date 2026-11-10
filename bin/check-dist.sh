#!/usr/bin/env bash
# ==============================================================================
# Elemen Ane Theme — Distribution Archive Verification
# Ensures `.distignore` excludes all dev/tooling files from release ZIPs.
# ==============================================================================
set -euo pipefail

RED='\033[0;31m'
GREEN='\033[0;32m'
NC='\033[0m'

TMP_DIR=$(mktemp -d)
trap 'rm -rf "$TMP_DIR"' EXIT

echo "============================================================"
echo "Simulating production build via .distignore..."
echo "============================================================"

rsync -rc --exclude-from=.distignore ./ "$TMP_DIR/elemenane/"

FORBIDDEN_PATHS=(
	".git"
	".github"
	".claude"
	".gitignore"
	".gitattributes"
	".distignore"
	".releaserc.json"
	"AGENTS.md"
	"CLAUDE.md"
	"README.md"
	"CHANGELOG.md"
	"ADMIN-DASHBOARD-GUIDE.md"
	"SEO-IMPLEMENTATION-GUIDE.md"
	"docs"
	"blueprints"
	"bin"
	"tests"
	"scss"
	"vendor"
	"node_modules"
	"composer.json"
	"composer.lock"
	"phpcs.xml.dist"
	"phpstan.neon.dist"
	"phpstan-baseline.neon"
	"phpstan-bootstrap.php"
	"languages/compile-mo.php"
	"languages/test-translation.php"
)

ERRORS=0
for item in "${FORBIDDEN_PATHS[@]}"; do
	if [[ -e "$TMP_DIR/elemenane/$item" ]]; then
		echo -e "${RED}[FAIL] Forbidden dev file/folder leaked into dist: ${item}${NC}"
		ERRORS=$((ERRORS + 1))
	fi
done

REQUIRED_PATHS=(
	"style.css"
	"functions.php"
	"index.php"
	"header.php"
	"footer.php"
	"screenshot.jpg"
	"css/main.min.css"
	"css/catalog.min.css"
	"css/landing.min.css"
	"css/linktree-page.min.css"
	"css/admin.min.css"
	"inc/elemenane.php"
	"inc/updater.php"
)

for req in "${REQUIRED_PATHS[@]}"; do
	if [[ ! -e "$TMP_DIR/elemenane/$req" ]]; then
		echo -e "${RED}[FAIL] Required theme file missing from dist: ${req}${NC}"
		ERRORS=$((ERRORS + 1))
	fi
done

if [[ "$ERRORS" -gt 0 ]]; then
	echo -e "${RED}Dist check failed with ${ERRORS} error(s).${NC}"
	exit 1
fi

FILE_COUNT=$(find "$TMP_DIR/elemenane" -type f | wc -l | tr -d ' ')
echo -e "${GREEN}[OK] Production package clean (${FILE_COUNT} runtime files ready for release ZIP).${NC}"
