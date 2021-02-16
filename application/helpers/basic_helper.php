<?php

/**
 * ********************IP�뿪�� ����ó��**************************
 */
$arr_inner_ip = array(
	'172\.20\.',
	'172\.16\.1\.',
	'10\.10\.',
	'10\.12\.',
	'10\.0\.2\.',      // VM NAT ip
	'192\.168\.56\.',  // VM host adapter ip
	'172\.30\.0\.',
	'172\.30\.1\.',

	'121\.133\.111\.125',   // Wi-Fi 9
	'112\.169\.208\.235',   // Wi-Fi 10

	'172\.30\.2\.',
	'172\.30\.3\.',
	'192\.168\.100\.',
	'172\.16\.0\.',
	'10\.11\.',
	'10\.10\.100\.',    // �̷���CS ����ó��
	'10\.10\.101\.',    // �������
	'172\.16\.0\.', // ���� ���и�
	'10\.11\.',    // ���� ���и�
	'14\.49\.30\.'    // ���� ���и�
);
$REMOTE_ADDR = $_SERVER['XFFCLIENTIP'] ? $_SERVER['XFFCLIENTIP'] : $_SERVER['REMOTE_ADDR'];
//$arr_inner_ip_list_result = preg_match('/' . implode('|', $arr_inner_ip) . '/', $REMOTE_ADDR);
//if(date('Y-m-d H:i:s') >= '2019-12-10 02:00:00'){
//	$shutdown_flag = true; // ����
//}
//  12�� 10�� 06�ú��� ���θ��� ���Ͽ� ���� ���
//if(date('Y-m-d H:i:s') >= '2019-12-10 05:00:00' && $arr_inner_ip_list_result) {
//	$shutdown_flag = false; // ����
//}
//true�� ��� ���� �������� �����̷�Ʈ
//if($shutdown_flag){
//	header("Location:https://member.hackers.com/maintenance");
//}

/* ********************IP�뿪�� ����ó�� END**************************
*/


if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if( !empty($_SERVER['XFFCLIENTIP']) ) {
	$_SERVER['REMOTE_ADDR'] = $_SERVER['XFFCLIENTIP'];
} else {
	$_SERVER['XFFCLIENTIP'] = $_SERVER['REMOTE_ADDR'];
}

// ���޼����� ���â����
function alert($msg='', $url='') {
    $CI =& get_instance();

    if (!$msg) $msg = '�ùٸ� ������� �̿��ϼ���.';

    echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=".$CI->config->item('charset')."\">";
    echo "<script type='text/javascript'>alert('".$msg."');";

    if ($url == '-1') echo "history.go(-1);";
    if (!$url) echo "parent.location.reload();";

    echo "</script>";

    if ($url) goto_url($url);
    exit;
}

function mypage_alert($msg='', $meta){

    echo "<meta property='og:title' content='6���� \'�״�� ����\' ��Ŀ�������'>
    	<meta property='og:type' content='https://{$_SERVER['HTTP_HOST']}/'>
    	<meta property='og:image' content='https://hackersg.hackers.com/hackersjob/bbs/data/2019/08/29/fddb4fd9f06fb87f29d07d78516aec97162552.jpg'>
    	<meta property='og:url' content='https://{$_SERVER['HTTP_HOST']}/'>
    	<meta property='og:description' content='����� ��� ���� ��Ŀ�������,NCS�հ�,����ä��,���������,�ڷ���,�ѱ����°���,���ﱳ�����,�������,����� ��� ��Ŀ�������'>
        ";

    echo "<script type='text/javascript'>";
    echo "alert('".$msg."');";
    echo "location.href = '".LOGIN_PAGE."&return_url='+encodeURIComponent(document.location.href); ";
    echo "</script>";
    exit;

}

function goLogin() {
    echo "<script type='text/javascript'>location.href = '".LOGIN_PAGE."&return_url='+encodeURIComponent(document.location.href);</script>";
    exit;
}

// ������ ����� ����
function dd($array)
{
    echo "<pre>";
    print_r($array);
    exit;
}

// �������
function date_of_week($date)
{
	$week = array("(��)", "(��)", "(ȭ)", "(��)", "(��)", "(��)", "(��)");
	$date_week = $week[date('w', strtotime($date))];
	
	return $date_week;
}


