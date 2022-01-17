#!/usr/bin/env bash

# Move into project root
BIN_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$BIN_DIR"
cd ..

# Exit on errors
set -e

CHANGED_FILES=$(git diff --cached --name-only --diff-filter=ACM -- '***.php')

if [[ -z "$CHANGED_FILES" ]]; then
  echo 'No changed files'
  exit 0
fi

if [[ -x vendor/bin/php-cs-fixer ]]; then
  vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php $CHANGED_FILES
  git add $CHANGED_FILES
else
  echo 'PHP-CS-Fixer is not installed'
  exit 1
fi
