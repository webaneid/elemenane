#!/usr/bin/env python3
"""
Elemen Ane Theme — SCSS & CSS Integrity Audit Script
Verifies:
1. All SCSS files (`scss/*.scss`) and CSS files (`css/*.css`) exist, are non-empty,
   and have balanced curly braces `{` and `}` outside comments/strings.
2. Every non-partial SCSS entrypoint (`scss/[!_]*.scss`) has a corresponding
   compiled `css/<name>.min.css` file.
3. Optional `--compile` flag compiles all `scss/[!_]*.scss` entrypoints into
   `css/<name>.css` and `css/<name>.min.css` if `sass` CLI is available.
"""
import glob
import os
import re
import shutil
import subprocess
import sys

SCSS_DIR = "scss"
CSS_DIR = "css"


def strip_comments_and_strings(text: str, is_scss: bool = False) -> str:
    # Remove /* ... */ block comments
    text = re.sub(r"/\*.*?\*/", "", text, flags=re.DOTALL)
    if is_scss:
        # Remove // single-line comments (ignoring URLs like https://)
        text = re.sub(r"(?<!:)//[^\n]*", "", text)
    # Remove quoted strings ( ignoring #{...} interpolation inside strings for brace balance )
    text = re.sub(r'"(?:\\.|[^"\\])*"', '""', text)
    text = re.sub(r"'(?:\\.|[^'\\])*'", "''", text)
    return text


def check_brace_balance(path: str, is_scss: bool = False) -> tuple[int, int]:
    with open(path, "r", encoding="utf-8") as f:
        raw = f.read()
    cleaned = strip_comments_and_strings(raw, is_scss=is_scss)
    return cleaned.count("{"), cleaned.count("}")


def compile_scss() -> int:
    sass_bin = shutil.which("sass")
    if not sass_bin:
        print("[WARN] `sass` CLI not found in PATH; skipping SCSS compilation.")
        return 0
    entrypoints = sorted(
        p for p in glob.glob(os.path.join(SCSS_DIR, "*.scss"))
        if not os.path.basename(p).startswith("_")
    )
    for src in entrypoints:
        name = os.path.splitext(os.path.basename(src))[0]
        out_expanded = os.path.join(CSS_DIR, f"{name}.css")
        out_min = os.path.join(CSS_DIR, f"{name}.min.css")
        subprocess.run([sass_bin, src, out_expanded], check=True)
        subprocess.run([sass_bin, "--style=compressed", src, out_min], check=True)
        print(f"[COMPILED] {src} -> {out_expanded}, {out_min}")
    return 0


def main() -> int:
    if "--compile" in sys.argv:
        compile_scss()

    if not os.path.isdir(SCSS_DIR):
        print(f"[FAIL] Missing directory: {SCSS_DIR}", file=sys.stderr)
        return 1
    if not os.path.isdir(CSS_DIR):
        print(f"[FAIL] Missing directory: {CSS_DIR}", file=sys.stderr)
        return 1

    scss_files = sorted(glob.glob(os.path.join(SCSS_DIR, "*.scss")))
    css_files = sorted(glob.glob(os.path.join(CSS_DIR, "*.css")))

    if not scss_files:
        print(f"[FAIL] No SCSS files found in {SCSS_DIR}", file=sys.stderr)
        return 1

    errors = 0

    # 1. Check brace balance in all SCSS files
    for path in scss_files:
        if os.path.getsize(path) == 0:
            print(f"[FAIL] {path} is empty.", file=sys.stderr)
            errors += 1
            continue
        opens, closes = check_brace_balance(path, is_scss=True)
        if opens != closes:
            print(
                f"[FAIL] Unbalanced braces in {path}: open={opens}, close={closes}",
                file=sys.stderr,
            )
            errors += 1

    # 2. Check brace balance in all CSS files
    for path in css_files:
        if os.path.getsize(path) == 0:
            print(f"[FAIL] {path} is empty.", file=sys.stderr)
            errors += 1
            continue
        opens, closes = check_brace_balance(path, is_scss=False)
        if opens != closes:
            print(
                f"[FAIL] Unbalanced braces in {path}: open={opens}, close={closes}",
                file=sys.stderr,
            )
            errors += 1

    # 3. Verify every SCSS entrypoint has a corresponding .min.css file
    entrypoints = [
        p for p in scss_files if not os.path.basename(p).startswith("_")
    ]
    for src in entrypoints:
        name = os.path.splitext(os.path.basename(src))[0]
        min_css = os.path.join(CSS_DIR, f"{name}.min.css")
        if not os.path.isfile(min_css) or os.path.getsize(min_css) == 0:
            print(
                f"[FAIL] Missing or empty compiled stylesheet for {src}: {min_css}",
                file=sys.stderr,
            )
            errors += 1

    if errors > 0:
        print(f"[FAIL] CSS/SCSS audit failed with {errors} error(s).", file=sys.stderr)
        return 1

    print(
        f"[OK] Audited {len(scss_files)} SCSS files and {len(css_files)} CSS files "
        f"({len(entrypoints)} entrypoints: {', '.join(os.path.basename(e) for e in entrypoints)}). "
        f"Braces balanced."
    )
    return 0


if __name__ == "__main__":
    sys.exit(main())
