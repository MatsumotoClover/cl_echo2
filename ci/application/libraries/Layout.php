<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Layout
{

    var $obj;
    var $layout;

    function Layout($layout = 'layout_main')
    {
        $this->obj =& get_instance();
        $this->layout = $layout;
    }

    function setLayout($layout)
    {
      $this->layout = $layout;
    }

    function view($view, $data = null, $return = FALSE)
    {
        $layout =  $this->layout;

        $loadedData = array();
        $loadedData['content_for_layout'] = $this->obj->load->view($view, $data, TRUE);

        if($return)
        {
            $output = $this->obj->load->view($layout, $loadedData, TRUE);
            return $output;
        }
        else
        {
            $this->obj->load->view($layout, $loadedData, FALSE);
        }

    }
}
