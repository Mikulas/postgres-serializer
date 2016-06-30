#!/usr/bin/env bash
set -euo pipefail
IFS=$'\n\t'
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

"$DIR"/../vendor/nette/tester/Tester/tester -p php "$@"
