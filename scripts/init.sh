#!/usr/bin/env bash

function twoSpaces() {
  echo ""
  echo ""
}

echo "                        _____                                     _   "
echo "     /\                / ____|                                   | |  "
echo "    /  \   _ __  _   _| |     ___  _ __ ___  _ __ ___   ___ _ __ | |_ "
echo "   / /\ \ | '_ \| | | | |    / _ \| '_ \` _ \| '_ \` _ \ / _ \ '_ \| __|"
echo "  / ____ \| | | | |_| | |___| (_) | | | | | | | | | | |  __/ | | | |_ "
echo " /_/    \_\_| |_|\__, |\_____\___/|_| |_| |_|_| |_| |_|\___|_| |_|\__|"
echo "                  __/ |                                               "
echo "                 |___/                                                "

twoSpaces

if [ -z "$1" ]; then
  echo "Please select the mode you will working in:"
  echo "php - I will be working just with php"
  echo "react - I will be working just with react"
  echo "both - I will be working with php & react"
  read -p "Please select the mode php/react/both: " side

  while [[ "$side" != "php" && "$side" != "react" && "$side" != "both" ]];
  do
    read -p "[ERR] You need to provide proper mode: php/react/both: " side
  done
else
  side=$1
fi;

ROOT_PATH=$(pwd)

function installPHP() {
  COMPOSER_PATH=$(command -v composer)

  if [ -z "$COMPOSER_PATH" ]
  then
    echo "[ERR] Please download & install Comsposer and try again"
    echo "[ERR] URL: https://getcomposer.org/download/"
    exit 1
  fi

  composer --no-interaction install
}

function installReact() {
  NODE_PATH=$(command -v node)

  if [ -z "$NODE_PATH" ]
  then
    echo "[ERR] Please download & install NodeJS and try again"
    echo "[ERR] URL: https://nodejs.org/en/download/"
    exit 1
  fi

  cd reactjs || exit
  rm -rf .cache dist
  echo "[INFO] Installing NPM packages"
  npm install

  cd $ROOT_PATH || exit
  ./scripts/init-dev.sh dev

  echo "[INFO] Enabled debug mode in plugin, to use development script version"
}

if [ "$side" == "php" ]
then
  installPHP
fi

if [ "$side" == "react" ]
then
  installReact
fi

if [ "$side" == "both" ]
then
  installPHP
  installReact
fi

twoSpaces

