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
//echo "<pre>".print_r($rec, true)."</pre>";

//echo("<table border='1'>");
while (1)
//for($i=0;$i<10;$i++)
{
    setGlobal((str_replace('.php', '', basename(__FILE__))) . 'Run', time(), 1);

    foreach ($rec as &$item) {
        $ts = time();
        if ($ts - $item['LATEST_POLL'] >= $item['POLL_RATE']) {
            $res = $hikvision->getIntercomCallStatus($item['ADDRESS'], $item['USERNAME'], $item['PASSWORD']);
            if (!isset($res->error)) {
                if ($item['LINKED_METHOD'])  {
                    $params = array();
                    $params['OLD_VALUE'] = GetGlobal($item['LINKED_OBJECT'].'.'.$item['LINKED_PROPERTY']);
                    $params['NEW_VALUE'] = $res->CallStatus->status;
                    callMethodSafe($item['LINKED_OBJECT'] . '.' . $item['LINKED_METHOD'], $params);
                }
                if ($item['LINKED_PROPERTY']) {
                    $oldvalue = GetGlobal($item['LINKED_OBJECT'].'.'.$item['LINKED_PROPERTY']);
                    $newvalue = $res->CallStatus->status;

                    if ($oldvalue <> $newvalue) {
                        SQLExec("update `hikvision` set `STATUS`='".$newvalue."' where `ID`=".$item['ID']);
                        SetGlobal($item['LINKED_OBJECT'].'.'.$item['LINKED_PROPERTY'], $newvalue, 0);
                    }
                }

               /* echo "<tr><td>".$item['ID']."</td>".
                    "<td>".$ts."</td>".
                    "<td>".$item['LATEST_POLL']."</td>".
                    "<td>".date("H:i:s")."</td>".
                    "<td><pre>".print_r($res, true)."</pre></td></tr>";*/
                $item['LATEST_POLL'] = time();
            } else {
                DebMes("Error getting status of intercom ".$item['ADDRESS'].': '.$res->error , basename(__FILE__, '.php'));
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
//echo("</table>");
DebMes("Unexpected close of cycle: " . basename(__FILE__));