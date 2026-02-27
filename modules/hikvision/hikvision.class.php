<?php
/**
 * Hikvision Intercom
 * @package project
 * @author Layet <layet@yandex.ru>
 * @copyright http://majordomo.smartliving.ru/ (c)
 * @version 1.0 (layet, 12:07:48 [Jan 28, 2026])
 */
//

class hikvision extends module {
    /**
     * hikvision
     *
     * Module class constructor
     *
     * @access private
     */
    function __construct() {
        $this->name="hikvision";
        $this->title="Hikvision Intercom";
        $this->module_category="<#LANG_SECTION_DEVICES#>";
        $this->checkInstalled();

        $this->getConfig();
    }
    /**
     * saveParams
     *
     * Saving module parameters
     *
     * @access public
     */
    function saveParams($data=1) {
        $p=array();
        if (IsSet($this->id)) {
            $p["id"]=$this->id;
        }
        if (IsSet($this->view_mode)) {
            $p["view_mode"]=$this->view_mode;
        }
        if (isset($mode)) {
            $p["mode"]=$this->mode;
        }
        if (isset($page)) {
            $p["page"]=$this->page;
        }
        return parent::saveParams($p);
    }
    /**
     * getParams
     *
     * Getting module parameters from query string
     *
     * @access public
     */
    function getParams() {
        global $id;
        global $mode;
        global $view_mode;
        global $page;
        if (isset($id)) {
            $this->id=$id;
        }
        if (isset($mode)) {
            $this->mode=$mode;
        }
        if (isset($view_mode)) {
            $this->view_mode=$view_mode;
        }
        if (isset($page)) {
            $this->page=$page;
        }
    }
    /**
     * Run
     *
     * Description
     *
     * @access public
     */
    function run() {
        global $session;
        $out=array();

        if ($this->action=='admin') {
            $this->admin($out);
        } else {
            $this->usual($out);
        }
        if (IsSet($this->owner->action)) {
            $out['PARENT_ACTION']=$this->owner->action;
        }
        if (IsSet($this->owner->name)) {
            $out['PARENT_NAME']=$this->owner->name;
        }
        $out['VIEW_MODE']=$this->view_mode;
        $out['MODE']=$this->mode;
        $out['ACTION']=$this->action;
        $this->data=$out;
        $p=new parser(DIR_TEMPLATES.$this->name."/".$this->name.".html", $this->data, $this);
        $this->result=$p->result;
    }

    /**
     * FrontEnd
     *
     * Module frontend
     *
     * @access public
     */
    function usual(&$out) {
        if ($this->ajax == 1) {
            $op = htmlspecialchars($_GET['op']);
            if ($op == 'check') {
                $this->ajaxCheckIntercom();
                exit;
            }
            if ($op == 'edit') {
                $this->ajaxEditIntercom();
                exit;
            }
            if ($op == 'status') {
                $this->ajaxGetIntercomStatus();
                exit;
            }
            if ($op == 'open') {
                $this->ajaxOpenIntercomDoor();
                exit;
            }
            if ($op == 'reject') {
                $this->ajaxRejectIntercomCall();
                exit;
            }
            if ($op == 'image') {
                $this->ajaxGetImageFromIntercom();
                exit;
            }
        }

        if ($this->view_mode == '') {
            $out['ID'] = $this->id;

        }
    }

    /**
     * BackEnd
     *
     * Module backend
     *
     * @access public
     */
    function admin(&$out) {
        $this->getConfig();

        if ($this->view_mode == '') {
            $r = SQLSelect("select * from `hikvision`");
            for($i=0;$i<count($r);$i++) {
                switch ($r[$i]['MODEL']) {
                    case 'DS-KV6103-PE1(C)': $r[$i]['MODEL_IMG'] = '/templates/hikvision/model_img/DS-KV6103-PE1C.png';
                        break;
                    case 'DS-KV6113-PE1(C)': $r[$i]['MODEL_IMG'] = '/templates/hikvision/model_img/DS-KV6103-PE1C.png';
                        break;
                    case 'DS-KV6113-WPE1(C)': $r[$i]['MODEL_IMG'] = '/templates/hikvision/model_img/DS-KV6103-PE1C.png';
                        break;
                    case 'DS-KV6114-E1': $r[$i]['MODEL_IMG'] = '/templates/hikvision/model_img/DS-KV6114-E1.png';
                        break;
                    case 'DS-KV6114-WBE1': $r[$i]['MODEL_IMG'] = '/templates/hikvision/model_img/DS-KV6114-E1.png';
                        break;
                    case 'DS-KV6114-ME1': $r[$i]['MODEL_IMG'] = '/templates/hikvision/model_img/DS-KV6114-ME1.png';
                        break;
                    case 'DS-KV6114-MWBE1': $r[$i]['MODEL_IMG'] = '/templates/hikvision/model_img/DS-KV6114-ME1.png';
                        break;
                    case 'DS-KV6124-E1': $r[$i]['MODEL_IMG'] = '/templates/hikvision/model_img/DS-KV6124-E1.png';
                        break;
                    case 'DS-KV6124-WBE1': $r[$i]['MODEL_IMG'] = '/templates/hikvision/model_img/DS-KV6124-E1.png';
                        break;
                    case 'DS-KV6133-ME1': $r[$i]['MODEL_IMG'] = '/templates/hikvision/model_img/DS-KV6133-ME1.png';
                        break;
                    case 'DS-KV6133-WME1': $r[$i]['MODEL_IMG'] = '/templates/hikvision/model_img/DS-KV6133-ME1.png';
                        break;
                    default: $r[$i]['MODEL_IMG'] = '/templates/hikvision/model_img/DEFAULT.png';
                }
            }
            $out['intercoms'] = $r;
        }

        if ($this->view_mode == 'intercom_edit') {
            $this->editIntercom($out, $this->id);
        }

        if ($this->view_mode == 'intercom_delete') {
            $this->deleteIntercom($this->id);
            $this->redirect('?');
        }
    }


