<?php

class M_member extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->haksa2080 = $this->load->database("haksa2080", TRUE);
		$this->tsms = $this->load->database('tsms', true);
		// $this->champ = $this->load->database("hackersjob_champ", TRUE);
    }
	
	## SMS 발송 log
	function sendSMS($message, $phone, $sendNumber,$subject = "해커스 독학사")
	{
		// 실시간 전송으로 변경
		if (strlen($message) > 90){
			$insertData = array(
				'SUBJECT' => $subject
				,'PHONE' => $phone
				,'CALLBACK' => $sendNumber
				,'STATUS' => '0'
				,'REQDATE' => date('Y-m-d H:i:s')
				,'MSG' => $message
				,'TYPE' => '0'
			);
			$result = $this->tsms->insert('MMS_MSG', $insertData);
			
		} else {
			$insertData = array(
				'TR_SENDDATE' => date('Y-m-d H:i:s')
				,'TR_SENDSTAT' => '0'
				,'TR_PHONE' => $phone
				,'TR_CALLBACK' => $sendNumber
				,'TR_MSG' => $message
				,'TR_MSGTYPE' => '0'
				,'TR_ETC4' => 'o'
			);
			
			$result = $this->tsms->insert('SC_TRAN', $insertData);
		}
		
		return $result;
	}
 
	## 인증번호 생성
	public function numbering($uid, $target, $div){ //uid : case구분자 , target : email주소 or phone 번호 , div : 발송 수단
		
		$y = date("Y");
		$m = date("m");
		$d = date("d");
		$h = date("H");
		$i = date("i");
		$s = date("s");
		
		$yy = $y - 2000;
		$date = $yy.$m.$d;
		
		$hh = $h * 60 * 60;
		$ii = $i * 60;
		$ss = $s;
		
		if($m < 10) $m = $m + 12;
		$number = ($hh + $ii + $ss + $d) * $m + $y;
		
		for($i=6;$i>=0;$i--){
			$number_r .= substr($number,$i,1);
		}

//		if($div == "sms"){
//			$target_send = "send_phone";
//		}else if($div == "email"){
//			$target_send = "send_email";
//		}
		
		$number_r = substr($number_r,0,6);
		$nowDate = date('Y-m-d H:i:s');
		
		$insertData = array(
			'send_code' => $number_r
			,'send_phone' => $target
			,'uid' => $uid
			,'wdate' => $nowDate
			,'certify' => 'n'
		);
		
		$this->haksa2080->insert('sms_certification', $insertData); // 인증절차 확인
		$insert_id  = $this->haksa2080->insert_id();
		
		// echo 'numbering = ' . $number_r."<>".$insert_id;
		// echo '<br />';
		return $number_r."<>".$insert_id;
		
	}
 
	## 회원가입 sms 발송
	function certSend($case, $cert_number, $cert_method) {
		$retValue = $this->numbering($case, $cert_number, $cert_method); // 인증번호 생성
		$certArr = explode("<>",$retValue);
		
		if (!$certArr[0] || !$certArr[1]) {
			return false;
		} else {
			$send_result = $this->sendSMS("[해커스 독학사] [".$certArr[0]."] 회원가입 인증번호입니다. 정확히 입력해주세요.", $cert_number, "15993081");
			
			if (!$send_result) {
				return false;
			}
			
			return $certArr;
		}
	}
    
    ## 회원가입된 유저가 있는지 확인
	public function getUserJoinCheck($birth, $handphone_index)
	{
    	$sql = "SELECT `no`, user_id FROM zetyx_member_table WHERE new_birth = '". $birth ."' AND handphone_index = '". $handphone_index ."'";
    	$query = $this->haksa2080->query($sql);
		return $query->row();
	}
    
	## 유저정보 조회
	public function getUserInfo($member_no, $member_id)
	{
		$query = $this->haksa2080->query("SELECT * FROM zetyx_member_table WHERE `no` = '". $member_no ."' AND user_id = '". $member_id ."'");
		return $query->row();
	}
	
	## 유저정보 조회
	public function getCouponInfo($coupon_number)
	{
		$query = $this->haksa2080->query("SELECT * FROM auth_list WHERE cupone_number = '". $coupon_number ."' AND user_no IS NULL AND user_id IS NULL");
		return $query->row();
	}
	
	## 유저보유 쿠폰 list 가져오기
	function getUserCoupon($member_no, $member_id)
	{
		$sql = "
				SELECT cupone_name, cupone_number, lucky_price, lucky_percent, lec_no, DATE_FORMAT(start_date, '%Y-%m-%d') as start_date, DATE_FORMAT(end_date, '%Y-%m-%d') as end_date
				FROM auth_list
				WHERE user_id = '". $member_id ."' AND user_no = '". $member_no ."'
				AND DATE_FORMAT(end_date, '%Y-%m-%d') >= DATE_FORMAT(NOW(), '%Y-%m-%d')
				ORDER BY end_date DESC
				";
		
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}
	
	## 결제시 사용가능한 쿠폰 리스트 가져오기
	function getLectureMemCoupon($member_no, $member_id, $lec_no)
	{
		$coupon_search = (!empty($lec_no)) ? "AND lec_no LIKE ('%". $lec_no ."%')" : "AND (lec_no = '' or lec_no IS NULL)";
		
		$sql = "
				SELECT cupone_name, cupone_number, lucky_price, lucky_percent, lec_no, DATE_FORMAT(start_date, '%Y-%m-%d') as start_date, DATE_FORMAT(end_date, '%Y-%m-%d') as end_date
				FROM auth_list
				WHERE user_id = '". $member_id ."' AND user_no = '". $member_no ."'
				AND DATE_FORMAT(end_date, '%Y-%m-%d') >= DATE_FORMAT(NOW(), '%Y-%m-%d')
				$coupon_search
				
				ORDER BY end_date DESC
				";
		
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}
	
	## 유저보유 쿠폰 가져오기 (Ajax) limit
	function getUserCouponList($member_no, $member_id, $limit)
	{
		$limit = (!empty($limit)) ? $limit : '0' ;
		$sql = "
				SELECT cupone_name, cupone_number, lucky_price, lucky_percent, DATE_FORMAT(start_date, '%Y-%m-%d') as start_date, DATE_FORMAT(end_date, '%Y-%m-%d') as end_date
				FROM auth_list
				WHERE user_id = '". $member_id ."' AND user_no = '". $member_no ."'
				AND DATE_FORMAT(end_date, '%Y-%m-%d') >= DATE_FORMAT(NOW(), '%Y-%m-%d')
				ORDER BY end_date DESC
				LIMIT $limit, 5
				";
		
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}
	
	## 유저 사용한 쿠폰리스트 가져오기
	function getUserUsedCoupon($member_no, $member_id)
	{
		$sql = "
				SELECT cupone_name, cupone_number, lucky_price, lucky_percent, DATE_FORMAT(start_date, '%Y-%m-%d') as start_date, DATE_FORMAT(end_date, '%Y-%m-%d') as end_date
				FROM auth_list_used
				WHERE user_id = '". $member_id ."' AND user_no = '". $member_no ."'
				ORDER BY end_date DESC
				";
		
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}
	
	## 유저 사용불가 쿠폰리스트 (Ajax) limit
	function getUserEndCouponList($member_no, $member_id, $limit)
	{
		$limit = (!empty($limit)) ? $limit : '0' ;
		$sql = "
		SELECT cupone_name, cupone_number, lucky_price, lucky_percent, DATE_FORMAT(start_date, '%Y-%m-%d') as start_date, DATE_FORMAT(end_date, '%Y-%m-%d') as end_date
		FROM auth_list_used
		WHERE user_no = '". $member_no ."' AND user_id = '". $member_id ."'
		LIMIT $limit, 5
		";
		
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}
	
	## 유저 사용불가 쿠폰리스트 cnt
	function getUserEndCouponListCnt($member_no, $member_id)
	{
		$sql = "
		SELECT COUNT(`no`) as cnt
		FROM auth_list_used
		WHERE user_no = '". $member_no ."' AND user_id = '". $member_id ."'
		AND DATE_FORMAT(end_date, '%Y-%m-%d %H:%i:%s') < DATE_FORMAT(NOW(), '%Y-%m-%d')
		";
		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
	
	## 쿠폰존재 확인
	function getCouponCheck($coupon_number)
	{
		$sql = "
				SELECT cupone_name, cupone_number, lucky_price, lucky_percent, DATE_FORMAT(start_date, '%Y-%m-%d') as start_date, DATE_FORMAT(end_date, '%Y-%m-%d') as end_date
				FROM auth_list
				WHERE cupone_number = '". $coupon_number ."'
				AND user_id IS NULL
				AND end_date IS NULL
				ORDER BY end_date DESC
				";
		
		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
	
	## 유저 특정쿠폰 보유 확인
	function getUserCouponCheck($member_no, $member_id, $coupon_number)
	{
		$sql = "
				SELECT cupone_name, cupone_number, lucky_price, lucky_percent, DATE_FORMAT(start_date, '%Y-%m-%d') as start_date, DATE_FORMAT(end_date, '%Y-%m-%d') as end_date
				FROM auth_list
				WHERE user_no = '". $member_no ."' AND user_id = '". $member_id ."' AND cupone_number = '". $coupon_number ."'
				AND DATE_FORMAT(end_date, '%Y-%m-%d') >= DATE_FORMAT(NOW(), '%Y-%m-%d')
				ORDER BY end_date DESC
				";
		
		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
	
	## 유저 특정쿠폰 INSERT
	function getUserCouponInsert($member_no, $member_id, $coupon_info)
	{
		$user_info = $this->getUserInfo($member_no, $member_id);
		$coupon_info = $this->getCouponInfo($coupon_info);
		
		$insertData = array(
			'user_no' => $member_no
			,'user_id' => $member_id
			,'user_name' => $user_info->name
			,'cupone_name' => $coupon_info->cupone_name
			,'cupone_number' => $coupon_info->cupone_number
			,'lucky_price' => $coupon_info->lucky_price
			,'lucky_percent' => $coupon_info->lucky_percent
			,'lucky_term' => $coupon_info->lucky_term
			,'start_date' => date('Y:m:d')
			,'end_date' => date('Y:m:d', strtotime("+{$coupon_info->lucky_term}day"))
			,'regi_date' => date('Y:m:d')
			,'use_date' => 0
			,'use_date_temp' => 0
			,'copone_mode' => ""
			,'wdate' => date('Y:m:d') . '-' . date('H:i:s')
			,'re_buy' => $coupon_info->re_buy
			,'xxx' => $coupon_info->xxx
			,'alert' => 0
			,'lec_no' => $coupon_info->lec_no
		);

		return $this->haksa2080->insert('auth_list', $insertData); // 쿠폰등록
	}
	
	
	## 유저 보유 포인트
	function getUserPoint($member_id)
	{
		$sql = "SELECT SUM(`point`) as `use_point` FROM use_point WHERE mem_user_id = '". $member_id ."' AND point_state = '1'";
		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
	
	## 유저 차감 포인트
	function getUserUsedPoint($member_id)
	{
		$sql = "SELECT SUM(`point`) as `used_point` FROM haksa2080.`used_point` WHERE mem_user_id = '". $member_id ."' AND point_state = '1'";
		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
	
	## 유저 지급 포인트 리스트 (limit)
	function getUserPointList($member_id, $limit)
	{
		$limit = (!empty($limit)) ? $limit : '0' ;
		$sql = "SELECT mem_user_id, `point`, point_title, LEFT(wdate, 10) as wdate FROM haksa2080.`use_point` WHERE mem_user_id = '". $member_id ."' AND point_state = '1' ORDER BY `no` DESC LIMIT $limit, 5";
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}
	
	## 유저 지급 포인트 리스트
	function getUserPointListAll($member_id)
	{
		$sql = "SELECT mem_user_id, `point`, point_title, LEFT(wdate, 10) as wdate FROM haksa2080.`use_point` WHERE mem_user_id = '". $member_id ."' AND point_state = '1' ORDER BY `no` DESC";
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}
	
	## 유저 사용 포인트 리스트 (limit)
	function getUserPointUsedList($member_id, $limit)
	{
		$limit = (!empty($limit)) ? $limit : '0' ;
		$sql = "SELECT mem_user_id, `point`, point_title, LEFT(wdate, 10) as wdate, order_num, lecture_no, point_state FROM haksa2080.`used_point` WHERE mem_user_id = '". $member_id ."' AND point_state IN ('1', '2', '3') ORDER BY `no` DESC LIMIT $limit, 5";
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}
	
	## 유저 사용 포인트 리스트 (limit)
	function getUserPointUsedListAll($member_id)
	{
		$sql = "SELECT mem_user_id, `point`, point_title, LEFT(wdate, 10) as wdate FROM haksa2080.`used_point` WHERE mem_user_id = '". $member_id ."' AND point_state IN ('1', '2', '3') ORDER BY `no` DESC";
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}
	
	## 휴먼회원 체크
	public function checkDormant($user_id)
	{
		$sql = "SELECT `no`, user_id FROM rest_zetyx_member_table WHERE user_id = '". $user_id ."'";
		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
	
	## 휴면회원 일반회원으로 전환
	public function restMemberAgain($no, $member_id)
	{
		## 회원복구
    	$insertSql = "INSERT INTO zetyx_member_table (SELECT * FROM rest_zetyx_member_table WHERE `no` = '". $no ."' AND user_id = '". $member_id ."')";
		$this->haksa2080->query($insertSql);
		
		## 휴면회원 삭제
		$this->haksa2080->where("no", $no);
		$this->haksa2080->where("user_id", $member_id);
		return $this->haksa2080->delete("rest_zetyx_member_table");
	}
	
	## 유저아이디 존재여부
	public function checkUserId($user_id)
	{
		$query = $this->haksa2080->query("SELECT `no` FROM zetyx_member_table WHERE user_id = '". $user_id ."'");
		$result_data = $query->row_array();
		
		return $result_data;
	}
	
	## 유저 password_new check
	public function checkUserPassword($password)
	{
    	$query = $this->haksa2080->query("select SHA2('$password',256) as password_new");
		$result_data = $query->row();
		
		return $result_data;
	}
	
	## 유저 아이디, 비밀번호 체크
	public function checkUserData($user_id, $password)
	{
		$query = $this->haksa2080->query("SELECT * FROM zetyx_member_table WHERE user_id = '". $user_id ."' AND password_new = '". $password ."'");
		return $query->row_array();
	}
	
	## 유저 회원가입 핸드폰 인증
	public function checkUserHandPhone($handphone, $birth, $cert_type)
	{
		$query = $this->haksa2080->query("SELECT `no`, user_id FROM zetyx_member_table WHERE handphone_index = '". $handphone ."' AND new_birth = '". $birth ."'");
		$result_array = $query->result_array();
		return $result_array;
	}

	## 유저 회원가입 핸드폰 인증
	public function checkCertification($insert_id, $send_code)
	{
		$query = $this->haksa2080->query("SELECT * FROM sms_certification WHERE `no` = '". $insert_id ."'");
		$select_array = $query->row();
		
		if ($send_code == $select_array->send_code) {
			$updateData = array(
				'certify' => 'y'
			);
			$updateWhere = "`no` = '". $insert_id ."'";
			$updateState = $this->haksa2080->update('sms_certification', $updateData, $updateWhere);
			$result_array = array('result' => true, 'msg' => '인증번호가 확인되었습니다.', 'update_state' => $updateState);
		} else {
			$result_array = array('result' => false, 'msg' => '입력하신 인증번호가 일치하지 않습니다. 인증번호 재발송 후 다시 시도해주세요.');
		}
		
		return $result_array;
	}
	
	## 회원가입 완료
	public function memberJoinFinsh($member, $member_detail)
	{
		$memberInsert = array(
			'group_no' => $member['group_no']
			,'user_id' => $member['user_id']
			,'password_new' => $member['password_new']
			,'name' => $member['name']
			,'level' => '9'
			,'email' => $member['email'] . '@' . $member['join_email']
			,'job' => $member['job']
			,'hobby' => $member['hobby']
			,'uno_new' => $member['uno_new']
			,'home_address' => $member['home_address']
			,'handphone' => $member['handphone']
			,'handphone_index' => $member['handphone_index']
			,'handphone_cert' => $member['handphone_cert']
			,'mailing' => $member['mailing']
			,'birth' => $member['birth']
			,'new_birth' => $member['new_birth']
			,'reg_date' => $member['reg_date']
			,'usepoint' => 5000
			,'rc_date' => $member['rc_date']
			,'rc_ip' => $member['rc_ip']
			,'sms' => $member['sms']
			,'sex' => $member['sex']
			,'join_channel' => $member['join_channel']
			,'email_agree_site' => $member['email_agree_site']
		);
		
		
		$memberInsert = $this->haksa2080->insert('zetyx_member_table', $memberInsert); // 유저 insert + 5000원 포인트 지급
		$member_insert_id = $this->haksa2080->insert_id();
		
		// 회원 디테일 insert
		$memberDetailInsert = array(
			'mem_no' => $member_insert_id
			,'user_id' => $member_detail['user_id']
			,'level_edu' => $member_detail['level_edu'] // 최종학력
			,'level_edu_major' => $member_detail['level_edu_major'] // 최종학력 상세정보
			,'question' => $member_detail['question'] // 문의사항
		
			,'hope_majer' => $member_detail['hope_majer'] // 희망학과
			,'hope_majer_sub' => $member_detail['hope_majer_sub'] // 희망학과 sub
			,'lec_object' => $member_detail['lec_object'] // 수강목적
			,'user_job' => $member_detail['user_job'] // 현재직업
			,'join_route' => $member_detail['join_route'] // 가입경로
			,'study_object' => $member_detail['study_object'] // 가입목적
		);
		
		$this->haksa2080->insert('zetyx_member_table_detail', $memberDetailInsert); // 유저디테일 insert
		
		// 회원가입 포인트 지급
		$memberPointInsert = array(
			'mem_user_id' => $member['user_id']
			,'point_title' => '신규회원가입 이벤트 포인트 지급'
			,'point' => 5000
			,'point_term' => $member['wdate']
			,'point_state' => '1'
			,'wdate' => $member['wdate']
		);
		
		$this->haksa2080->insert('use_point', $memberPointInsert); // 유저 insert + 5000원 포인트 지급
		
		if ($memberInsert) {
			$result_array = array('result' => true, 'msg' => '해커스독학사 회원가입완료');
		} else {
			$result_array = array('result' => false, 'msg' => '회원가입오류!! 관리자에 문의해주세요.');
		}
		
		return $result_array;
	}
	
	## 유저아이디 찾기
	public function searchMemberId($searchType, $member_name, $member_birth, $search)
	{
		if ($searchType == 'search_tel') {
			$query = $this->haksa2080->query("SELECT user_id, `name` FROM zetyx_member_table WHERE `name` = '". $member_name ."' AND new_birth = '". $member_birth ."' AND handphone_index = '". $search ."'");
			$result_array = $query->row();
		} else {
			$query = $this->haksa2080->query("SELECT user_id, `name` FROM zetyx_member_table WHERE `name` = '". $member_name ."' AND new_birth = '". $member_birth ."' AND email = '". $search ."'");
			$result_array = $query->row();
		}
		return $result_array;
	}
	
	
	## 유저 비밀번호 찾기
	public function searchMemberPassword($searchType, $member_id, $member_name, $member_birth, $search)
	{
		if ($searchType == 'search_tel') {
			$query = $this->haksa2080->query("SELECT `no`, user_id, `name` FROM zetyx_member_table WHERE user_id = '". $member_id ."' AND `name` = '". $member_name ."' AND new_birth = '". $member_birth ."' AND handphone_index = '". $search ."'");
			$result_array = $query->row();
		} else {
			$query = $this->haksa2080->query("SELECT `no`, user_id, `name` FROM zetyx_member_table WHERE user_id = '". $member_id ."' AND `name` = '". $member_name ."' AND new_birth = '". $member_birth ."' AND email = '". $search ."'");
			$result_array = $query->row();
		}
		
		return $result_array;
	}
	
	## 유저정보 가져오기
	public function memberInfo($getMemNo)
	{
		$query = $this->haksa2080->query("
									SELECT
										mem.`no`, mem.sex, mem.sms, mem.password_new, mem.birth, mem.new_birth, mem.mailing, mem.handphone, mem.handphone_index, mem.home_address, mem.uno_new, mem.hobby, mem.job, mem.email, mem.name, mem.user_id, mem.password_new, mem.rc_cnt,
										mem_d.question, mem_d.hope_majer, mem_d.hope_majer_sub, mem_d.lec_object, mem_d.join_route, mem_d.level_edu, mem_d.level_edu_major, mem_d.study_object
									FROM
										zetyx_member_table AS mem
									INNER JOIN zetyx_member_table_detail AS mem_d ON mem.`no` = mem_d.`mem_no`
									WHERE mem.`no` = '". $getMemNo ."'
									");
		return $query->row();
	}
	
	
	## 유저 정보업데이트
	public function memberInfoUpdate($updateType, $member_no, $password_new)
	{
		if ($updateType == 'password') {
			$updateData = array(
				'password_new' => $password_new
			);
			$updateWhere = "`no` = '". $member_no ."'";
			$updateState = $this->haksa2080->update('zetyx_member_table', $updateData, $updateWhere);
			
			return $updateState;
		}
	}
	
	## 유저정보 수정
	public function memberInfoUpdateQuery($member, $member_detail)
	{
		$memberUpdateQuery = array(
			'email' => $member['email']
			,'job' => $member['job']
			,'hobby' => $member['hobby']
			,'uno_new' => $member['uno_new']
			,'home_address' => $member['home_address']
			,'handphone' => $member['handphone']
			,'handphone_index' => $member['handphone_index']
			,'birth' => $member['birth']
			,'new_birth' => $member['new_birth']
			,'reg_date' => $member['reg_date']
			,'rc_date' => $member['rc_date']
			,'rc_ip' => $member['rc_ip']
			,'rc_cnt' => (int)($member['rc_cnt'] + 1)
			,'sms' => $member_detail['sms']
		);
		
		$updateWhere = "`no` = '". $member['member_no'] ."'";
		$memberUpdateRs = $this->haksa2080->update('zetyx_member_table', $memberUpdateQuery, $updateWhere);
		
		$memberDetailUpdate = array(
			'level_edu' => $member_detail['level_edu'] // 최종학력
			,'level_edu_major' => $member_detail['level_edu_major'] // 최종학력 상세정보
			,'question' => $member_detail['question'] // 문의사항

			,'hope_majer' => $member_detail['hope_majer'] // 희망학과
			,'hope_majer_sub' => $member_detail['hope_majer_sub'] // 희망학과 sub
			,'lec_object' => $member_detail['lec_object'] // 수강목적
			,'user_job' => $member_detail['user_job'] // 현재직업
			,'join_route' => $member_detail['join_route'] // 가입경로
			,'study_object' => $member_detail['study_object'] // 가입목적
		);

		$updateWhere = "mem_no = '". $member['member_no'] ."'";
		$memberUpdateRs = $this->haksa2080->update('zetyx_member_table_detail', $memberDetailUpdate, $updateWhere);
		
		if ($memberUpdateRs) {
			$result_array = array('result' => true, 'msg' => '회원정보 수정되었습니다.');
		} else {
			$result_array = array('result' => false, 'msg' => '회원정보 수정실패!!! 관리자에 문의해주세요.');
		}
		
		return $result_array;
	}
	
	
	## 로그인 로그 저장
//	function save_access_log($id, $name) {
//
//		$data = array(
//			'user_id'       => $id,
//			'name'          => $name,
//			'ip'            => $_SERVER['XFFCLIENTIP'] ? $_SERVER['XFFCLIENTIP'] : $_SERVER['REMOTE_ADDR'],
//			'loginsite'     => '공기업모바일',
//			'createdate'    => date("Y-m-d H:i:s"),
//			'access_agent'  => $_SERVER['HTTP_USER_AGENT']
//		);
//
//		$this->haksa2080->insert('access_log',$data);
//	}
	
	
	
//	public function auto_login() {
//
//		if($_COOKIE['HACKERSJOB_PUB']) {
//			include_once str_replace('job_m','hackersjob',$_SERVER['DOCUMENT_ROOT'])."/include/var/var.secukey.php";
//			$cookie = $this->m_member->decrypt($_COOKIE['HACKERSJOB_PUB'], $secukey);
//
//			$autologin = explode("|", $cookie);
//
//			$_SESSION['niceId'] = $autologin[0];
//			$_SESSION['niceName'] = $autologin[1];
//			$_SESSION['user_id'] = $autologin[2];
//			$_SESSION['user_no'] = $autologin[3];
//			$_SESSION['user_name'] = $autologin[4];
//			$_SESSION['is_temp_password'] = $autologin[5];
//			$_SESSION['user_login_date'] = date('Y-m-d H:i:s');
//
//			$this->m_member->login_date_update($_SESSION['user_no']);
//		}
//
//	}
	

    // 휴먼회원 유저아이디 존재여부
//    public function checkDormantUserId($value, $column = 'user_id')
//    {
//        $this->champ->select("*");
//        $this->champ->from("zetyx_member_table_dormant");
//
//        if($column == 'user_id'){
//            $this->champ->where("REPLACE(user_id,' ','') = '$value'");
//        }else{
//            $this->champ->where($column,$value);
//        }
//
//
//        $query = $this->champ->get();
//        $result_data = $query->row_array();
//
//        return $result_data;
//    }


    // (이름,이메일,성별) 회원 존재여부 체크
    public function checkNaverUser($name, $email, $gender)
    {
        $this->champ->select("user_id");
        $this->champ->from("zetyx_member_table");
        $this->champ->where("name",$name);
        $this->champ->where("email",$email);
        $this->champ->where("sex",$gender);
 
        $query = $this->champ->get();
        $result_data = $query->row_array();

        return $result_data;
    }

    // 네이버 연동 아이디 존재여부 체크 (이름,이메일,성별)
//    public function getSnsUser($name, $email, $gender)
//    {
//        $this->champ->select(" *,  RPAD(SUBSTR(user_id, 1, 4), LENGTH(user_id), '*') AS  id",false);
//        $this->champ->from("zetyx_member_table");
//        $this->champ->where("name",$name);
//        $this->champ->where("email",$email);
//        $this->champ->where("sex",$gender);
//        $this->champ->where("sns",'N');
//        $this->champ->order_by("last_visit","DESC");
//
//        $query = $this->champ->get();
//        $result_data = $query->row_array();
//
//        return $result_data;
//    }

    //네이버 아이디로 로그인한 유저인지 체크
//    public function isNaverUser($name, $email, $gender)
//    {
//        $this->champ->select("no");
//        $this->champ->from("zetyx_member_table");
//        $this->champ->where("user_id",$_SESSION['niceId']);
//        $this->champ->where("sns","N");
//
//        $query = $this->champ->get();
//        $total = $query->num_rows();
//
//        $result_data = ($total > 0) ? true : false;
//
//        return $result_data;
//    }

    //네이버 연동 휴면아이디 존재여부 체크 (이름,이메일,성별)
//    public function getSnsDormantUser($name, $email, $gender)
//    {
//
//        $this->champ->select("user_id");
//        $this->champ->from("zetyx_member_table_dormant");
//        $this->champ->where("name",$name);
//        $this->champ->where("email",$email);
//        $this->champ->where("sex",$gender);
//        $this->champ->where("sns",'N');
//        $this->champ->order_by("last_visit","DESC");
//
//        $query = $this->champ->get();
//        $result_data = $query->row_array();
//
//        return $result_data;
//    }

    
    //유저 네이버 연동처리
//    public function setSnsUser($name, $email, $gender)
//    {
//        $this->champ->set("sns","N");
//        $this->champ->where("name",$name);
//        $this->champ->where("email",$email);
//        $this->champ->where("sex",$gender);
//        $this->champ->update("zetyx_member_table");
//    }

    //회원가입 유저 연락처 중복체크
    public function checkPhone($handphone,$type)
    {
        if($type == 'Parent'){
            $tableName ='member_parent_certification_info';
            $columnName = 'parent_handphone';
        }else{
            $tableName ='zetyx_member_table' ;
            $columnName = 'handphone';
        }

        $this->champ->select("no");
        $this->champ->from($tableName);
        $this->champ->where("REPLACE(".$columnName.",'-','') = '".$handphone."'");
        
        $query = $this->champ->get();
        $total = $query->num_rows();

        return $total;
     }

    // 회원가입 유저 정보 저장
    public function setJoinUser($userData)
    {
        $userInfo = $this->checkUserId($userData['user_id']) ;
        $userDormantInfo = $this->checkDormant($userData['user_id']) ;

        if(!$userInfo && empty($userInfo) && empty($userDormantInfo) && !$userDormantInfo){

            $array_etc = array(
                "희망기업" => $userData['company'] ? $userData['company'] : '' ,
                "희망직무" => $userData['hopejob'] ? $userData['hopejob'] : '' ,
                "스팩" => $userData['spec'] ? $userData['spec']  : ''
            );

            $phone = str_replace('-', '', $userData['handphone']);
            $home_tel = str_replace('-', '', $userData['home_tel']);
            $birth = str_replace('-', '', $userData['birth']);
            
            if($phone) $phone = strlen($phone) < 11 ? substr($phone,0,3).'-'.substr($phone,3,3).'-'.substr($phone,6,4) : substr($phone,0,3).'-'.substr($phone,3,4).'-'.substr($phone,7,4);
            if($home_tel) $home_tel = strlen($home_tel) < 10 ? substr($home_tel,0,2).'-'.substr($phone,2,3).'-'.substr($phone,5,3) : substr($phone,0,3).'-'.substr($phone,3,4).'-'.substr($phone,7,3);
            if($birth) $birth = substr($birth,0,4).'-'.substr($birth,4,2).'-'.substr($birth,6,2);
            
            $data = array(
                'group_no'              => $userData['group_no'] ,  // group_no : 4 => 14세미만회원
                'name'                  => $userData['name'] ,
                'name2'                 => $userData['name2'] ,
                'sex'                   => $userData['sex'],    // 성별 (M:남  F:여)
                'birth'                 => $birth,
                'user_id'               => $userData['user_id'],
                'password_date'         => date("Y-m-d H:i:s") ,
                'email'                 => $userData['email'] ,
                'handphone'             => $phone,
                'home_tel'              => $home_tel ,
                'sms'                   => $userData['sms'] ,  //sms 수신동의여부 (1:수신 0:거부)
                'mailing'               => $userData['mailing']  , //메일수신동의여부 (1:수신 0:거부)
                'join_path'             => $userData['join_path'] ? $userData['join_path'] : '' , //가입경로
                'privacy_agree'         => $userData['privacy_agree'] , //개인정보 처리업무 위탁 동의여부 (Y:동의)
                'interesting'           => $userData['sns'] != 'N' ? serialize($array_etc) : '' ,
                'new_confirm'           => 1 ,
                'reg_date'              => date("Y-m-d H:i:s") ,
                'new_confirm_date'      => date("Y-m-d H:i:s") ,
                'mem_modification_date' => date("Y-m-d H:i:s") ,
                'last_visit'            => date("Y-m-d H:i:s") ,
                'visit'                 => 1,
                'sns'                   => $userData['sns']     // 네이버연동여부  (N:네이버회원)
            );

            $query = $this->champ->insert('zetyx_member_table',$data);

             if($query){
                if($userData['sns'] != 'N'){

                    //마지막 insert된 유저번호 가져오기
                    $sql ="SELECT LAST_INSERT_ID() AS uno";
                    $query  = $this->champ->query($sql);
                    $result = $query->row_array();

                    $this->setPassWord($result['uno'],$userData['password']);
                }

                $_SESSION['niceId'] = $userData['user_id'];
                $_SESSION['niceName'] = $userData['name'];
                $_SESSION['user_id'] = $userData['user_id'];
                $_SESSION['user_no'] = $userData['no'];
                $_SESSION['user_name'] = $userData['name'];
            }

            //14세미만 회원 보호자 인증정보 저장
            if($userData['group_no'] == '4'){
                $parent_phone = $userData['parent_handphone'];
                if($parent_phone) $parent_phone = strlen($parent_phone) < 11 ? substr($parent_phone,0,3).'-'.substr($parent_phone,3,3).'-'.substr($parent_phone,6,4) : substr($parent_phone,0,3).'-'.substr($parent_phone,3,4).'-'.substr($parent_phone,7,4);
                 $parentData = array(
                    'user_id'              => $userData['user_id'],
                    'parent_name'          => $userData['parent_name'],
                    'parent_sex'           => $userData['parent_sex'],
                    'parent_birth'         => $userData['parent_birth'],
                    'parent_handphone'     => $parent_phone,
                    'reg_date'             => date("Y-m-d H:i:s")
                 );
            
                $this->champ->insert('member_parent_certification_info',$parentData);
            }
            return 'ok';
        }
     }

    //아이디 or 비밀번호 찾기
    public function searchUserId($mode , $table ,$userData)
    {
        $this->champ->select("user_id");
        $this->champ->from($table);
        $this->champ->where('name',$userData['name']);
        //$this->champ->where("REPLACE(birth,'-','') = '".$userData['birth']."'");

        if($userData['email']){
            $this->champ->where('email',$userData['email']);
        }else{
            $this->champ->where("REPLACE(handphone,'-','') = '".$userData['handphone']."'");
        }
        
        if($mode == 'pw') $this->champ->where('user_id',$userData['user_id']);

        $query = $this->champ->get();
        $result_data = $query->row_array();

        return $result_data;
    }

    //비밀번호 설정
    public function setPassWord($user_no,$password){

        $tbl_name = "zetyx_member_table";
        //일반회원 체크
        $userInfo = $this->checkUserId($user_no, 'no') ;
        if(empty($userInfo))
            $tbl_name = "zetyx_member_table_dormant";

        if(!empty($user_no) && !empty($password) && $user_no > 0 ){
            $this->champ->set('password',"SHA2(old_password('".$password."'),256)",false);
            $this->champ->set('password_date',date("Y-m-d H:i:s"));
            $this->champ->where('no',$user_no);
            $this->champ->update($tbl_name);
            return 'ok';
        }else{
            return 'empty user info';
        }
    }

    //회원정보수정
    public function editUserInfo($userData)
    {
        $array_etc = array(
            "희망기업" => $userData['company'] ? $userData['company'] : '' ,
            "희망직무" => $userData['hopejob'] ? $userData['hopejob'] : '' ,
            "스팩" => $userData['spec'] ? $userData['spec']  : ''
        );

        $phone = str_replace('-', '', $userData['handphone']);
        $home_tel = str_replace('-', '', $userData['home_tel']);
        $birth = str_replace('-', '', $userData['birth']);
        
        if($phone) $phone = strlen($phone) < 11 ? substr($phone,0,3).'-'.substr($phone,3,3).'-'.substr($phone,6,4) : substr($phone,0,3).'-'.substr($phone,3,4).'-'.substr($phone,7,4);
        if($home_tel) $home_tel = strlen($home_tel) < 10 ? substr($home_tel,0,2).'-'.substr($home_tel,2,3).'-'.substr($home_tel,5,4) : substr($home_tel,0,3).'-'.substr($home_tel,3,3).'-'.substr($home_tel,6,4);
	    if($birth) $birth = substr($birth,0,4).'-'.substr($birth,4,2).'-'.substr($birth,6,2);
        
        $join_path = $userData['join_path'] ? $userData['join_path'] : '';
        
        $this->champ->set("email",$userData['email']);
        $this->champ->set("handphone",$phone);
        $this->champ->set("home_tel",$home_tel);
        $this->champ->set("sms",$userData['sms']);
        $this->champ->set("mailing",$userData['mailing']);
        $this->champ->set("uno",$userData['uno']);
        $this->champ->set("home_address",$userData['home_address']);
        $this->champ->set("tail_address",$userData['tail_address']);
        $this->champ->set("join_path",$join_path);
        $this->champ->set("interesting",serialize($array_etc));
        $this->champ->set("mem_modification_date",date("Y-m-d H:i:s"));
        $this->champ->where("no",$userData['user_no']);
        $this->champ->update("zetyx_member_table");

        return 'ok';
     }

    //네이버연동 해제
    public function removeSnsUser($user_id)
    {
        $this->champ->set("sns","");
        $this->champ->where("user_id",$user_id);
        $this->champ->where("sns","N");
        $this->champ->update("zetyx_member_table");

        return 'ok';
    }

    /*로그인 업데이트*/
    public function login_date_update($user_no) {
        $this->champ->set("last_visit",date("Y-m-d H:i:s"));
        $this->champ->where("no",$user_no);
        $this->champ->update("zetyx_member_table");
    }

    /* 암호화 php 버전이 너무 낮음 */
    public function encrypt($string, $key) {

        $key = hash('sha256', $key);

        $result = '';
        for($i=0; $i<strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key))-1, 1);
            $char = chr(ord($char)+ord($keychar));
            $result .= $char;
        }
        return base64_encode($result);
    }

    /* 복호화 php 버전이 너무 낮음 */
    function decrypt($string, $key) {

        $key = hash('sha256', $key);

        $result = '';
        $string = base64_decode($string);
        for($i=0; $i<strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key))-1, 1);
            $char = chr(ord($char)-ord($keychar));
            $result .= $char;
        }
        return $result;
    }
     
}