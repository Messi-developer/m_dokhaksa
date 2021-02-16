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
			'title' => '���л� �÷��̾�',
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
		// LecAttendance�� �α� �ױ�
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
		
		//�ϸ�ũ ��뿩�� ���� �ɼ�
		//0: �ϸ�ũ ��� ������
		//1: �ϸ�ũ ��� ���
		$bookmark_use = 0;
		
		//����ڰ� ������ �ϸ�ũ ���� ���� �ɼ� (����:ms)
		//�ϸ�ũ ������ �ټ��� ��� "|" �� �̿��Ͽ� ����
		$bookmarkpos = '';
		
		//���������ġ
		$startpos = 0;
		
		//.smi �ڸ�URL
		$subtitle_url = '';
		
		/*
		urlpath : VOD ���� URL
		urltitle : ȭ��� �������� �̸�
		default : ó�� ��� �� �������� contents (��� false�ΰ�� ù��° ������� �����)
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
		$xml .= "					<urltitle>�Ϲ�ȭ��</urltitle>";
		$xml .= "					<default>true</default>";
		$xml .= "				</url>";
		$xml .= "				<url>";
		$xml .= "					<urlpath>http://mvod.aquan.hackers.co.kr/champstudymobile/wmv/withus/".$target."</urlpath>";
		$xml .= "					<urltitle>��ȭ��</urltitle>";
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
	
	## �÷��̾� ��������
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
		
		
		// è�� ���������н� ���� ����
//        $lecData = $this->m_lecture->getLecData($idx);
//        if ($lecData['discount_div'] == 'V') {
//            //$device_id = $this->callChampAPI($lecData);
//            $device_id = '';
//        }
		
		// �������� ��ȸ
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
		$find_lecType = $find_type[1];  //wmv or mp4 ����
		$target_url = str_replace(".wmv",".mp4", $target_url); // ���� ���ڵ� ����
		
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
		
		/* �� �÷��̾� */
		if ($lec_path == "PC_path") { //Ȯ���� mp4�ΰ��
			$Url = "https://mvod.aquan.hackers.co.kr/champstudymobile/wmv/withus/". $filename;  //pc ��ȭ�� ���
		} else { //Ȯ���� wmv�� ���
			$Url = "https://mvod.aquan.hackers.co.kr/champstudymobile/wmv/withus/". $filename;  //����� ��ȭ�� ���
		}
		
		// �ε��ð� üũ
		if ($enable_pos > 0) {
			//$param = $param. "&pos=".($enable_pos*1000);
			$enable_pos = $enable_pos * 1000;
		}
		
		/* ����Ʈ �÷��̾� */
		$url = 'https://'.$_SERVER["HTTP_HOST"].'/player/playlist/xmlload?lectureNo='. $no ."&target=". $filename ."&pos=". $enable_pos;
		//$url = $this->xmlload($filename,$enable_pos);
		
		//�𵨸� üũ
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
		
		
		// ���͸�ũ ����
		// wm_text: ���͸�ũ �ؽ�Ʈ
		// 	- �Ʒ� �׸��� wm_text�� ����ϴ� ��츸 �Է�
		// 	- wm_color: ���͸�ũ �ؽ�Ʈ�� ����(FFFFFF �� ���� 16������ ǥ��)
		// 	- wm_size : 0 (0 : ���� ���� ũ��, 1 : ���� ũ��, 2 : ���� ū ũ��)
		// 	- wm_shade_color : ���͸�ũ �׸��� �ؽ�Ʈ�� ����
		// wm_pos : 0-8������ �ش� ��ġ ����, 9 : random�ϰ� ��Ÿ��
		// wm_image : ���͸�ũ �̹���(���͸�ũ�� ���� �̹��� URL ���� �Է�)
		// wm_padding : �е� ������(�ȼ� x,y)
		
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
	 * �̾�� �α� üũ, ������ insert
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
		
		## �̾��� üũ
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
		$site_code = 'JR';//�������ΰ�:GI, �����������ΰ�:PI, �����߰����ΰ�:LI, ���ð�����:HI, ����:FN, �ӿ��ΰ�:TE, �߱���:HC, ����ΰ� : JI
		$site_mem_id = $_SESSION['member_id'];
		
		//������ ������ ����.
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
		
		//����URL
		$target_url = "https://champ.hackers.com";
		$result = file_get_contents($target_url, false, $context);
		
		$arr_data = json_decode($result,true);
		
		if ($arr_data['result'] == 'success') { //����
			$device_id = $arr_data['d_id'];
		} else {//����
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
			echo json_encode(array('result' => false, 'msg' => iconv('EUC-KR', 'UTF-8', '�̹� ��ϵǾ��ֽ��ϴ�.')));
			exit;
		}
		
		$LecAttendanceInsert = $this->m_lecture->LecAttendanceInsert($this->input->post('lmno'), $this->input->post('seq'), $this->input->post('uno'), $this->input->post('uid'));
		if ($LecAttendanceInsert) {
			echo json_encode(array('result' => true, 'msg' => iconv('EUC-KR', 'UTF-8', '��ϿϷ�')));
		} else {
			echo json_encode(array('result' => false, 'msg' => iconv('EUC-KR', 'UTF-8', '��Ͻ��� �����ڿ� �������ּ���.')));
		}
		exit;
	}
	
}