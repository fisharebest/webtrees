#!/bin/bash

### init some used functions
SCRIPT_DIR=$(cd "$(dirname "$0")" && pwd)

### configuration
OS=linux
SERVER=${1:-apache2}

VAGRANT_FILE_PATH="tools/vagrant/${OS}/${SERVER}"
SETUP_DIR="$SCRIPT_DIR"

function checkout_submodule() {
    cd "$SCRIPT_DIR"
    git submodule init
    git submodule update
}

function setup_vagrant() {
    . "$SCRIPT_DIR/tools/vagrant/linux/functions.sh"

    $DIFF_TOOL -Nr --exclude='*id_rsa*' "$SCRIPT_DIR/${VAGRANT_FILE_PATH}/puphpet/" "$SETUP_DIR/puphpet/"
    NEW=$?

    if [[ ${NEW} -gt 0 ]]; then
        rsync -av --delete --exclude=files/dot/ssh/ "$SCRIPT_DIR/${VAGRANT_FILE_PATH}/puphpet/" "$SETUP_DIR/puphpet/"
        rsync -av "$SCRIPT_DIR/${VAGRANT_FILE_PATH}/puphpet/files/dot/ssh/" "$SETUP_DIR/puphpet/files/dot/ssh/"
        cp "$SCRIPT_DIR/${VAGRANT_FILE_PATH}/Vagrantfile" ./
        log DONE "copying from $SCRIPT_DIR/${VAGRANT_FILE_PATH}"
    else
        log DONE "no changes detected -> nothing to do"
    fi

    log INFO "You should run now 'vagrant up' or 'vagrant provision' now... "
}

if [[ ! -f tools/vagrant/LICENSE ]]; then
    checkout_submodule
fi

setup_vagrant