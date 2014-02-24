<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Input Class
 *
 * Pre-processes global input data for security
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Input
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/input.html
 */
class my_Input extends CI_Input {

    function __construct()
    {
        parent::__construct();
    }

    // --------------------------------------------------------------------

    /**
    * Clean Keys
    *
    * This is a helper function. To prevent malicious users
    * from trying to exploit keys we make sure that keys are
    * only named with alpha-numeric text and a few other items.
    *
    * @access	private
    * @param	string
    * @return	string
    */
    function _clean_input_keys($str)
    {
        if ( ! preg_match("/^[a-z0-9:_\/-]+$/i", $str))
        {
            exit('Disallowed Key Characters.');
        }

        // 勝手にUTF-8にされないようにする
//        // Clean UTF-8 if supported
//        if (UTF8_ENABLED === TRUE)
//        {
//            $str = $this->uni->clean_string($str);
//        }

        return $str;
    }

    // --------------------------------------------------------------------

    /**
    * Clean Input Data
    *
    * This is a helper function. It escapes data and
    * standardizes newline characters to \n
    *
    * @access	private
    * @param	string
    * @return	string
    */
    function _clean_input_data($str)
    {
        if (is_array($str))
        {
            $new_array = array();
            foreach ($str as $key => $val)
            {
                $new_array[$this->_clean_input_keys($key)] = $this->_clean_input_data($val);
            }
            return $new_array;
        }

        // We strip slashes if magic quotes is on to keep things consistent
        if (function_exists('get_magic_quotes_gpc') AND get_magic_quotes_gpc())
        {
            $str = stripslashes($str);
        }

        // 勝手にUTF-8にされないようにする
//        // Clean UTF-8 if supported
//        if (UTF8_ENABLED === TRUE)
//        {
//            $str = $this->uni->clean_string($str);
//        }

        // Remove control characters
        $str = remove_invisible_characters($str);

        // Should we filter the input data?
        if ($this->_enable_xss === TRUE)
        {
            $str = $this->security->xss_clean($str);
        }

        // Standardize newlines if needed
        if ($this->_standardize_newlines == TRUE)
        {
            if (strpos($str, "\r") !== FALSE)
            {
                $str = str_replace(array("\r\n", "\r", "\r\n\n"), PHP_EOL, $str);
            }
        }

        return $str;
    }

}