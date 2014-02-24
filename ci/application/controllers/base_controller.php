<?php

/**
 * 基底コントローラ
 *
 * @author shibuya
 */
class Base_controller extends CI_Controller
{

    /**
     * @var コントローラー名
     */
    var $name = "Base_controller";

   /**
     * @var viewに設定するための配列
     */
    protected $view = array();

    /**
     * @var エラーメッセージを格納するキャッシュキー
     */
    const CACHE_KEY_ERROR = "error_message_";

   /**
     * @var リクエストコントローラ名
     */
    var $controller_name = "";

    /**
     * @var リクエストアクション名
     */
    var $action_name = "";

    /**
     * コンストラクタ
     */
    function __construct()
    {

        parent::__construct();

        //メソッド名
        $RTR =& load_class('Router', 'core');
        $controller = $RTR->fetch_class();
        $action = $RTR->fetch_method();
        $this->controller_name = $controller;
        $this->action_name     = $action;

        $this->load->driver("cache", array("adapter" => "file"));
        $this->load->library('layout');
        $this->load->library("profiler");
        $this->load->database(DB_CONNECTION_WRITE);
        //-----------------------------------------
        // データを設定する
        //-----------------------------------------
        $this->view["controller_name"] = $this->name;

    }

    /**
     * デストラクタ
     *
     * parent::__destruct()は存在しないので、とりあえず呼ばない
     */
    function __destruct()
    {
        $this->db->close();
    }

    /**
     * リダイレクトを行う
     *
     * @param $path
     * @return void
     */
    protected function _redirect($path = "")
    {
        header("Location: ".SITE_URL.$path);
        exit;
    }

    /**
     * 不正エラー画面へリダイレクトする
     */
    protected function _redirect_fatal_error()
    {
        $this->_redirect("/err/fatal/");
    }
}