// ���޼��� ����� â�� ����
// �߰� : url ���� �� �θ�â �ش� url�� �̵�
function alert_close($msg, $url='') {
    $CI =& get_instance();

    echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=".$CI->config->item('charset')."\">";
    echo "<script type='text/javascript'> alert('".$msg."'); ";

    if ($url != '') echo "opener.location.href ='".$url."';";

    echo "window.close(); </script>";
    echo "</script>";
    exit;
}


// ���޼��� ����� �θ�â ���ΰ�ħ �� â�� ����
function alert_opener($msg) {
    $CI =& get_instance();

    echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=".$CI->config->item('charset')."\">";
    echo "<script type='text/javascript'> alert('".$msg."'); opener.document.location.reload(); self.close();</script>";
    exit;
}

//confirm â ��� �� Ȯ�� ���� �� url �� �̵�, ���� �� no action (�θ�â �̵�)
function confirm($msg, $url) {
    $CI =& get_instance();

    echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=".$CI->config->item('charset')."\">";
    echo "<script type='text/javascript'> if(confirm('".$msg."')){ ";

    if ($url) echo "parent.location.replace('".$url."');";

    echo "}</script>  ";
    exit;
}

// �ش� url�� �̵�
function goto_url($url) {
    $CI =& get_instance();
    $CI->load->helper('url');

    $temp = parse_url($url);
    if (empty($temp['host'])) {
        $CI =& get_instance();
        $url = ($temp['path'] != '/') ? SITE_DOMAIN.'/'.$url : SITE_DOMAIN;
        //$url = $CI->config->item('base_url').$url;
    }
    
    echo "<script type='text/javascript'> location.replace('".$url."'); </script>";
    exit;
}


//�θ�â  �ش� url�� �̵�
function goto_url_parent($url) {
    $CI =& get_instance();
    $CI->load->helper('url');

    $temp = parse_url($url);
    if (empty($temp['host'])) {
        $CI =& get_instance();
        $url = ($temp['path'] != '/') ? SITE_DOMAIN.'/'.$url : SITE_DOMAIN;
        $url = $CI->config->item('base_url').$url;
    }
    echo "<script type='text/javascript'> parent.location.replace('".$url."'); </script>";
    exit;
}


// �ش� url�� �̵�
function goto_url_blank($url) {
    $CI =& get_instance();
    $CI->load->helper('url');

    $temp = parse_url($url);
    if (empty($temp['host'])) {
        $CI =& get_instance();
        $url = ($temp['path'] != '/') ? SITE_DOMAIN.'/'.$url : SITE_DOMAIN;
        $url = $CI->config->item('base_url').$url;
    }
    echo "<script type='text/javascript'> window.open('".$url."'); </script>";
    exit;
}

function check_browser(){
    if(preg_match('/(iPhone|iPod|iPad)/', $_SERVER['HTTP_USER_AGENT'])){
        $os = "ios";
    }
    elseif(preg_match('/(Android)/', $_SERVER['HTTP_USER_AGENT'])){
        $os = "android";
    }
    else {
        $os = "etc";
    }
    return $os;
}

// �α��� ����üũ
function check_login(){
    $user_id = $_SESSION['user_id'];
    return $user_id;
}

// ��¥ ���� ���
function get_remain_date($__edate=NULL,$__sdate=NULL){

    $__edate = $__edate?strtotime($__edate):strtotime(date('Y-m-d'));
    $__sdate = $__sdate?strtotime($__sdate):strtotime(date('Y-m-d'));

    $remain_date = (($__edate-$__sdate)/86400);

    return $remain_date;
}


if(!function_exists('convertText')){
    function convertText($before,$after,&$arr){
        foreach($arr as $idx=>$val){
            if(gettype($arr[$idx])=='array'){
                convertText($before,$after,$arr[$idx]);
            }else{
                if(gettype($arr[$idx])=='string' && trim($arr[$idx])){
                    $arr[$idx]=iconv($before,$after,trim($arr[$idx]));
                }
            }
        }
    }
}

