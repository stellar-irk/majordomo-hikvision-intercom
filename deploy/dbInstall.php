<?php
echo("Hikvision Intercom DB Install script");

$mjdm_dir = '/var/www/html/';
chdir($mjdm_dir);
include_once($mjdm_dir."config.php");
include_once($mjdm_dir."lib/loader.php");
include_once($mjdm_dir."load_settings.php");
include_once($mjdm_dir."modules/hikvision/hikvision.class.php");

$zm = new hikvision();
$zm->dbInstall('');