    /**
     * getImageFromIntercom
     *
     * Get Image from url with Digest Auth
     *
     * @access public
     */
    function getImageFromIntercom($url, $username, $password) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects if any
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password); // Set username and password
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST); // Specify Digest authentication
        $response = curl_exec($ch);
        $result = new StdClass();
        if (curl_errno($ch)) {
            $result->error = curl_error($ch);
            curl_close($ch);
            return $result;
        } else {
            // Check HTTP status code (e.g., 200 OK, 401 Unauthorized)
            $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($status_code != 200) {
                $result->error = "Response with Status Code [" . $status_code . "].";
                curl_close($ch);
                return $result;
            } else {
                header("Content-Type: image/jpeg");
                curl_close($ch) ;
                echo $response;
            }
        }
    }

    /**
     * getDataFromIntercom
     *
     * Get Data from url with Digest Auth
     *
     * @access public
     */
    function getDataFromIntercom($url, $username, $password): StdClass {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects if any
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password); // Set username and password
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST); // Specify Digest authentication
        $response = curl_exec($ch);
        $result = new StdClass();
        if (curl_errno($ch)) {
            $result->error = curl_error($ch);
            curl_close($ch);
            return $result;
        } else {
            // Check HTTP status code (e.g., 200 OK, 401 Unauthorized)
            $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($status_code != 200) {
                $result->error = "Response with Status Code [" . $status_code . "].";
                curl_close($ch);
                return $result;
            } else {
                $xml = simplexml_load_string($response);
                curl_close($ch);
                if ($xml === false) return json_decode($response);
                    else return json_decode(json_encode($xml));
            }
        }
    }

    /**
     * putDataToIntercom
     *
     * Get Data from url with Digest Auth
     *
     * @access public
     */
    function putDataToIntercom($url, $username, $password, $data): StdClass {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects if any
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data)));
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password); // Set username and password
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST); // Specify Digest authentication
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($ch);
        $result = new StdClass();
        if (curl_errno($ch)) {
            $result->error = curl_error($ch);
            curl_close($ch);
            return $result;
        } else {
            // Check HTTP status code (e.g., 200 OK, 401 Unauthorized)
            $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($status_code != 200) {
                $result->error = "Response with Status Code [" . $status_code . "].";
                curl_close($ch);
                return $result;
            } else {
                $xml = simplexml_load_string($response);
                curl_close($ch);
                if ($xml === false) return json_decode($response);
                else return json_decode(json_encode($xml));
            }
        }
    }

    /**
     * editIntercom
     *
     * Edit Intercom View
     *
     * @access public
     */
    function editIntercom(&$out, $id) {
        $r = SQLSelectOne("select * from `hikvision` where ID='".DBSafe1($id)."'");
        switch ($r['MODEL']) {
            case 'DS-KV6103-PE1(C)': $r['MODEL_IMG'] = '/templates/hikvision/model_img/DS-KV6103-PE1C.png';
                break;
            case 'DS-KV6113-PE1(C)': $r['MODEL_IMG'] = '/templates/hikvision/model_img/DS-KV6103-PE1C.png';
                break;
            case 'DS-KV6113-WPE1(C)': $r['MODEL_IMG'] = '/templates/hikvision/model_img/DS-KV6103-PE1C.png';
                break;
            case 'DS-KV6114-E1': $r['MODEL_IMG'] = '/templates/hikvision/model_img/DS-KV6114-E1.png';
                break;
            case 'DS-KV6114-WBE1': $r['MODEL_IMG'] = '/templates/hikvision/model_img/DS-KV6114-E1.png';
                break;
            case 'DS-KV6114-ME1': $r['MODEL_IMG'] = '/templates/hikvision/model_img/DS-KV6114-ME1.png';
                break;
            case 'DS-KV6114-MWBE1': $r['MODEL_IMG'] = '/templates/hikvision/model_img/DS-KV6114-ME1.png';
                break;
            case 'DS-KV6124-E1': $r['MODEL_IMG'] = '/templates/hikvision/model_img/DS-KV6124-E1.png';
                break;
            case 'DS-KV6124-WBE1': $r['MODEL_IMG'] = '/templates/hikvision/model_img/DS-KV6124-E1.png';
                break;
            case 'DS-KV6133-ME1': $r['MODEL_IMG'] = '/templates/hikvision/model_img/DS-KV6133-ME1.png';
                break;
            case 'DS-KV6133-WME1': $r['MODEL_IMG'] = '/templates/hikvision/model_img/DS-KV6133-ME1.png';
                break;
            default: $r['MODEL_IMG'] = '/templates/hikvision/model_img/DEFAULT.png';
        }
        $out['id'] = $r['ID'];
        $out['address'] = $r['ADDRESS'];
        $out['username'] = $r['USERNAME'];
        $out['password'] = $r['PASSWORD'];
        $out['model'] = $r['MODEL'];
        $out['model_img'] = $r['MODEL_IMG'];
        $out['pollrate'] = $r['POLL_RATE'];
        $out['LINKED_OBJECT'] = $r['LINKED_OBJECT'];
        $out['LINKED_PROPERTY'] = $r['LINKED_PROPERTY'];
        $out['LINKED_METHOD'] = $r['LINKED_METHOD'];
    }

    /**
     * deleteIntercom
     *
     * Delete Intercom View
     *
     * @access public
     */
    function deleteIntercom($id) {
        SQLExec("delete from `hikvision` where `ID`='".DBSafe1($id)."'");
    }

    function ajaxGetImageFromIntercom()
    {
        $id = htmlspecialchars($_GET['id']);
        $db = SQLSelectOne("select * from `hikvision` where `ID`='".$id."'");

        $this->getImageFromIntercom("http://".$db['ADDRESS']."/ISAPI/Streaming/channels/101/picture", $db['USERNAME'], $db['PASSWORD']);
    }

    /**
     * ajaxCheckIntercom
     *
     * Check Intercom by ISAPI
     *
     * @access public
     */
    function ajaxCheckIntercom() {
        $address = htmlspecialchars($_POST['address']);
        $username = htmlspecialchars($_POST['user_name']);
        $password = $_POST['user_pass'];
        $pollrate = htmlspecialchars($_POST['poll_rate']);

        $intercom = $this->getDataFromIntercom('http://'.$address.'/ISAPI/System/deviceInfo', $username, $password);
        if (!isset($intercom->error)) {
            $dbrecord = SQLSelectOne("select `ID` from `hikvision` where `ADDRESS`='".DBSafe1($address)."'");
            if (isset($dbrecord['ID'])) {
                $intercom->error = LANG_HIKVISION_INTERCOM_EXISTS;
            } else {
                $dbrecord['MODEL'] = $intercom->model;
                $dbrecord['ADDRESS'] = $address;
                $dbrecord['USERNAME'] = $username;
                $dbrecord['PASSWORD'] = $password;
                $dbrecord['POLL_RATE'] = $pollrate;

                SQLInsert('hikvision', $dbrecord);
            }
        }
        echo json_encode($intercom);
    }

    /**
     * ajaxEditIntercom
     *
     * Check Intercom by ISAPI
     *
     * @access public
     */
    function ajaxEditIntercom() {
        $id = htmlspecialchars($_POST['intercom-id']);
        $address = htmlspecialchars($_POST['address']);
        $username = htmlspecialchars($_POST['user_name']);
        $password = $_POST['user_pass'];
        $pollrate = htmlspecialchars($_POST['poll_rate']);
        $linked_object = htmlspecialchars($_POST['linked_object']);
        $linked_property = htmlspecialchars($_POST['linked_property']);
        $linked_method = htmlspecialchars($_POST['linked_method']);

        $intercom = $this->getDataFromIntercom('http://'.$address.'/ISAPI/System/deviceInfo', $username, $password);
        if (!isset($intercom->error)) {
            $dbrecord = SQLSelectOne("select * from `hikvision` where ID='".DBSafe1($id)."'");
            $old_linked_object = $dbrecord['LINKED_OBJECT'];
            $old_linked_property = $dbrecord['LINKED_PROPERTY'];
            $dbrecord['ID'] = $id;
            $dbrecord['MODEL'] = $intercom->model;
            $dbrecord['ADDRESS'] = $address;
            $dbrecord['USERNAME'] = $username;
            $dbrecord['PASSWORD'] = $password;
            $dbrecord['POLL_RATE'] = $pollrate;
            $dbrecord['LINKED_OBJECT'] =$linked_object;
            $dbrecord['LINKED_PROPERTY'] =$linked_property;
            $dbrecord['LINKED_METHOD'] =$linked_method;

            SQLUpdate('hikvision', $dbrecord);

            if ($old_linked_object != $dbrecord['LINKED_OBJECT'] && $old_linked_property != $dbrecord['LINKED_PROPERTY']) {
                removeLinkedProperty($old_linked_object, $old_linked_property, $this->name);
            }

            if ($dbrecord['LINKED_OBJECT'] && $dbrecord['LINKED_PROPERTY']) {
                addLinkedProperty($dbrecord['LINKED_OBJECT'], $dbrecord['LINKED_PROPERTY'], $this->name);
            }
        }
        echo json_encode($intercom);
    }

    /**
     * ajaxGetIntercomStatus
     *
     * Get Intercom Status from DB
     *
     * @access public
     */
    function ajaxGetIntercomStatus() {
        $res  = SQLSelect('select `ID`, `STATUS`, `UPDATED_ON` from `hikvision`');
        if ($res === 0) {
            $res = new StdClass;
            $res->error = 'DB Error';
        }

        echo json_encode($res);
    }

    /**
     * ajaxOpenIntercomDoor
     *
     * Open Door Intercom Command
     *
     * @access public
     */
    function ajaxOpenIntercomDoor() {
        $id = htmlspecialchars($_GET['id']);
        $db = SQLSelectOne("select * from `hikvision` where `ID`='".$id."'");

        $res = $this->putDataToIntercom('http://'.$db['ADDRESS'].'/ISAPI/AccessControl/RemoteControl/door/1', $db['USERNAME'], $db['PASSWORD'],
        "<?xml version='1.0' encoding='utf-8'?><RemoteControlDoor xmlns=\"http://www.isapi.org/ver20/XMLSchema\" version=\"2.0\"><cmd>open</cmd></RemoteControlDoor>");

        echo "<pre>".print_r($res, true)."</pre>";
    }

    /**
     * ajaxRejectIntercomCall
     *
     * Reject Intercom Call
     *
     * @access public
     */
    function ajaxRejectIntercomCall() {
        $id = htmlspecialchars($_GET['id']);
        $db = SQLSelectOne("select * from `hikvision` where `ID`='".$id."'");

        $res = $this->putDataToIntercom('http://'.$db['ADDRESS'].'/ISAPI/VideoIntercom/callSignal?format=json', $db['USERNAME'], $db['PASSWORD'],
            '{"CallSignal":{"cmdType": "reject"}}');

        echo "<pre>".print_r($res, true)."</pre>";
    }

    /**
     * getIntercomCallStatus
     *
     * Check Intercom CallStatus by ISAPI
     *
     * @access public
     */
    function getIntercomCallStatus($address, $username, $password): StdClass
    {
        return $this->getDataFromIntercom('http://'.$address.'/ISAPI/VideoIntercom/callStatus', $username, $password);
    }

    /**
     * Install
     *
     * Module installation routine
     *
     * @access private
     */
    function install($data='') {
        parent::install();
    }

    /**
     * Uninstall
     *
     * Module uninstall routine
     *
     * @access public
     */
    function uninstall() {
        SQLExec('DROP TABLE IF EXISTS hikvision');
        parent::uninstall();
    }

    /**
     * dbInstall
     *
     * Database installation routine
     *
     * @access private
     */
    function dbInstall($data) {
        $data = <<<EOD
   hikvision: ID int NOT NULL PRIMARY KEY AUTO_INCREMENT
   hikvision: MODEL varchar(1000) NOT NULL
   hikvision: ADDRESS varchar(1000) NOT NULL
   hikvision: USERNAME varchar(1000) NOT NULL
   hikvision: PASSWORD varchar(1000) NOT NULL
   hikvision: STATUS varchar(45) NULL
   hikvision: POLL_RATE int NOT NULL DEFAULT '2'
   hikvision: LINKED_OBJECT varchar(1000) NULL
   hikvision: LINKED_PROPERTY varchar(1000) NULL
   hikvision: LINKED_METHOD varchar(1000) NULL
   hikvision: UPDATED_ON datetime NULL
EOD;
        parent::dbInstall($data);
    }
// --------------------------------------------------------------------
}
