#!/usr/bin/env bash
# ==============================================================================
# Elemen Ane Theme — Automated Security & Backdoor Scanner
# ==============================================================================
set -euo pipefail

RED='\033[0;31m'
YELLOW='\033[1;33m'
GREEN='\033[0;32m'
NC='\033[0m'

ERRORS=0
WARNINGS=0

echo "============================================================"
echo "Running Elemen Ane Theme Security & Anti-Backdoor Scan..."
echo "============================================================"

# 1. Dangerous PHP execution / obfuscation functions (webshell indicators)
echo -e "\n[1/5] Checking for dangerous PHP execution functions..."
DANGEROUS_Matches=$(grep -rnE --include="*.php" \
	--exclude-dir={vendor,node_modules,dist,languages} \
	'\b(eval|exec|shell_exec|system|passthru|proc_open|popen|pcntl_exec|assert|create_function|base64_decode|gzinflate|gzuncompress|str_rot13)\s*\(' . || true)

if [[ -n "$DANGEROUS_Matches" ]]; then
	echo -e "${RED}[FAIL] Found dangerous PHP functions:${NC}"
	echo "$DANGEROUS_Matches"
	ERRORS=$((ERRORS + 1))
else
	echo -e "${GREEN}[OK] No dangerous execution/obfuscation functions found.${NC}"
fi

# 2. Variable function calls via superglobals (e.g. $_GET['x']($_POST['y']))
echo -e "\n[2/5] Checking for superglobal variable function execution..."
SUPERGLOBAL_EXEC=$(grep -rnE --include="*.php" \
	--exclude-dir={vendor,node_modules,dist,languages} \
	'\$_(GET|POST|REQUEST|COOKIE|SERVER|FILES)\s*\[[^]]+\]\s*\(' . || true)

if [[ -n "$SUPERGLOBAL_EXEC" ]]; then
	echo -e "${RED}[FAIL] Found superglobal variable function call (backdoor pattern):${NC}"
	echo "$SUPERGLOBAL_EXEC"
	ERRORS=$((ERRORS + 1))
else
	echo -e "${GREEN}[OK] No superglobal variable function calls found.${NC}"
fi

# 3. Direct ABSPATH check in PHP files (except functions.php, index.php, phpstan-bootstrap.php, and comments-only files)
echo -e "\n[3/5] Checking for missing ABSPATH direct-access guards in PHP files..."
MISSING_ABSPATH=0
while IFS= read -r -d '' file; do
	base=$(basename "$file")
	if [[ "$base" == "functions.php" || "$base" == "index.php" || "$base" == "phpstan-bootstrap.php" ]]; then
		continue
	fi
	if ! grep -qE "ABSPATH" "$file"; then
		echo -e "${YELLOW}[WARN] Missing ABSPATH guard in: ${file}${NC}"
		WARNINGS=$((WARNINGS + 1))
		MISSING_ABSPATH=$((MISSING_ABSPATH + 1))
	fi
done < <(find . -name "*.php" -not -path "./vendor/*" -not -path "./node_modules/*" -not -path "./dist/*" -not -path "./languages/*" -print0)

if [[ "$MISSING_ABSPATH" -eq 0 ]]; then
	echo -e "${GREEN}[OK] All PHP files contain ABSPATH guard.${NC}"
fi

# 4. Leftover debug statements
echo -e "\n[4/5] Checking for leftover debug statements (var_dump, print_r, error_log)..."
DEBUG_MATCHES=$(grep -rnE --include="*.php" \
	--exclude-dir={vendor,node_modules,dist,languages} \
	'\b(var_dump|print_r|debug_print_backtrace|wp_die\s*\(\s*\$)\s*\(' . || true)

if [[ -n "$DEBUG_MATCHES" ]]; then
	echo -e "${YELLOW}[WARN] Found debug statements:${NC}"
	echo "$DEBUG_MATCHES"
	WARNINGS=$((WARNINGS + 1))
else
	echo -e "${GREEN}[OK] No leftover debug dump statements found.${NC}"
fi

# 5. Hardcoded API keys / tokens
echo -e "\n[5/5] Checking for hardcoded API keys or secrets..."
SECRET_MATCHES=$(grep -rnE --include="*.php" \
	--exclude-dir={vendor,node_modules,dist,languages} \
	'(AIza[0-9A-Za-z_-]{35}|ghp_[0-9A-Za-z]{36}|github_pat_[0-9A-Za-z_]{80,}|sk-[0-9A-Za-z]{32,})' . || true)

if [[ -n "$SECRET_MATCHES" ]]; then
	echo -e "${YELLOW}[WARN] Found potential hardcoded API key:${NC}"
	echo "$SECRET_MATCHES"
	WARNINGS=$((WARNINGS + 1))
else
	echo -e "${GREEN}[OK] No hardcoded API keys/tokens detected.${NC}"
fi

echo "============================================================"
echo -e "Security Scan Summary: ${ERRORS} critical error(s), ${WARNINGS} warning(s)."
echo "============================================================"

if [[ "$ERRORS" -gt 0 ]]; then
	exit 1
fi
exit 0
