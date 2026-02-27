<?php


$dictionary=array(
    'HIKVISION_ADDRESS'=>'Адрес',
    'HIKVISION_USERNAME'=>'Пользователь',
    'HIKVISION_PASSWORD'=>'Пароль',
    'HIKVISION_ADD'=>'Добавить',
    'HIKVISION_CHECK'=>'Проверить',
    'HIKVISION_FOUND'=>'найдено',
    'HIKVISION_CHANGED'=>'изменен',
    'HIKVISION_ABOUT'=>'О модуле',
    'HIKVISION_CLOSE'=>'Закрыть',
    'HIKVISION_SAVE'=>'Сохранить',
    'HIKVISION_HOME'=>'Главная',
    'HIKVISION_INTERCOM_EXISTS'=>'Вызывная панель с этим адресом уже добавлена.',
    'HIKVISION_COLUMN_ID'=>'ID',
    'HIKVISION_COLUMN_MODEL'=>'Модель',
    'HIKVISION_COLUMN_ADDRESS'=>'Адрес',
    'HIKVISION_COLUMN_USER'=>'Пользователь',
    'HIKVISION_COLUMN_POLL_RATE'=>'Время опроса',
    'HIKVISION_COLUMN_STATUS'=>'Статус',
    'HIKVISION_COLUMN_UPDATED_ON'=>'Обновлен',
    'HIKVISION_SECOND'=>'с',
    'HIKVISION_ABOUT_TEXT'=>'Модуль поддержки вызывных панелей Hikvision.<br><br>
               Проект в <a href="https://github.com/stellar-irk/majordomo-hikvision-intercom" target="_blank">Github</a>.<br>
               Канал в <a href="https://t.me/mjdm_zoneminder" target="_blank">Telegram</a>.<br>',
);

foreach ($dictionary as $k=>$v) {
	if (!defined('LANG_'.$k)) {
		define('LANG_'.$k, $v);
	}
}
