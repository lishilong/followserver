#!/bin/bash
#########################################################################
#   File: link_src_to_env.sh
#########################################################################
src_path=$(cd `dirname $0`; pwd)
product_name="followserver"
app_name="followserver"
orppath="/home/work/orp"

echo "You ORP path: $orppath"
app_path=$orppath/app/$app_name
appconf_path=$orppath/conf/app/$app_name
appwebroot=$orppath/webroot/$app_name

#if [ ! -d $app_path  ]
#then
 #   echo "ORP directory does not exist!"
  #  exit 4
#fi

echo -n -e "do you want to clear it? \n$app_path \n$appconf_path \n$appwebroot \ndo you want to clear it? (yes or no)"
while true; do
    read answer
    if [ "$answer" == "no" ]; then
        exit 4
    elif [ "$answer" == "yes" ]; then
        break
    fi
    echo -n "Enter yes or no:"
done

rm -rfv $app_path
rm -rfv $appconf_path
rm -rfv $appwebroot
mkdir -p $app_path
mkdir -p $appconf_path
mkdir -p $appwebroot
echo "Link src to env ..."
ln -sv $src_path/actions $app_path/actions
ln -sv $src_path/plugins $app_path/plugins
ln -sv $src_path/controllers $app_path/controllers
ln -sv $src_path/library $app_path/library
ln -sv $src_path/models $app_path/models
ln -sv $src_path/script $app_path/script
ln -sv $src_path/Bootstrap.php $app_path/Bootstrap.php
ln -sv $src_path/index.php $appwebroot
ln -sv $src_path/conf/* $appconf_path

ln -svf $src_path/testconf/ral/services/* $orppath/conf/ral/services/

# ln Debug Redis2.php to Env --- End
echo "Link src to env END!!!"


