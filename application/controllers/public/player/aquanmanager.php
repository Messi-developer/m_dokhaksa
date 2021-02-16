<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);
class Aquanmanager extends CI_Controller
{
	function __construct(){
		parent::__construct();
		$this->load->helper('url');
		$this->load->model('lecture/m_lecture');
		$this->load->model('player/m_player');
		$this->head = array(
			'title' => '독학사 플레이어',
			'css' => array(),
			'js' => array()
		);
		
		$this->user_id = $this->session->userdata('member_id');
		$this->user_name = $this->session->userdata('member_name');
		$this->user_no = $this->session->userdata('member_no');
		
		//if(!check_login()) goLogin();
	}
	
	public function player_view(){
		$lmno = $this->input->get("lmno");
		$lec_no = $this->input->get("lec_no");
		$seq = $this->input->get("leck_kang_name");
		$enable_id = $this->input->get("enable_id");
		$enable_pos = $this->input->get("enable_pos");
		
		$server_ip 	= $this->input->server('REMOTE_ADDR');
		
		$checkOS 	= check_browser();
		
		//$device_id = $this->m_player->mobile_device_chk($this->user_no);
		// LecAttendance에 로그 쌓기
		$__set_array = array(
			"uid"			=> $this->user_id,
			"lmno"			=> $lmno,
			"seq"			=> $seq,
			"lectype"		=> "M",
			"wdate"			=> date('Y-m-d H:i:s'),
			"ip"			=> $server_ip,
		);
		//$this->m_lecture->lecture_att_insert($__set_array);
		//$lecture_player_data = $this->m_lecture->lecture_player_data($lmno,$lec_no,$seq);
		$result_data = $this->player_make_param($lmno, $lec_no, $seq, $this->session->userdata('member_id'), $this->session->userdata('member_no'),0, $server_ip, $enable_id, $enable_pos);
		
		$head = $this->head;
		$data = array(
			"market_URL" 	=> $result_data['market_URL'],
			"fullurl" 		=> $result_data['fullurl'],
			"checkOS"		=> $checkOS,
		);
		
		/*echo "<pre>"; print_r($data); echo "</pre>";
		exit;*/
		
		$this->load->view('common/header',$head);
		$this->load->view('player/player',$data);
		$this->load->view('common/footer');
	}
	public function xmlload($target = '',$pos = 0){
		$lectureName = '';
		
		//북마크 사용여부 설정 옵션
		//0: 북마크 기능 사용안함
		//1: 북마크 기능 사용
		$bookmark_use = 0;
		
		//사용자가 지정한 북마크 정보 설정 옵션 (단위:ms)
		//북마크 정보가 다수일 경우 "|" 를 이용하여 구분
		$bookmarkpos = '';
		
		//영상시작위치
		$startpos = 0;
		
		//.smi 자막URL
		$subtitle_url = '';
		
		/*
		urlpath : VOD 서비스 URL
		urltitle : 화면상에 보여지는 이름
		default : 처음 재생 시 보여지는 contents (모두 false인경우 첫번째 영상부터 재생됨)
		*/
		
		$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
		$xml .= "<result>";
		$xml .= "	<contents>";
		$xml .= "		<start>0</start>";
		$xml .= "		<content>";
		$xml .= "			<cid>cid</cid>";
		$xml .= "			<index>0</index>";
		$xml .= "			<path><![CDATA[".$lectureName."]]></path>";
		$xml .= "			<urls>";
		$xml .= "				<url>";
		$xml .= "					<urlpath>http://mvod.aquan.hackers.co.kr/champstudymobile/wmv/withus/".$target."</urlpath>";
		$xml .= "					<urltitle>일반화질</urltitle>";
		$xml .= "					<default>true</default>";
		$xml .= "				</url>";
		$xml .= "				<url>";
		$xml .= "					<urlpath>http://mvod.aquan.hackers.co.kr/champstudymobile/wmv/withus/".$target."</urlpath>";
		$xml .= "					<urltitle>고화질</urltitle>";
		$xml .= "					<default>false</default>";
		$xml .= "				</url>";
		$xml .= "			</urls>";
		$xml .= "			<subtitle_url></subtitle_url>";
		$xml .= "			<startpos>".$pos."</startpos>";
		$xml .= "			<bookmarkpos>".$bookmarkpos."</bookmarkpos>";
		$xml .= "			<bookmark_use>".$bookmark_use."</bookmark_use>";
		$xml .= "		</content>";
		$xml .= "	</contents>";
		$xml .= "</result>";
		
		return $xml;
		
	}
	
