#!/usr/bin/env bash

if [[ "$1" != "dev"  && "$1" != "prod" ]]; then
    echo "Env can be 'dev' or 'prod' only"
fi


COPYPATH=env/.env-$1
COPYTO=.env

echo $(pwd)

if [ -f "$COPYPATH" ]; then
    echo "File $COPYPATH exist, initiating..."
else
    echo "env $COPYPATH doesn't exist"
    exit 1
fi

echo "Copying ${COPYPATH} to ${COPYTO} in ${PWD}"

cp $COPYPATH $COPYTO

if [ -f "$COPYTO" ]; then
    echo "OK"
else
    echo "FAIL"
    exit 1
fi