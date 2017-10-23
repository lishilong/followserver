include mydev
#Create a file named "mydev"
#Create a file named "mydev"
#Write your dev machine's account&address in "mydev"
#eg:dev="work@m1-wise-ttrd33.vm"
#also you can run this Command: echo dev=\"work@cq01-baijianmin.epc.baidu.com\" > mydev

## Description: Makefile
# Common conf
APP_PATH="/home/work/orp/app/followserver"
CONF_PATH="/home/work/orp/conf/app/followserver"
WEBROOT_PATH="/home/work/orp/webroot/followserver"
APP_SRC_FILE=actions Bootstrap.php controllers library models plugins crontab qpt script
# Backup conf
BACKUPDIR=/home/work/backup/orp/app/`date +%Y-%m-%d`/
BACKFILE=`date +%H-%M-%S`.followserver.tar.gz
BACKCODE=/home/work/orp/conf/app/followserver/ /home/work/orp/app/followserver/ 
# syntax conf
SYNTAXPATH=/home/work/orp/app/followserver/ /home/work/orp/webroot/followserver

dev  :
	@# backup old code
	@echo -e "\033[32m\033[1m[ info ]\033[0m Dev = $(dev)"
	@echo "[step-1] back you old code in you dev machine...";
	@ssh $(dev) "mkdir -p $(BACKUPDIR); cd $(BACKUPDIR); tar zcPf $(BACKFILE) $(BACKCODE)"

	@# deloy orp followserver code
	@echo "[step-2] deploy your develop followserver orp code...";
	@ssh $(dev) "mkdir -p $(CONF_PATH); mkdir -p $(WEBROOT_PATH);"
	@#ssh $(dev) "mkdir -p $(CONF_PATH); mkdir -p $(WEBROOT_PATH);"
	@#先将文件打包再传输
	@tar czf followserver.tgz $(APP_SRC_FILE) conf;
	@scp -r followserver.tgz $(dev):$(APP_PATH) 1>/dev/null; scp index.php $(dev):$(WEBROOT_PATH) 1>/dev/null;
	@rm followserver.tgz
	@ssh $(dev) "cd $(APP_PATH); tar xzf followserver.tgz; \rm followserver.tgz; find . -type d -name .svn | xargs -i rm -rf {}; mv conf/* $(CONF_PATH); \rm -rf conf";
	@echo "[step-3] deploy Success...";

syntax  :dev
	@# check PHP syntax
	@echo "[step-4] check php syntax...";
	@ssh $(dev) "find $(SYNTAXPATH) -type f -name '*.php' -exec /home/work/php/bin/php -ln {} \; | grep -v 'No syntax'"
