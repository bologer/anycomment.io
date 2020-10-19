#!/bin/bash

# Notice: "/" at the end of source path
# Usage example: ./scripts/deploy.sh /path/to/wp-content/plugins/anycomment/ /path/to/anycomment/trunk

PLUGINSOURCEPATH=$1
TRUNKFOLDERPATH=$2

if [ ! -d "$PLUGINSOURCEPATH" ]
then
      echo "[ERR] Plugin source path cannot be empty or it does not exist"
      exit 1
fi

if [ ! -d "$TRUNKFOLDERPATH" ]
then
      echo "[ERR] Trunk path cannot be empty or it does not exist"
      exit 1
fi

cd $TRUNKFOLDERPATH || exit
echo "Now removing all from here $TRUNKFOLDERPATH"
rm -rf *
echo "Going back to $PLUGINSOURCEPATH"
cd $PLUGINSOURCEPATH || exit
echo "Now in $PLUGINSOURCEPATH"
echo "Ready to move files from $PLUGINSOURCEPATH to $TRUNKFOLDERPATH..."
rsync -arv --cvs-exclude \
  --exclude='.gitignore' \
  --exclude='.git' \
  --exclude='assets/src/anycomment' \
  --exclude='README.md'\
  --exclude=node_modules \
  --exclude=reactjs \
  --exclude=scripts \
  --exclude=gulpfile.js \
  --exclude=package.json \
  --exclude=package-lock.json \
  --exclude=.travis.yml \
  --exclude=phpunit.xml \
  --exclude=vendor/bin \
  $PLUGINSOURCEPATH \
  $TRUNKFOLDERPATH
echo "OK"
echo "Moved all files to trunk folder: $TRUNKFOLDERPATH"
