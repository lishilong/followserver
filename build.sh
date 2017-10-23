#!/bin/bash
PRODUCT_NAME="followserver"
APP_NAME="followserver"

usecache_switch=`cat ./conf/global.conf | grep 'usecache' | tr -d ' ' | sed 's/usecache://g'`
setcache_switch=`cat ./conf/global.conf | grep 'setcache' | tr -d ' ' | sed 's/setcache://g'`
if [ $usecache_switch -ne 1 -o $setcache_switch -ne 1 ];then
	echo 'check usecache or setcache switch error'
	exit 1;
else
	echo 'check usecache and setcache switch ok'
fi

/bin/rm -rf output
mkdir -p output/app/$APP_NAME
mkdir -p output/conf/app/$APP_NAME
mkdir -p output/webroot/$APP_NAME

cp -r actions controllers library models Bootstrap.php plugins script output/app/$APP_NAME
cp -r conf/*  output/conf/app/$APP_NAME
cp -r index.php  output/webroot/$APP_NAME

cd output
find ./ -name .svn |xargs rm -rf {} \;
tar zcvf app_$APP_NAME.tar.gz app conf webroot > /dev/null
/bin/rm -rf app conf webroot
cd -

echo '###########################################'
echo 'followserver app build success!'
echo '###########################################'
