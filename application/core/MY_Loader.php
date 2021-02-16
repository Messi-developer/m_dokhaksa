<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Loader  extends CI_Loader
{
    function __construct() {
        parent::__construct();
    }

    function view($view, $vars = array(), $return = FALSE) {
        return $this->_ci_load(
            array(
                '_ci_view' => $view.'.html',
                '_ci_vars' => $this->_ci_object_to_array($vars),
                '_ci_return' => $return)
        );
    }
}