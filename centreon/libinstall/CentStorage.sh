# -*-Shell-script-*-
# install centreon centstorage  

echo "------------------------------------------------------------------------"
echo -e "\t`gettext \"Start CentStorage Installation\"`"
echo "------------------------------------------------------------------------"

## Where is install_dir_centreon ?
locate_centreon_installdir

## locate or create Centreon log dir
locate_centreon_logdir
locate_centreon_etcdir

## Config pre-require
locate_rrd_perldir
locate_rrdtool
#locate_mail
#locate_pear
#locate_nagios_installdir
#locate_nagios_etcdir
#locate_nagios_vardir
#locate_nagios_plugindir
#locate_nagios_binary
#locate_nagiosstats_binary
#locate_nagios_plugindir
#locate_init_d

locate_centstorage_rrddir
## Config Nagios
check_group_nagios
check_user_nagios

## Populate temporaty source directory
copyInTempFile

## Create temporary folder
log "INFO" "`gettext \"Create working directory\"`"
mkdir -p $TMPDIR/final/www/install
mkdir -p $TMPDIR/work/www/install
mkdir -p $TMPDIR/final/bin
mkdir -p $TMPDIR/work/bin
[ ! -d $INSTALL_DIR_CENTREON/examples ] && mkdir -p $INSTALL_DIR_CENTREON/examples

## Change Macro in working dir

sed -e 's|@NAGIOS_VAR@|"$NAGIOS_VAR"|g' \
 -e 's|@NAGIOS_BIN@|"$NAGIOS_BIN"|g' \
 -e 's|@INSTALL_DIR_NAGIOS@|"$INSTALL_DIR_NAGIOS"|g' \
 -e 's|@NAGIOS_USER@|"$NAGIOS_USER"|g' \
 -e 's|@NAGIOS_GROUP@|"$NAGIOS_GROUP"|g' \
 -e 's|@NAGIOS_ETC@|"$NAGIOS_ETC"|g' \
 -e 's|@NAGIOS_PLUGINS@|"$NAGIOS_PLUGIN"|g' \
 -e 's|@RRDTOOL_PERL_LIB@|"$RRD_PERL"|g' \
 -e 's|@RRD_PERL@|"$RRD_PERL"|g' \
 -e 's|@INSTALL_DIR_OREON@|"$INSTALL_DIR_OREON"|g' \
 -e 's|@BIN_RRDTOOL@|"$BIN_RRDTOOL"|g' \
 -e 's|@BIN_MAIL@|"$BIN_MAIL"|g' \
 $TMPDIR/src/www/install/createTablesODS.sql > $TMPDIR/work/www/install/createTablesODS.sql

## Copy in final dir
log "INFO" "`gettext \"Copying www/install/createTablesODS.sql in final directory\"`"
cp $TMPDIR/work/www/install/createTablesODS.sql $TMPDIR/final/www/install/createTablesODS.sql 2>&1 >> $LOG_FILE

## Create CentStorage Status folder
if [ ! -d "$CENTSTORAGE_RRD/status" ] ; then
	log "INFO" "`gettext \"Create CentStorage status directory\"`"
	mkdir $CENTSTORAGE_RRD/status 2>&1 >> $LOG_FILE
	chown $NAGIOS_USER:$NAGIOS_GROUP $CENTSTORAGE_RRD/status 2>&1 >> $LOG_FILE
	chmod 775 $CENTSTORAGE_RRD/status 2>&1 >> $LOG_FILE
	echo_success "`gettext \"Creating Centreon Directory\"` '$CENTSTORAGE_RRD/status'" "$ok"
else
	echo_passed "`gettext \"CentStorage status Directory already exists\"`" "$passed"
fi
## Create CentStorage metrics folder
if [ ! -d "$CENTSTORAGE_RRD/metrics" ] ; then
	log "INFO" "`gettext \"Create CentStorage metrics directory\"`"
	mkdir $CENTSTORAGE_RRD/metrics 2>&1 >> $LOG_FILE
	chown $NAGIOS_USER:$NAGIOS_GROUP $CENTSTORAGE_RRD/metrics 2>&1 >> $LOG_FILE
	chmod 775 $CENTSTORAGE_RRD/metrics 2>&1 >> $LOG_FILE
	echo_success "`gettext \"Creating Centreon Directory\"` '$CENTSTORAGE_RRD/metrics'" "$ok"
else
	echo_passed "`gettext \"CentStorage metrics Directory already exists\"`" "$passed"
fi
    
    
## Change macros in CentStorage binary
sed -e 's|@CENTREON_PATH@|'"$INSTALL_DIR_CENTREON"'|g' \
 -e 's|@RRD_PERL@|'"$RRD_PERL"'|g' \
 $TMPDIR/src/bin/centstorage > $TMPDIR/work/bin/centstorage
echo_success "`gettext \"Replace Centstorage Macro\"`" "$ok"
log "INFO" "`gettext \"Copying CentStorage binary in final directory\"`"
cp $TMPDIR/work/bin/centstorage $TMPDIR/final/bin/centstorage 2>&1 >> $LOG_FILE
chown $NAGIOS_USER:$NAGIOS_GROUP $TMPDIR/final/bin/centstorage
chmod 7755 $TMPDIR/final/bin/centstorage
echo_success "`gettext \"Set CentStorage properties\"`" "$ok"
 	
## Change macros in CentStorage init script
sed -e 's|@CENTREON_PATH@|"$INSTALL_DIR_CENTREON"|g' \
 -e 's|@NAGIOS_USER@|"$NAGIOS_USER"|g' \
 -e 's|@NAGIOS_GROUP@|"$NAGIOS_GROUP"|g' \
 $TMPDIR/src/init.d.centstorage > $TMPDIR/work/init.d.centstorage
echo_success "`gettext \"Replace Centstorage init script Macro\"`" "$ok"
cp $TMPDIR/work/init.d.centstorage $TMPDIR/final/init.d.centstorage
cp $TMPDIR/final/init.d.centstorage $INSTALL_DIR_CENTREON/examples/init.d.centstorage
chmod 755 $TMPDIR/final/init.d.centstorage

yes_no_default "`gettext \"Do you want I install CentStorage init script ?\"`"
if [ $? -eq 0 ] ; then 
	log "INFO" "`gettext \"CentStorage init script installed\"`"
	cp -a $TMPDIR/final/init.d.centstorage $INIT_D/centstorage
	yes_no_default "`gettext \"Do you want I install CentStorage run level ?\"`"
		if [ $? -eq 0 ] ; then
			install_init_service "centstorage"
		fi
else
	echo_passed "`gettext \"CentStorage init script not installed, please use \"`:\n $INSTALL_DIR_CENTREON/examples/init.d.centstorage" "$passed"
	log "INFO" "`gettext \"CentStorage init script not installed, please use \"`: $INSTALL_DIR_CENTREON/examples/init.d.centstorage"
fi

## wait and see...
## sql console inject ?
