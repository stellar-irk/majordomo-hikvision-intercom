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

        if ($this->ajax == 1) {

        }

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
                $address = htmlspecialchars($_POST['address']);
                $username = htmlspecialchars($_POST['user_name']);
                $password = $_POST['user_pass'];

                $intercom = $this->getDataFromIntercom('http://'.$address.'/ISAPI/System/deviceInfo', $username, $password);
                if (!isset($intercom->error)) {
                    $dbrecord = SQLSelectOne("select `ID` from `hikvision` where `ADDRESS`='".$address."'");
                    if (isset($dbrecord['ID'])) {
                        $intercom->error = LANG_HIKVISION_INTERCOM_EXISTS;
                    } else {
                        $dbrecord['MODEL'] = $intercom->model;
                        $dbrecord['ADDRESS'] = $address;
                        $dbrecord['USERNAME'] = $username;
                        $dbrecord['PASSWORD'] = $password;

                        SQLInsert('hikvision', $dbrecord);
                    }
                }
                echo json_encode($intercom);
                exit;
            }
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
                    default: $r[$i]['MODEL_IMG'] = '/templates/hikvision/model_img/DEFAULT.png';
                }
            }
            $out['intercoms'] = $r;
        }

        if ($this->view_mode == 'intercom_edit') {
            $r = SQLSelectOne("select * from `hikvision` where ID='".$this->id."'");
            switch ($r['MODEL']) {
                case 'DS-KV6103-PE1(C)': $r['MODEL_IMG'] = '/templates/hikvision/model_img/DS-KV6103-PE1C.png';
                    break;
                default: $r['MODEL_IMG'] = '/templates/hikvision/model_img/DEFAULT.png';
            }
            $out['address'] = $r['ADDRESS'];
            $out['username'] = $r['USERNAME'];
            $out['password'] = $r['PASSWORD'];
            $out['model'] = $r['MODEL'];
            $out['model_img'] = $r['MODEL_IMG'];
        }
    }



    /**
     * getDataFromIntercom
     *
     * Get Data from url with Digest Auth
     *
     * @access public
     */
    function getDataFromIntercom($url, $username, $password, $method = 'GET'): StdClass {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects if any
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password); // Set username and password
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST); // Specify Digest authentication
        switch ($method) {
            case 'POST': curl_setopt($ch, CURLOPT_POST, true);
                        break;
            case 'PUT': curl_setopt($ch, CURLOPT_PUT, true);
                break;
        }
        $response = curl_exec($ch);
        $result = new StdClass();
        if (curl_errno($ch)) {
            $result->error = curl_error($ch);
            return $result;
        } else {
            // Check HTTP status code (e.g., 200 OK, 401 Unauthorized)
            $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($status_code != 200) {
                $result->error = "Response with Status Code [" . $status_code . "].";
                return $result;
            } else {
                $xml = simplexml_load_string($response);
                return json_decode(json_encode($xml));
            }
        }
        curl_close($ch);
    }

    /**
     * editIntercom
     *
     * Edit Intercom View
     *
     * @access public
     */
    function editIntercom(&$out, $id) {

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
        $data = '';
        parent::dbInstall($data);
    }
// --------------------------------------------------------------------
}
