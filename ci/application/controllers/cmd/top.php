<?php
require_once dirname(__FILE__) . "./../base_controller.php";

/**
 * TOPページコントローラ
 *
 * @author matsumoto
 */
class Top extends Base_controller {

    function index() {
        $a = 'test PullReq';

        $this->view['a'] = $a;
        $this->layout->view("top/index", $this->view);
    }
}
