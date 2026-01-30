<?php


$dictionary=array(
    'HIKVISION_ADDRESS'=>'Address',
    'HIKVISION_USERNAME'=>'Username',
    'HIKVISION_PASSWORD'=>'Password',
    'HIKVISION_ADD'=>'Add',
    'HIKVISION_CHECK'=>'Check',
    'HIKVISION_FOUND'=>'found',
    'HIKVISION_ABOUT'=>'About',
    'HIKVISION_CLOSE'=>'Close',
    'HIKVISION_SAVE'=>'Save',
    'HIKVISION_HOME'=>'Home',
    'HIKVISION_INTERCOM_EXISTS'=>'The Intercom with this address already added.',
    'HIKVISION_COLUMN_MODEL'=>'Model',
    'HIKVISION_COLUMN_ADDRESS'=>'Address',
    'HIKVISION_COLUMN_USER'=>'Username',
    'HIKVISION_ABOUT_TEXT'=>'Hikvision Intercom support module.<br><br>
               Project on <a href="https://github.com/layet/majordomo-hikvision-intercom" target="_blank">Github</a>.<br>
               Channel on <a href="https://t.me/mjdm_zoneminder" target="_blank">Telegram</a>.<br>',
);

foreach ($dictionary as $k=>$v) {
	if (!defined('LANG_'.$k)) {
		define('LANG_'.$k, $v);
	}
}
