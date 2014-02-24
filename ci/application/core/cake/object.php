<?php
/**
 * Object class, allowing __construct and __destruct in PHP4.
 *
 * Also includes methods for logging and the special method RequestAction,
 * to call other Controllers' Actions from anywhere.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.libs
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Object class, allowing __construct and __destruct in PHP4.
 *
 * Also includes methods for logging and the special method RequestAction,
 * to call other Controllers' Actions from anywhere.
 *
 * @package cake
 * @subpackage cake.cake.libs
 */
class Object {

/**
 * A hack to support __construct() on PHP 4
 * Hint: descendant classes have no PHP4 class_name() constructors,
 * so this constructor gets called first and calls the top-layer __construct()
 * which (if present) should call parent::__construct()
 *
 * @return Object
 */
    function Object() {
        $args = func_get_args();
        if (method_exists($this, '__destruct')) {
            register_shutdown_function (array(&$this, '__destruct'));
        }
        call_user_func_array(array(&$this, '__construct'), $args);
    }

/**
 * Class constructor, overridden in descendant classes.
 */
    function __construct() {
    }

/**
 * Object-to-string conversion.
 * Each class can override this method as necessary.
 *
 * @return string The name of this class
 * @access public
 */
    function toString() {
        $class = get_class($this);
        return $class;
    }
/**
 * Stop execution of the current script.  Wraps exit() making
 * testing easier.
 *
 * @param $status see http://php.net/exit for values
 * @return void
 * @access public
 */
    function _stop($status = 0) {
        exit($status);
    }

/**
 * Allows setting of multiple properties of the object in a single line of code.  Will only set
 * properties that are part of a class declaration.
 *
 * @param array $properties An associative array containing properties and corresponding values.
 * @return void
 * @access protected
 */
    function _set($properties = array()) {
        if (is_array($properties) && !empty($properties)) {
            $vars = get_object_vars($this);
            foreach ($properties as $key => $val) {
                if (array_key_exists($key, $vars)) {
                    $this->{$key} = $val;
                }
            }
        }
    }
}