	## 플레이어 강의정보
	public function player_make_param($__lmno, $__lec_no, $__seq, $__user_id, $__user_no, $__device_id="", $__server_ip, $enable_id, $enable_pos)
	{
		$checkOS = check_browser();
		
		$idx = $__lmno;
		$su = $__seq;
		$no = $__lec_no;
		$lec_type = "b";
		$ls_kind = "m";
		$num = '1';
		
		$defaultHost = 'https://mr.haksa2080.com';
		
		
		// 챔프 통합프리패스 관련 로직
//        $lecData = $this->m_lecture->getLecData($idx);
//        if ($lecData['discount_div'] == 'V') {
//            //$device_id = $this->callChampAPI($lecData);
//            $device_id = '';
//        }
		
		// 강의정보 조회
		$lecKangInfo = $this->m_lecture->lecture_player_data($__lmno, $__seq, $__lec_no);
		$lec_info = $this->m_lecture->lecture_data($lecKangInfo->lec_no);
		$view_tail = "";
		
		
		switch ($num) {
			case '1' :
				$target_url = $lecKangInfo->leck_url;
				break;
			case '2' :
				$target_url = $lecKangInfo->leck_url_p;
				break;
			case '3' :
				$target_url = $lecKangInfo->leck_url_p2;
				break;
			case '4' :
				$target_url = $lecKangInfo->leck_url_p3;
				break;
			default :
				$target_url = $lecKangInfo->leck_url;
				break;
		}
		
		$find_type = explode('.', $target_url);
		$find_lecType = $find_type[1];  //wmv or mp4 영상
		$target_url = str_replace(".wmv",".mp4", $target_url); // 영상 인코딩 변경
		
		$masterkey = "champ"; // champ -> Master
		$child_customer_id = 'chp_jr'; // chp_jr -> hks
		$userid = $__user_id;
		$serverip = getenv('REMOTE_ADDR'); // getenv('REMOTE_ADDR') -> 211.122.229.63
		$filename = $target_url;
		$webservertime = gmmktime();
		$aquaauth = "1";
		$timeout = "30";
		
		$wmtext = "ChampStudy";
		$wmcolor = "FFFFFF";
		$wmsize = "1";
		$wmshadecolor = "000000";
		$wmpos = "9";
		
		/* 구 플레이어 */
		if ($lec_path == "PC_path") { //확장자 mp4인경우
			$Url = "https://mvod.aquan.hackers.co.kr/champstudymobile/wmv/withus/". $filename;  //pc 저화질 경로
		} else { //확장자 wmv인 경우
			$Url = "https://mvod.aquan.hackers.co.kr/champstudymobile/wmv/withus/". $filename;  //모바일 저화질 경로
		}
		
		// 로딩시간 체크
		if ($enable_pos > 0) {
			//$param = $param. "&pos=".($enable_pos*1000);
			$enable_pos = $enable_pos * 1000;
		}
		
		/* 리스트 플레이어 */
		$url = 'https://'.$_SERVER["HTTP_HOST"].'/player/playlist/xmlload?lectureNo='. $no ."&target=". $filename ."&pos=". $enable_pos;
		//$url = $this->xmlload($filename,$enable_pos);
		
		//모델명 체크
		if ($checkOS == "ios") {
			$tmp_AGENT = explode("(",$_SERVER['HTTP_USER_AGENT']);
			$arr_AGENT = explode(";",$tmp_AGENT[1]);
			$d_model = $arr_AGENT[0];
		} else {
			$arr_AGENT = explode(" ",$_SERVER['HTTP_USER_AGENT']);
			$d_model = $arr_AGENT[6];
		}
		
		$ret_url = '';
		if ($_SESSION['app_use'] != 'Y') {
			$ret_url = SITE_DOMAIN.'/myclass/player_list?lec_no=' . $no . '&no=' . $idx;
		}
		
		$param = "MasterKey=". $masterkey;
		$param = $param. "&userid=". $userid;
		$param = $param. "&serverip=". $serverip;
		$param = $param. "&child_customer_id=".$child_customer_id;
		$param = $param. "&WebServerTime=". $webservertime;
		$param = $param. "&AquaAuth=". $aquaauth;
		$param = $param. "&timeout=". $timeout;
		$param = $param. "&bookmark_use=1";
		$param = $param. "&return_url=". rawurlencode($ret_url);
		$param = $param. "&bookmark_url=". rawurlencode($defaultHost.'/player/aqua_progress');
		
		if ($device_id == '' || $userid == 'admin') {
			$param = $param. "&progress=10&d_id=&d_count=0";
		} else {
			$param = $param. "&progress=10&d_id=".$device_id."&d_count=2";
		}
		
		$param = $param. "&bookmark_data=". rawurlencode('uid='.$userid.'&uno='.$__user_no.'&si='.$idx.'a'.$su.'a'.$no."&d_model=".$d_model."&enable_id='".$enable_id);
		
		$param = $param. "&url=". rawurlencode($url);
		
		
		// 워터마크 설정
		// wm_text: 워터마크 텍스트
		// 	- 아래 항목은 wm_text를 사용하는 경우만 입력
		// 	- wm_color: 워터마크 텍스트의 색깔(FFFFFF 와 같이 16진수로 표기)
		// 	- wm_size : 0 (0 : 가장 작은 크기, 1 : 보통 크기, 2 : 가장 큰 크기)
		// 	- wm_shade_color : 워터마크 그림자 텍스트의 색깔
		// wm_pos : 0-8까지는 해당 위치 고정, 9 : random하게 나타남
		// wm_image : 워터마크 이미지(워터마크로 사용될 이미지 URL 정보 입력)
		// wm_padding : 패딩 사이즈(픽셀 x,y)
		
		$param = $param. "&wm_pos=". $wmpos;
		$param = $param. "&wm_padding=0,0";
		
		$encparam = exec($_SERVER['DOCUMENT_ROOT']."/AquaAuth/ENCAQALINK2 -t ENC " . "\"" . $param . "\"");

//		echo 'aa : ' . $filename;
//		echo '<br />';
//		echo '<br />';
//        echo 'bb : ' . $param;
//        echo '<br />';
//		echo '<br />';
//		echo 'cc : ' . $encparam;
//		echo '<br />';
//		echo '<br />';
		
		$enc = `$encparam`;
		
		if ($checkOS == 'ios') {
			$fullurl = "cdnmp://cddr_dnp/playlist?param=".$encparam;
		} else {
			$fullurl = "intent://cddr_dnp/playlist?param=".$encparam."#Intent;scheme=cdnmp;action=android.intent.action.VIEW;category=android.intent.category.BROWSABLE;package=com.cdn.aquanmanager;end";
		}
		
		if ($checkOS == "ios") {
			$market_URL = "https://itunes.apple.com/kr/app/aquanmanager/id1048325731?mt=8&uo=4";
		} else {
			$market_URL = "market://details?id=com.cdn.aquanmanager";
		}
		
		$result_data = array(
			'fullurl' 		=> $fullurl,
			'market_URL' 	=> $market_URL,
		);
		
		return $result_data;
	}
	
