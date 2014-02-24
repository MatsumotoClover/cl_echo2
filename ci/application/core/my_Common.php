<?php
    require APPPATH . "/core/cake/file.php";

    /**
     * cakeログ
     * @param メッセージ $msg
     * @param ログタイプ $type
     */
    function cake_log($msg, $type = "debug") {
        if (!is_string($msg)) {
            $msg = print_r($msg, true);
        }

        $path = APPPATH . "tmp" . DS . "logs" . DS;;

        $debugTypes = array('notice', 'info', 'debug');

        if ($type == 'error' || $type == 'warning') {
            $filename = $path  . 'error.log';
        } elseif (in_array($type, $debugTypes)) {
            $filename = $path . 'debug.log';
        } else {
            $filename = $path . $type . '.log';
        }
        $output = date('Y-m-d H:i:s') . ' ' . ucfirst($type) . ': ' . $msg . "\n";

        $log = new File($filename, true);

        if ($log->writable()) {
            return $log->append($output);
        }
    }