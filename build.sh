#!/bin/sh
APP_NAME="followserver"
BUILD_NAME="${APP_NAME}.tar.gz"
rm -rf output

mkdir -p output/app/$APP_NAME
mkdir -p output/webroot/$APP_NAME
mkdir -p output/conf/app/$APP_NAME

cp -r actions controllers library models Bootstrap.php plugins script output/app/$APP_NAME/
cp -r index.php  output/webroot/$APP_NAME/
cp -r conf/* output/conf/app/$APP_NAME/


cd output
find ./ -name .svn -exec rm -rf {} \;
tar cvzf $BUILD_NAME app conf webroot
rm -rf app conf webroot