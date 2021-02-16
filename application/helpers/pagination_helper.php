<?php if ( ! defined( 'BASEPATH')) exit( 'No direct script access allowed');
function get_basic_view_config(){
    $config = array();
    /*
    <div class="paging">
         <span class="first dimmed">처음</span>
         <span class="prev dimmed">이전</span>
         <span>1</span>
         <a href="">1</a>
         <a href="">3</a>
         <a href="">4</a>
         <a href="">5</a>
         <a href="">6</a>
         <span class="next"><a href="">다음</a></span>
         <span class="last"><a href="">마지막</a></span>
    </div>
     */
    $config['use_page_numbers'] = TRUE; //offset 대신 page number 로 넘겨줌
    $config['full_tag_open'] = '<div class="paging">';
    $config['full_tag_close'] = '</div>';

    $config['first_link'] = '처음';
    $config['first_link_dimmed'] = '';
    $config['first_tag_open'] = '<span>';
    $config['first_tag_close'] = '';

    $config['prev_link'] = '<';
    // $config['prev_link_dimmed'] = '<i><</i>';
    //$config['prev_tag_open'] = '<span class="prev">';
    //$config['prev_tag_close'] = '</span>';

    $config['next_link'] = '>';
    //$config['next_link_dimmed'] = '<i>></i>';
    //$config['next_tag_open'] = '<span class="next">';
    //$config['next_tag_close'] = '</span>';

    $config['last_link'] = '마지막';
    $config['last_link_dimmed'] = '';
    $config['last_tag_open'] = '';
    $config['last_tag_close'] = '</span>';


    $config['cur_tag_open'] = '<a class="on">';
    $config['cur_tag_close'] = '</a>';

    $config['num_tag_open'] = '';
    $config['num_tag_close'] = '';
    return $config;
}

function get_small_view_config(){
    $config = array();

    $config['pagination_type'] = 'small';
    $config['display_pages'] = FALSE;
    $config['use_page_numbers'] = TRUE; //offset 대신 page number 로 넘겨줌
    $config['full_tag_open'] = '<div class="smallpage">';
    $config['full_tag_close'] = '</div>';

    $config['first_link'] = '';

    $config['prev_link'] = '<img src="'.IMG_DIR.'/mydangi/btn/btn_prev_small.gif" alt="이전" class="prev">';
    $config['prev_tag_open'] = '';
    $config['prev_tag_close'] = '';

    $config['next_link'] = '<img src="'.IMG_DIR.'/mydangi/btn/btn_next_small.gif" alt="다음" class="next">';
    $config['next_tag_open'] = '';
    $config['next_tag_close'] = '';

    $config['last_link'] = '';

    $config['cur_tag_open'] = '<span class="on">';
    $config['cur_tag_close'] = '</span>';

    $config['num_tag_open'] = '<span>';
    $config['num_tag_close'] = '</span>';
    return $config;
}


//페이지네이션 기본 style
function get_basic_pagination($base_url, $total_rows, $uri_segment=3, $per_page=15, $query_str = '' , $num_link=10) {
    $CI =& get_instance();
    $CI->load->library('pagination');

    $config['script_link'] = false;
    $config['display_prev_next'] = true;
    $config['base_page'] = 2;
    $config['suffix'] = $query_str;
    $config['base_url'] = $base_url;
    $config['total_rows'] = $total_rows;
    $config['uri_segment'] = $uri_segment;
    $config['per_page'] = $per_page;
    $config['num_links'] = $num_link;

    $config = array_merge($config, get_basic_view_config());

    $CI->pagination->initialize($config);
    return $CI-> pagination->create_links();
}

