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
        if ($this->view_mode == '') {

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

        $out['debug'] = 'DEBUG';
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
