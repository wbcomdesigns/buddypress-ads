#!/usr/bin/env bash
# Build the release zip from a committed ref, never the working folder, so the
# zip always equals the repo. Dev files are excluded via .gitattributes export-ignore.
# Usage: bin/build-zip.sh [ref]   (default HEAD) -> release-zip/buddypress-ads-<version>.zip
set -euo pipefail
cd "$(dirname "$0")/.."
ref="${1:-HEAD}"
version="$(git show "$ref:buddypress-ads-rotator.php" | sed -n 's/^ \* Version: *//p' | tr -d '\r ')"
mkdir -p release-zip
out="release-zip/buddypress-ads-${version}.zip"
git archive --format=zip --prefix=buddypress-ads/ -o "$out" "$ref"
echo "$out ($(unzip -l "$out" | tail -1 | awk '{print $2}') files)"
