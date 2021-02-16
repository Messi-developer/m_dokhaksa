<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login {
    function index()
    {
            $CI = &get_instance();
            $CI->load->model("login/m_member");
            // $CI->m_member->auto_login();
    }
}
?>