//페이지네이션 기본 style
function get_script_pagination($base_url, $total_rows, $uri_segment=3,  $per_page=15, $other_info='' , $num_link=10) {
    $CI =& get_instance();
    $CI->load->library('pagination');

    $config['script_link'] = true;
    $config['display_prev_next'] = true;
    $config['prefix'] = "('";
    $config['suffix'] = "', '".$other_info. "');"; // other_infoi로 페이징 타입 등을 설정
    $config['base_url'] = $base_url;
    $config['total_rows'] = $total_rows;
    $config['uri_segment'] = $uri_segment;
    $config['per_page'] = $per_page;
    $config['num_links'] = $num_link;

    $config = array_merge($config, get_basic_view_config());

    $CI->pagination->initialize($config);
    $paging = $CI-> pagination->create_links();
    //$paging = str_replace('go_page/', 'go_page', $paging);

    return $paging;
}

//페이지네이션 기본 style
function get_script_pagination_2($base_url, $total_rows, $uri_segment=3,  $per_page=15, $other_info='', $other_info2='', $other_info3='', $other_info4='', $other_info5='' , $num_link=10) {
    $CI =& get_instance();
    $CI->load->library('pagination');

    $config['script_link'] = true;
    $config['display_prev_next'] = true;
    $config['prefix'] = "('";
    $config['suffix'] = "', '".$other_info. "','".$other_info2. "','".$other_info3. "','".$other_info4. "','".$other_info5. "');"; // other_infoi로 페이징 타입 등을 설정
    $config['base_url'] = $base_url;
    $config['total_rows'] = $total_rows;
    $config['uri_segment'] = $uri_segment;
    $config['per_page'] = $per_page;
    $config['num_links'] = $num_link;

    $config = array_merge($config, get_basic_view_config());

    $CI->pagination->initialize($config);
    $paging = $CI-> pagination->create_links();

    return $paging;
}

//페이지네이션 기본 style
function get_link_pagination($base_url, $total_rows, $uri_segment=3, $per_page=15,  $num_link=10) {
    $CI =& get_instance();
    $CI->load->library('pagination');
    $config['script_link'] = false;
    $config['display_prev_next'] = true;
    $config['prefix'] = "/";
    $config['base_url'] = $base_url;
    $config['total_rows'] = $total_rows;
    $config['uri_segment'] = $uri_segment;
    $config['per_page'] = $per_page;
    $config['num_links'] = $num_link;
    $config = array_merge($config, get_basic_view_config());
    $CI->pagination->initialize($config);
    $paging = $CI-> pagination->create_links();
    return $paging;
}

//페이지네이션 small style
function get_small_pagination($base_url, $total_rows, $uri_segment=3, $per_page=15, $query_str = '' , $num_link=2) {
    $CI =& get_instance();
    $CI->load->library('pagination');

    $config['display_prev_next'] = false;
    $config['suffix'] = $query_str;
    $config['base_url'] = $base_url;
    $config['total_rows'] = $total_rows;
    $config['uri_segment'] = $uri_segment;
    $config['per_page'] = $per_page;
    $config['num_links'] = $num_link;

    $config = array_merge($config, get_small_view_config());

    $CI->pagination->initialize($config);
    return $CI-> pagination->create_links();
}

//페이지네이션 small style
function get_small_script_pagination($base_url, $total_rows, $uri_segment=3,  $per_page=15, $other_info='' , $num_link=2) {
    $CI =& get_instance();
    $CI->load->library('pagination');

    $config['script_link'] = true;
    $config['display_prev_next'] = false;
    $config['prefix'] = "('";
    $config['suffix'] = "', '".$other_info. "');"; // other_infoi로 페이징 타입 등을 설정
    $config['base_url'] = $base_url;
    $config['total_rows'] = $total_rows;
    $config['uri_segment'] = $uri_segment;
    $config['per_page'] = $per_page;
    $config['num_links'] = $num_link;

    $config = array_merge($config, get_small_view_config());

    $CI->pagination->initialize($config);
    $paging = $CI-> pagination->create_links();
    //$paging = str_replace('go_page/', 'go_page', $paging);

    return $paging;

}
?>