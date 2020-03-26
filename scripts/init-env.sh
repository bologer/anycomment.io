#!/usr/bin/env bash

CONFIG_PATH="env/.env-$1"

if [ ! -f "$CONFIG_PATH" ]; then
    echo "Environment $1 does not exist. Available: dev/prod."
    exit 1
fi

/bin/cp -rf $CONFIG_PATH .env