function LIB_getPageLink($block_num,$p,$total_page)
{
    if (strpos($_SERVER['REQUEST_URI'],'?')) {
        $_URL = $_SERVER['REQUEST_URI'].'&';
    } else {
        $_URL = $_SERVER['REQUEST_URI'].'?';
    }
    $return  = '';
    if ($p < $block_num + 1) {
        $return .= '<span class="borad_prev"><a href="#;">����</a></span><ul>';
    } else {
        $pp = (int)(($p-1) / $block_num) * $block_num;
        $return .= '<span class="borad_prev"><a href="'.$_URL.'p='.$pp.'">����</a></span><ul>';
    }

    $st1 = (int)(($p-1) / $block_num) * $block_num + 1;
    $st2 = $st1 + $block_num;
    for ($jn = $st1; $jn < $st2; $jn++) {
        if ($jn <= $total_page) {
            if ($jn == $p) {
                $return .= '<li class="active"><a href="#;">'.$jn.'</a></li>';
            } else {
                $return .= '<li><a href="'.$_URL.'p='.$jn.'">'.$jn.'</a></li>';
            }
        }
    }
    if ($total_page < $block_num || $total_page < $jn) {
        $return .= '</ul><span class="borad_next"><a href="#;">����</a></span>';
    } else {
        $np = $jn;
        $return .= '</ul><span class="borad_next"><a href="'.$_URL.'p='.$np.'">����</a></span>';
    }

    return $return;
}
function Paging_list($values) {

	GLOBAL $Bpage,$prev;
	GLOBAL $pagecount;
	$evtCode = $_GET['k'];

	if($pagecount >= 1) {

		if($values) {

			$values_array = split("/",$values);
			for($m=0;$m<sizeof($values_array);$m++) {
				$link_list.= "&".$values_array[$m];
			}
		}

		echo"<ul class='num_comment'>";

		if($prev > 5){
			$prevs = $prev - 5;
			$pages = $prevs + 4;
			$prevs_p = $Bpage - 1;
			echo"<li><a href='?k=$evtCode&Bpage=$prevs&prev=$prevs$link_list'>&lt;</a></li>";
		}

		$imax = $prev + 4;

		if($imax > $pagecount) $imax = $pagecount;

		for($i=$prev; $i <= $imax;$i++)
		{
			$class = ($Bpage == $i) ?  "class='on'" : "" ;
			echo"<li $class><a href='?k=$evtCode&Bpage=$i&prev=$prev$link_list'>$i</a></li>";
		}

		$next = $prev + 5;

		if($next <= $pagecount) echo"<li><a href='?k=$evtCode&Bpage=$next&prev=$next$link_list'>&gt;</a></li>";

		echo"</div>";
	}
}
function comment_getPageLink($block_num,$p,$total_page)
{
    $_URL = $_SERVER['SCRIPT_URI'].'?'.$_SERVER['QUERY_STRING'].'&';

    $return  = '';
    if ($p < $block_num + 1) {
        $return .= '<span class="borad_prev"><a href="#;">����</a></span><ul>';
    } else {
        $pp = (int)(($p-1) / $block_num) * $block_num;
        $return .= '<span class="borad_prev"><a href="'.$_URL.'p='.$pp.'">����</a></span><ul>';
    }

    $st1 = (int)(($p-1) / $block_num) * $block_num + 1;
    $st2 = $st1 + $block_num;
    for ($jn = $st1; $jn < $st2; $jn++) {
        if ($jn <= $total_page) {
            if ($jn == $p) {
                $return .= '<li class="active"><a href="#;">'.$jn.'</a></li>';
            } else {
                $return .= '<li><a href="'.$_URL.'p='.$jn.'">'.$jn.'</a></li>';
            }
        }
    }
    if ($total_page < $block_num || $total_page < $jn) {
        $return .= '</ul><span class="borad_next"><a href="#;">����</a></span>';
    } else {
        $np = $jn;
        $return .= '</ul><span class="borad_next"><a href="'.$_URL.'p='.$np.'">����</a></span>';
    }

    return $return;
}

function oldPassword($password) {
    $nr=0x50305735;
    $nr2=0x12345671;
    $add=7;
    $charArr = preg_split("//", $password);
    foreach ($charArr as $char) {
        if (($char == '') || ($char == ' ') || ($char == '\t')) continue;
        $charVal = ord($char);
        $nr ^= ((($nr & 63) + $add) * $charVal) + ($nr << 8);
        $nr2 += ($nr2 << 8) ^ $nr;
        $add += $charVal;
    }
    return sprintf("%08x%08x", ($nr & 0x7fffffff), ($nr2 & 0x7fffffff));
}

