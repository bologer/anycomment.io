#!/usr/bin/env bash
# Script is meant to package plugin into ZIP archive so it can be easily used for testing purposes
# This script SHOULD NOT be used for release purposes

# Temp DIR where to store code
TEMP_PATH="/tmp"

# ZIP file name
ZIP_FILE_NAME=anycomment

read -p "Enter plugin root: " plugin_root
echo
read -p "Enter archive path: " save_path
echo
echo "Trying to get files in $plugin_root and then save archive in $save_path"
echo
cd ..

rsync -arv --cvs-exclude \
    --exclude='.gitignore' \
    --exclude='assets/src' \
    --exclude='scripts' \
    --exclude='test' \
    --exclude='CHANGELOG.md' \
    --exclude='README.md' \
    --exclude='node_modules' \
    --exclude='gulpfile.js' \
    --exclude='package.json' \
    --exclude='package-lock.json' \
    --exclude='.travis.yml' \
    --exclude='phpunit.xml' \
    --exclude='vendor/bin' \
    --exclude='*.js.map' \
    --exclude='*.css.map' \
    $plugin_root \
    $TEMP_PATH


# Packaging
echo "Ready to ZIP package in $TEMP_PATH and move to $save_path"
cd $TEMP_PATH
zip -r $ZIP_FILE_NAME anycomment/*
echo "Moving "${TEMP_PATH}/${ZIP_FILE_NAME}.zip" to $save_path"
mv "${TEMP_PATH}/${ZIP_FILE_NAME}.zip" $save_path

# Cleaning
echo "ZIP should've been moved, now cleaning"
rm -f "$TEMP_PATH.zip"

# Done
echo "============= DONE ============="
echo "ZIP located in ${TEMP_PATH}/${save_path}.zip"
echo "============= DONE ============="
