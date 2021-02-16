<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login_session_chk {
    function index()
    {
        $CI = &get_instance();
        
        if(!strpos($_SERVER['REQUEST_URI'],"/login/m_login/logout") && !strpos($_SERVER['REQUEST_URI'],"/login/m_login/login")){

            $hackers_data = "";
            $hackers_data_key = array('zb_logged_no','zb_logged_name','zb_logged_user_id','zb_logged_time');
            foreach ($_SESSION as $key => $rs) if(in_array($key, $hackers_data_key)) $hackers_data .= $key.$rs;
            if($CI->session->userdata("user_id")){
                if(($_SESSION['zb_logged_hackers'] != $CI->session->userdata("zb_logged_hackers"))||(md5($hackers_data) != $CI->session->userdata("zb_logged_hackers"))){
                    alert('로그인 정보가 수정되었습니다 다시 로그인 해주세요.','/login/m_login/logout');
                    exit;
                }
            }
        }

    }
}
?>