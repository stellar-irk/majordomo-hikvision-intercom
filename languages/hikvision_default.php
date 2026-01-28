<?php


$dictionary=array(
    'HIKVISION_ABOUT'=>'About',
    'HIKVISION_CLOSE'=>'Close',
    'HIKVISION_ABOUT_TEXT'=>'Hikvision Intercom support module.<br><br>
               Project on <a href="https://github.com/layet/majordomo-hikvision-intercom" target="_blank">Github</a>.<br>
               Channel on <a href="https://t.me/mjdm_zoneminder" target="_blank">Telegram</a>.<br>',
);

foreach ($dictionary as $k=>$v) {
	if (!defined('LANG_'.$k)) {
		define('LANG_'.$k, $v);
	}
}