	/**
	 * 이어보기 로그 체크, 없으면 insert
	 */
	public function play_check()
	{
		$lmno = $this->input->post("lmno", true);
		$lec_code = $this->input->post("lec_code", true);
		$lec_num = $this->input->post("lec_num", true); // lec_kang_num
		$user_id = $this->session->userdata('member_id', true);
		$ls_kind = "p";
		
		if(!is_numeric($lmno)){
			$arr_return = array ('success'=>false, 'message'=>'Block Parameter.1');
			echo json_encode($arr_return);
			exit;
		}
		
		if(!is_numeric($lec_num)){
			$arr_return = array ('success'=>false, 'message'=>'Block Parameter.2');
			echo json_encode($arr_return);
			exit;
		}
		
		if (!preg_match("/^[A-Za-z0-9_]{3,20}$/i", $user_id)) {
			if(strpos($user_id, "intra_") !== false){
			
			} else if(strpos($user_id, "tB2B_") !== false) {
			
			} else {
				$arr_return = array ('success'=>false, 'message'=>'Block Parameter.3');
				echo json_encode($arr_return);
				exit;
			}
		}
		
		//if(strpos($user_id, "tB2B_") !== false){
		$odri_id_condigion = "user_id = '".$user_id."'";
		//}else{
		//    $odri_id_condigion = " odri_id = '".$lmno ."'";
		//}
		
		## player speed
		$speed_rate = "700";
		
		## lecture_kang number
		$lec_num = ($lec_num == '0') ? '1' : $lec_num ;
		
		$lec_type = "a";
		
		## 이어듣기 체크
		$log_result = $this->m_player->check_player_log($user_id, $lmno, $lec_code, $ls_kind, $lec_num, $speed_rate, $lec_type);
		
		$loading_time = $log_result["loading_time"];
		$loading_time_no =  $log_result["loading_time_no"];
		
		$startTime_continue = 0;
		
		if ($log_result['original_loading_time'] > 0) {
			$startTime_continue = $log_result['original_loading_time'];
			
			$startTime_viewformat_hour = (floor(($startTime_continue/3600)) < 10)? "0".floor(($startTime_continue/3600)) : floor(($startTime_continue/3600));
			
			if(floor($startTime_continue/3600) > 0){
				$startTime_continue2 = $startTime_continue - (3600 * floor($startTime_continue/3600));
				$startTime_viewformat_minute = (floor(($startTime_continue2/60)) < 10)? "0".floor(($startTime_continue2/60)) : floor(($startTime_continue2/60));
			}else{
				$startTime_viewformat_minute = (floor(($startTime_continue/60)) < 10)? "0".floor(($startTime_continue/60)) : floor(($startTime_continue/60));
			}
			
			$startTime_viewformat_second = (floor(($startTime_continue%60)) < 10)? "0".floor(($startTime_continue%60)) : floor(($startTime_continue%60));
			
			if($startTime_viewformat_hour > 0){
				$startTime_viewformat = $startTime_viewformat_hour." : ".$startTime_viewformat_minute." : ".$startTime_viewformat_second;
			}else if($startTime_viewformat_minute >0){
				$startTime_viewformat = $startTime_viewformat_minute." : ".$startTime_viewformat_second;
			}else{
				$startTime_viewformat = "00 : ".$startTime_viewformat_second;
			}
			
		}
		
		$arr_return = array (
			'success'               =>      true,
			'message'               =>      'ok',
			'loading_time_no'       =>      $loading_time_no,
			'loading_time'          =>      $loading_time,
			'startTime_viewformat'  =>      $startTime_viewformat
		);
		
		echo json_encode($arr_return);
		exit;
	}
	
