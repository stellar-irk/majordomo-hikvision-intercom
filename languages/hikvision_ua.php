<?php


$dictionary=array(
    'HIKVISION_ADDRESS'=>'Адреса',
    'HIKVISION_USERNAME'=>'Користувач',
    'HIKVISION_PASSWORD'=>'Пароль',
    'HIKVISION_ADD'=>'Додати',
    'HIKVISION_CHECK'=>'Перевірити',
    'HIKVISION_FOUND'=>'знайдено',
    'HIKVISION_CHANGED'=>'змінений',
    'HIKVISION_ABOUT'=>'Про модуль',
    'HIKVISION_CLOSE'=>'Закрити',
    'HIKVISION_SAVE'=>'Зберегти',
    'HIKVISION_HOME'=>'Головна',
    'HIKVISION_INTERCOM_EXISTS'=>'Викличну панель з цією адресою вже додано.',
    'HIKVISION_COLUMN_ID'=>'ID',
    'HIKVISION_COLUMN_MODEL'=>'Модель',
    'HIKVISION_COLUMN_ADDRESS'=>'Адреса',
    'HIKVISION_COLUMN_USER'=>'Користувач',
    'HIKVISION_COLUMN_POLL_RATE'=>'Час опитування',
    'HIKVISION_COLUMN_STATUS'=>'Статус',
    'HIKVISION_COLUMN_UPDATED_ON'=>'Оновлено',
    'HIKVISION_SECOND'=>'с',
    'HIKVISION_ABOUT_TEXT'=>'Модуль підтримки викликних панелей Hikvision.<br><br>
               Проект у <a href="https://github.com/stellar-irk/majordomo-hikvision-intercom" target="_blank">Github</a>.<br>
               Канал у <a href="https://t.me/mjdm_zoneminder" target="_blank">Telegram</a>.<br>',
);

foreach ($dictionary as $k=>$v) {
	if (!defined('LANG_'.$k)) {
		define('LANG_'.$k, $v);
	}
}
