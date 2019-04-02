#!/usr/bin/env bash

if [[ "$REMOVE_XDEBUG" = true ]]; then
  phpenv config-rm xdebug.ini;
fi

echo date.timezone = Europe/Berlin >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