	public function callChampAPI($lecData)
	{
		//Parameter
		$cpn_no = $lecData['discount_etc'];
		$site_code = 'JR';//공무원인강:GI, 경찰공무원인강:PI, 공인중개사인강:LI, 주택관리사:HI, 금융:FN, 임용인강:TE, 중국어:HC, 취업인강 : JI
		$site_mem_id = $_SESSION['member_id'];
		
		//전달할 데이터 생성.
		$post_data = http_build_query(
			array(
				'm' => 'api',
				'a' => 'evt_freepass_deviceid_list',
				'cpn_no' => $cpn_no,
				'site_code' => $site_code,
				'site_mem_id' => $site_mem_id
			)
		);
		
		$opt = array('http' =>
			array(
				'method' => 'POST',
				'header' => 'Content-type: application/x-www-form-urlencoded',
				'content' => $post_data
			)
		);
		
		$context = stream_context_create($opt);
		
		//전송URL
		$target_url = "https://champ.hackers.com";
		$result = file_get_contents($target_url, false, $context);
		
		$arr_data = json_decode($result,true);
		
		if ($arr_data['result'] == 'success') { //성공
			$device_id = $arr_data['d_id'];
		} else {//실패
			//echo $arr_data['msg'];
		}
		
		$this->m_lecture->insertChampLog($arr_data, $cpn_no);
		
		return $device_id;
	}
	
	## LecAttendance Insert
	public function playerLecAttendanceCheck()
	{
		$LecAttendanceInsertCheck = $this->m_lecture->LecAttendanceInsertCheck($this->input->post('lmno'), $this->input->post('seq'), $this->input->post('uno'));
		if (!empty($LecAttendanceInsertCheck)) {
			echo json_encode(array('result' => false, 'msg' => iconv('EUC-KR', 'UTF-8', '이미 등록되어있습니다.')));
			exit;
		}
		
		$LecAttendanceInsert = $this->m_lecture->LecAttendanceInsert($this->input->post('lmno'), $this->input->post('seq'), $this->input->post('uno'), $this->input->post('uid'));
		if ($LecAttendanceInsert) {
			echo json_encode(array('result' => true, 'msg' => iconv('EUC-KR', 'UTF-8', '등록완료')));
		} else {
			echo json_encode(array('result' => false, 'msg' => iconv('EUC-KR', 'UTF-8', '등록실패 관리자에 문의해주세요.')));
		}
		exit;
	}
	
}