function stripslashes_custom($value){
    if(is_array($value)){
        return array_map('stripslashes_custom', $value);
    }else{
        return stripslashes($value);
    }
}

function PageView($values,$Bpage,$prev,$pagecount){

    if($pagecount >= 1) {

        if($values) {

            $values_array = split("/",$values);
            for($m=0;$m<sizeof($values_array);$m++) {
                $link_list.= "&".$values_array[$m];
            }
        }
        $str = '';
        $str .= "<div class='live_number'>";

        if($prev > 5){
            $prevs = $prev - 5;
            $pages = $prevs + 4;
            $prevs_p = $Bpage - 1;
            $str .= "<a href='?Bpage=$prevs&prev=$prevs$link_list' class='num_prev'><span class='hide-text'>����</span></a>";
        }

        $imax = $prev + 4;

        if($imax > $pagecount) $imax = $pagecount;

        for($i=$prev; $i <= $imax;$i++)
        {
            if ($Bpage == $i) $str .= "<a href='javascript:void(0);' class='on'>$i</a>";
            else $str .= "<a href='?Bpage=$i&prev=$prev$link_list' >$i</a>";
        }

        $next = $prev + 5;

        if($next <= $pagecount) $str .= "<a class='num_next' href='?Bpage=$next&prev=$next$link_list'><span class='hide-text'>����</span></a>";

        $str .= "</div>";

        return $str;
    }
}

function LIB_getContents($str,$html)
{
    if ($html == 'HTML')
    {
        $str = str_replace('<A href=','<a target="_blank" rel="noreferrer" href=',$str);
        $str = str_replace('<a href=','<a target="_blank" rel="noreferrer" href=',$str);
        $str = str_replace('<a target="_blank" rel="noreferrer" href="#','<a href="#',$str);
        $str = str_replace(' target="_blank" rel="noreferrer">','>',$str);
        $str = str_replace('< param','<param',$str);
        $str = str_replace("\t",'&nbsp;&nbsp;&nbsp;&nbsp;',$str);

        $str = str_replace('@IFRAME@','iframe',$str);

        $onAttributes = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavaible', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragdrop', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterupdate', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmoveout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
        $str = str_replace('imgOrignWin(this.src)=""','onclick="imgOrignWin(this.src);"',$str);
        $str = str_replace('imgorignwin(this.src)=""','onclick="imgOrignWin(this.src);"',$str);

        $_atkParam = array(';a=','&a=','?a=');
        foreach($_atkParam as $_prm)
        {
            $str = str_replace($_prm,'',$str);
        }
    }
    else {
        $str = str_replace('<','&lt;',$str);
        $str = str_replace('>','&gt;',$str);
        $str = str_replace('&nbsp;','&amp;nbsp;',$str);
        $str = str_replace("\t",'&nbsp;&nbsp;&nbsp;&nbsp;',$str);
        $str = nl2br($str);
    }
    return $str;
}

#----------------------------------------------------------------------
# �ַ�� �⺻ Ŭ���� ���ϵ��� ó���ϴ� ������
# GET, POST, COOKIE �� stripslashes ó��
#----------------------------------------------------------------------

if((function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc()) || (ini_get('magic_quotes_sybase') && (strtolower(ini_get('magic_quotes_sybase'))!="off"))) {
	function stripslashes_deep(&$value) {
		$value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
		return $value;
	}
	stripslashes_deep($_GET);
	stripslashes_deep($_POST);
	stripslashes_deep($_COOKIE);
}

#----------------------------------------------------------------------
# SQL Injection ���� ���� ó��(GET ���) - [ ', ", #, -, |, & ] %
#----------------------------------------------------------------------

$add_string = (preg_match("/free_lecture/", $_SERVER['REQUEST_URI']) || preg_match("/bbs/", $_SERVER['REQUEST_URI']) || preg_match("/tohackers/", $_SERVER['REQUEST_URI']) ) ? "" : "\:\;";

