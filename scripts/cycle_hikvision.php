<?php
chdir(dirname(__FILE__) . '/../');
include_once("./config.php");
include_once("./lib/loader.php");
$db = new mysql(DB_HOST, '', DB_USER, DB_PASSWORD, DB_NAME);
include_once("./load_settings.php");
include_once(DIR_MODULES . 'hikvision/hikvision.class.php');
$hikvision = new hikvision();

$rec = SQLSelect("select * from `hikvision`");
foreach ($rec as &$item) $item['LATEST_POLL'] = time();
unset($item);

echo date("H:i:s") . " running " . basename(__FILE__) . PHP_EOL;

while (1)
{
    setGlobal((str_replace('.php', '', basename(__FILE__))) . 'Run', time(), 1);

    foreach ($rec as &$item) {
        $ts = time();
        if ($ts - $item['LATEST_POLL'] >= $item['POLL_RATE']) {
            $res = $hikvision->getIntercomCallStatus($item['ADDRESS'], $item['USERNAME'], $item['PASSWORD']);
            if (!isset($res->error)) {
                $oldvalue = SQLSelectOne("select `STATUS` from `hikvision` where `ID` = ".$item['ID'])['STATUS'];
                $newvalue = $res->CallStatus->status;

                if ($oldvalue <> $newvalue) SQLExec("update `hikvision` set `STATUS`='".$newvalue."' where `ID`=".$item['ID']);
                SQLExec("update `hikvision` set `UPDATED_ON`='".date('Y-m-d H:i:s')."' where `ID`=".$item['ID']);

                if ($item['LINKED_METHOD'])  {
                    $params = array();
                    $params['OLD_VALUE'] = $oldvalue;
                    $params['NEW_VALUE'] = $newvalue;
                    callMethodSafe($item['LINKED_OBJECT'] . '.' . $item['LINKED_METHOD'], $params);
                }
                if ($item['LINKED_PROPERTY'] && oldvalue <> $newvalue)
                    SetGlobal($item['LINKED_OBJECT'].'.'.$item['LINKED_PROPERTY'], $newvalue, 0);

               /* echo $item['ADDRESS']."\t".
                    $oldvalue."\t".
                    $newvalue."\t".
                    date("H:i:s")."\r\n";
                    //print_r($res, true)."</pre></td></tr>";*/
                $item['LATEST_POLL'] = time();
            }
        }
    }
    unset($item);

    if (file_exists('./reboot') || IsSet($_GET['onetime'])){
        $db->Disconnect();
        exit;
    }
    sleep(1);
}
$db->Disconnect();
DebMes("Unexpected close of cycle: " . basename(__FILE__));