foreach ($_GET as $key => $val)
{
    // �Ķ���͸� üũ
    if (preg_match("/[\'\"\#\%\|\;*]+/", $key)) {
        header("HTTP/1.0 404 Not Found");
        exit;
    }

    // �Ķ���Ͱ� üũ
    if (is_array($_GET[$key]))
    {
        foreach ($_GET[$key] as $key2 => $val2) {
            if (preg_match("/[\'\"\#\%\<\>\$".$add_string."*]+/", $val2)) {
                header("HTTP/1.0 404 Not Found");
                exit;
            }
        }
    }
    else {
        if(preg_match("/[\'\"\#\%\<\>\$".$add_string."*]+/", $val)) {
            header("HTTP/1.0 404 Not Found");
            exit;
        }
    }
}

function xss_clean($data)
{
	// If its empty there is no point cleaning it :\
	if(empty($data))
		return $data;

		// Recursive loop for arrays
		if(is_array($data))
		{
			foreach($data as $key => $value)
			{
				$data[$key] = xss_clean($value);
			}

			return $data;
		}

		// Fix &entity\n;
		$data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
		$data = preg_replace('/(&#*\w+)[\x00-\x20]+;/', '$1;', $data);
		$data = preg_replace('/(&#x*[0-9A-F]+);*/i', '$1;', $data);

		if (function_exists("html_entity_decode"))
		{
			$data = html_entity_decode($data);
		}
		else
		{
			$trans_tbl = get_html_translation_table(HTML_ENTITIES);
			$trans_tbl = array_flip($trans_tbl);
			$data = strtr($data, $trans_tbl);
		}

		// Remove any attribute starting with "on" or xmlns
		$data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#i', '$1>', $data);

		// Remove javascript: and vbscript: protocols
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#i', '$1=$2nojavascript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#i', '$1=$2novbscript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#', '$1=$2nomozbinding...', $data);

		// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#i', '$1>', $data);

		// Remove namespaced elements (we do not need them)
		$data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);
		//Special word remove
		$data = str_replace(array("'","--",'"'), "", $data);
		//select, create, delete, update, char, cast, create, alter, update, delete, exec, union remove

		do
		{
			// Remove really unwanted tags
			$old_data = $data;
			$data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
		}
		while ($old_data !== $data);

		return $data;

}

// COOKIE ����
function setCookieWithHeader($name, $value, $expire, $path = '/', $domain = '')
{
    if (empty($domain)) {
        $domain = str_replace("www.", "", $_SERVER['SERVER_NAME']);
    }
    if (headers_sent()) {
        $cookie = $name . '=' . urlencode($value) . ';';
        if ($expire) $cookie .= ' path=' . $path . '; domain=' . $domain . '; expires=' . gmdate('D, d M Y H:i:s', $expire) . ' GMT';
        echo '<script language="javascript">document.cookie="' . $cookie . '";</script>';
    } else {
        setcookie($name, $value, $expire, $path, $domain);
    }
}

$isLive		= (preg_match('/^(mr|m)\.haksa2080\.com/', $_SERVER['HTTP_HOST'])) ? true : false;
$isQa		= (preg_match('/^(mq)\.haksa2080\.com/', $_SERVER['HTTP_HOST'])) ? true : false;
$isDev		= (preg_match('/^(mt)\.haksa2080\.com/', $_SERVER['HTTP_HOST'])) ? true : false;
$isLocal	= (preg_match('/^(ml)\.haksa2080\.com/', $_SERVER['HTTP_HOST'])) ? true : false;

if($isLive) define('APP_ENV_CHAR', ''); // �Ǽ���
if($isQa) define('APP_ENV_CHAR', 'q'); // Q����
if($isDev) define('APP_ENV_CHAR', 't'); // T����
if($isLocal) define('APP_ENV_CHAR', 'l'); // ���ü���


$host_value = ".haksa2080.com";

@ini_set('session.cookie_domain', $host_value); // ������Ű�� ����������
@session_set_cookie_params(0,"/", $host_value);
@session_cache_limiter('nocache, must_revalidate');
@ini_set('session.gc_maxlifetime','10800'); // ���� ����ð��� �ѽð����� ����
@session_start();

if(!empty($_COOKIE['hackersID']) && empty($_SESSION['user_id'])) {
    //$return_url = '//'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

    //header('Location: //'.ENV_DOMAIN.'public.hackers.com/rankup_module/rankup_member/integration_member.php?mode=login&return_url=' . urlencode($return_url));

    require_once $_SERVER['DOCUMENT_ROOT'] . '/application/libraries/call_integration.php';

    $call_api = new CallApi();
    $res = $call_api->exec('POST', 'decryptID', $_COOKIE['hackersID']);
    if(!$res) {
        setCookieWithHeader("hackersID", "", 0, "/", ".hackers.com");
        setCookieWithHeader("hackersCCD", "", 0, "/", ".hackers.com");
        setCookieWithHeader("hackersFLAG", "", 0, "/", ".hackers.com");

        header("Refresh:0");
        exit;
    }

    if($res['curl_errno']) {
        echo "integration error " . $res['curl_errno'];
        exit;
    }

    $http_code = $res['curl_http_code'];
    $arr = json_decode($res['json'], true);
    switch ($http_code) {
        case 200:
            $MemberIdx = $arr['data']['idx'];
            break;
        // ���� ����
        case 401:
            $MemberIdx = "integration error 401:[" . $arr['code'] . "]" . $arr['message'];
            break;
        default:
            $MemberIdx = 'Unexpected HTTP code: ' . $http_code . "\n";
    }
    unset($res);
    
    // �ڵ��α��� ó��.
    if($MemberIdx == 'keepLogin') {
        $integration_return_url = urlencode('//'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
        header('Location: //'.APP_ENV_CHAR.'member.hackers.com/login/keep?service_id=3080&return_url='.$integration_return_url);
        exit;
    }
    
    if(is_numeric($MemberIdx)) {
        $CI =& get_instance();
        $this->champ = $CI->load->database("hackersjob_champ", TRUE);
        $member = $this->champ->query("SELECT * FROM `zetyx_member_table` WHERE MemberIdx = '$MemberIdx'")->first_row('array');
        if(!empty($member)) {
            $_SESSION['niceId'] = $member['user_id'];
            $_SESSION['user_id'] = $member['LoginID'];
            $_SESSION['user_no'] = $member['no'];
            $_SESSION['niceVal'] = $member['kind'];
            $_SESSION['niceName'] = $member['name'];
            $_SESSION['user_name'] = $member['name'];
            $_SESSION['integration_time'] = $member['IntegrateMigrateDatetime'];
        } else {
            $arr_hackersFLAG = explode(',', $_COOKIE['hackersFLAG']);
            if(in_array('400', $arr_hackersFLAG)) {
                $ch = curl_init();
                $url = "http://".APP_ENV_CHAR."api.hackers.com/v1/member/insert/{$MemberIdx}";

                $crypt = new Crypt();

                $KEY = 'Api@Crypt.hackers';
                $token = 'Api@Token.Hackers';
                $apiKey = 'Api@Key.Hackers';
                $serviceID = 3080;
                $hdFlag = time() . rand();

                $token_s = $crypt->encrypt("{$token}|{$hdFlag}", $KEY);
                $apiKey_s = $crypt->encrypt("{$apiKey}|{$hdFlag}", $KEY);
                $serviceID_s = $crypt->encrypt("{$serviceID}|{$hdFlag}", $KEY);
                $flag_key = 'Api@Flag.Hackers';

                $applicationCode = '008005';

                $tempTime = time();

                $flag = md5("{$serviceID}|{$tempTime}|{$flag_key}");

                $curl_post_data = array(
                    'service_id' => $serviceID,
                    'tempTime' => $tempTime,
                    'flag' => $flag
                );


                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    "token: {$token_s}",
                    "apiKey: {$apiKey_s}",
                    "serviceId: {$serviceID_s}",
                    "hdFlag: {$hdFlag}"
                ));
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_post_data);
                $json = curl_exec($ch);
                $result = json_decode($json, true);

                header("Refresh:0");
                exit;
            }
        }
    }
}
$arr_hackersFLAG = explode(',', $_COOKIE['hackersFLAG']);
if((empty($_COOKIE['hackersID']) && !empty($_SESSION['user_id'])) || (!in_array('400', $arr_hackersFLAG) && !empty($_COOKIE['hackersFLAG']))) {
    setCookieWithHeader("hackersID", "", 0, "/", ".haksa2080.com");
    setCookieWithHeader("hackersCCD", "", 0, "/", ".haksa2080.com");
    setCookieWithHeader("hackersFLAG", "", 0, "/", ".haksa2080.com");

    session_destroy();
    header("Refresh:0");
    //header('Location: //'.$_SERVER['HTTP_HOST'].'/');
}

$_GET = xss_clean($_GET);
$_POST = xss_clean($_POST);

?>