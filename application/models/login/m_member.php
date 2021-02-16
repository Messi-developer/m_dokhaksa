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
	
	## SMS �߼� log
	function sendSMS($message, $phone, $sendNumber,$subject = "��Ŀ�� ���л�")
	{
		// �ǽð� �������� ����
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
 
	## ������ȣ ����
	public function numbering($uid, $target, $div){ //uid : case������ , target : email�ּ� or phone ��ȣ , div : �߼� ����
		
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
		
		$this->haksa2080->insert('sms_certification', $insertData); // �������� Ȯ��
		$insert_id  = $this->haksa2080->insert_id();
		
		// echo 'numbering = ' . $number_r."<>".$insert_id;
		// echo '<br />';
		return $number_r."<>".$insert_id;
		
	}
 
	## ȸ������ sms �߼�
	function certSend($case, $cert_number, $cert_method) {
		$retValue = $this->numbering($case, $cert_number, $cert_method); // ������ȣ ����
		$certArr = explode("<>",$retValue);
		
		if (!$certArr[0] || !$certArr[1]) {
			return false;
		} else {
			$send_result = $this->sendSMS("[��Ŀ�� ���л�] [".$certArr[0]."] ȸ������ ������ȣ�Դϴ�. ��Ȯ�� �Է����ּ���.", $cert_number, "15993081");
			
			if (!$send_result) {
				return false;
			}
			
			return $certArr;
		}
	}
    
    ## ȸ�����Ե� ������ �ִ��� Ȯ��
	public function getUserJoinCheck($birth, $handphone_index)
	{
    	$sql = "SELECT `no`, user_id FROM zetyx_member_table WHERE new_birth = '". $birth ."' AND handphone_index = '". $handphone_index ."'";
    	$query = $this->haksa2080->query($sql);
		return $query->row();
	}
    
	## �������� ��ȸ
	public function getUserInfo($member_no, $member_id)
	{
		$query = $this->haksa2080->query("SELECT * FROM zetyx_member_table WHERE `no` = '". $member_no ."' AND user_id = '". $member_id ."'");
		return $query->row();
	}
	
	## �������� ��ȸ
	public function getCouponInfo($coupon_number)
	{
		$query = $this->haksa2080->query("SELECT * FROM auth_list WHERE cupone_number = '". $coupon_number ."' AND user_no IS NULL AND user_id IS NULL");
		return $query->row();
	}
	
	## �������� ���� list ��������
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
	
	## ������ ��밡���� ���� ����Ʈ ��������
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
	
	## �������� ���� �������� (Ajax) limit
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
	
	## ���� ����� ��������Ʈ ��������
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
	
	## ���� ���Ұ� ��������Ʈ (Ajax) limit
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
	
	## ���� ���Ұ� ��������Ʈ cnt
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
	
	## �������� Ȯ��
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
	
	## ���� Ư������ ���� Ȯ��
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
	
	## ���� Ư������ INSERT
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

		return $this->haksa2080->insert('auth_list', $insertData); // �������
	}
	
	
	## ���� ���� ����Ʈ
	function getUserPoint($member_id)
	{
		$sql = "SELECT SUM(`point`) as `use_point` FROM use_point WHERE mem_user_id = '". $member_id ."' AND point_state = '1'";
		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
	
	## ���� ���� ����Ʈ
	function getUserUsedPoint($member_id)
	{
		$sql = "SELECT SUM(`point`) as `used_point` FROM haksa2080.`used_point` WHERE mem_user_id = '". $member_id ."' AND point_state = '1'";
		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
	
	## ���� ���� ����Ʈ ����Ʈ (limit)
	function getUserPointList($member_id, $limit)
	{
		$limit = (!empty($limit)) ? $limit : '0' ;
		$sql = "SELECT mem_user_id, `point`, point_title, LEFT(wdate, 10) as wdate FROM haksa2080.`use_point` WHERE mem_user_id = '". $member_id ."' AND point_state = '1' ORDER BY `no` DESC LIMIT $limit, 5";
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}
	
	## ���� ���� ����Ʈ ����Ʈ
	function getUserPointListAll($member_id)
	{
		$sql = "SELECT mem_user_id, `point`, point_title, LEFT(wdate, 10) as wdate FROM haksa2080.`use_point` WHERE mem_user_id = '". $member_id ."' AND point_state = '1' ORDER BY `no` DESC";
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}
	
	## ���� ��� ����Ʈ ����Ʈ (limit)
	function getUserPointUsedList($member_id, $limit)
	{
		$limit = (!empty($limit)) ? $limit : '0' ;
		$sql = "SELECT mem_user_id, `point`, point_title, LEFT(wdate, 10) as wdate, order_num, lecture_no, point_state FROM haksa2080.`used_point` WHERE mem_user_id = '". $member_id ."' AND point_state IN ('1', '2', '3') ORDER BY `no` DESC LIMIT $limit, 5";
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}
	
	## ���� ��� ����Ʈ ����Ʈ (limit)
	function getUserPointUsedListAll($member_id)
	{
		$sql = "SELECT mem_user_id, `point`, point_title, LEFT(wdate, 10) as wdate FROM haksa2080.`used_point` WHERE mem_user_id = '". $member_id ."' AND point_state IN ('1', '2', '3') ORDER BY `no` DESC";
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}
	
	## �޸�ȸ�� üũ
	public function checkDormant($user_id)
	{
		$sql = "SELECT `no`, user_id FROM rest_zetyx_member_table WHERE user_id = '". $user_id ."'";
		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
	
	## �޸�ȸ�� �Ϲ�ȸ������ ��ȯ
	public function restMemberAgain($no, $member_id)
	{
		## ȸ������
    	$insertSql = "INSERT INTO zetyx_member_table (SELECT * FROM rest_zetyx_member_table WHERE `no` = '". $no ."' AND user_id = '". $member_id ."')";
		$this->haksa2080->query($insertSql);
		
		## �޸�ȸ�� ����
		$this->haksa2080->where("no", $no);
		$this->haksa2080->where("user_id", $member_id);
		return $this->haksa2080->delete("rest_zetyx_member_table");
	}
	
	## �������̵� ���翩��
	public function checkUserId($user_id)
	{
		$query = $this->haksa2080->query("SELECT `no` FROM zetyx_member_table WHERE user_id = '". $user_id ."'");
		$result_data = $query->row_array();
		
		return $result_data;
	}
	
	## ���� password_new check
	public function checkUserPassword($password)
	{
    	$query = $this->haksa2080->query("select SHA2('$password',256) as password_new");
		$result_data = $query->row();
		
		return $result_data;
	}
	
	## ���� ���̵�, ��й�ȣ üũ
	public function checkUserData($user_id, $password)
	{
		$query = $this->haksa2080->query("SELECT * FROM zetyx_member_table WHERE user_id = '". $user_id ."' AND password_new = '". $password ."'");
		return $query->row_array();
	}
	
	## ���� ȸ������ �ڵ��� ����
	public function checkUserHandPhone($handphone, $birth, $cert_type)
	{
		$query = $this->haksa2080->query("SELECT `no`, user_id FROM zetyx_member_table WHERE handphone_index = '". $handphone ."' AND new_birth = '". $birth ."'");
		$result_array = $query->result_array();
		return $result_array;
	}

	## ���� ȸ������ �ڵ��� ����
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
			$result_array = array('result' => true, 'msg' => '������ȣ�� Ȯ�εǾ����ϴ�.', 'update_state' => $updateState);
		} else {
			$result_array = array('result' => false, 'msg' => '�Է��Ͻ� ������ȣ�� ��ġ���� �ʽ��ϴ�. ������ȣ ��߼� �� �ٽ� �õ����ּ���.');
		}
		
		return $result_array;
	}
	
	## ȸ������ �Ϸ�
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
		
		
		$memberInsert = $this->haksa2080->insert('zetyx_member_table', $memberInsert); // ���� insert + 5000�� ����Ʈ ����
		$member_insert_id = $this->haksa2080->insert_id();
		
		// ȸ�� ������ insert
		$memberDetailInsert = array(
			'mem_no' => $member_insert_id
			,'user_id' => $member_detail['user_id']
			,'level_edu' => $member_detail['level_edu'] // �����з�
			,'level_edu_major' => $member_detail['level_edu_major'] // �����з� ������
			,'question' => $member_detail['question'] // ���ǻ���
		
			,'hope_majer' => $member_detail['hope_majer'] // ����а�
			,'hope_majer_sub' => $member_detail['hope_majer_sub'] // ����а� sub
			,'lec_object' => $member_detail['lec_object'] // ��������
			,'user_job' => $member_detail['user_job'] // ��������
			,'join_route' => $member_detail['join_route'] // ���԰��
			,'study_object' => $member_detail['study_object'] // ���Ը���
		);
		
		$this->haksa2080->insert('zetyx_member_table_detail', $memberDetailInsert); // ���������� insert
		
		// ȸ������ ����Ʈ ����
		$memberPointInsert = array(
			'mem_user_id' => $member['user_id']
			,'point_title' => '�ű�ȸ������ �̺�Ʈ ����Ʈ ����'
			,'point' => 5000
			,'point_term' => $member['wdate']
			,'point_state' => '1'
			,'wdate' => $member['wdate']
		);
		
		$this->haksa2080->insert('use_point', $memberPointInsert); // ���� insert + 5000�� ����Ʈ ����
		
		if ($memberInsert) {
			$result_array = array('result' => true, 'msg' => '��Ŀ�����л� ȸ�����ԿϷ�');
		} else {
			$result_array = array('result' => false, 'msg' => 'ȸ�����Կ���!! �����ڿ� �������ּ���.');
		}
		
		return $result_array;
	}
	
	## �������̵� ã��
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
	
	
	## ���� ��й�ȣ ã��
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
	
	## �������� ��������
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
	
	
	## ���� ����������Ʈ
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
	
	## �������� ����
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
			'level_edu' => $member_detail['level_edu'] // �����з�
			,'level_edu_major' => $member_detail['level_edu_major'] // �����з� ������
			,'question' => $member_detail['question'] // ���ǻ���

			,'hope_majer' => $member_detail['hope_majer'] // ����а�
			,'hope_majer_sub' => $member_detail['hope_majer_sub'] // ����а� sub
			,'lec_object' => $member_detail['lec_object'] // ��������
			,'user_job' => $member_detail['user_job'] // ��������
			,'join_route' => $member_detail['join_route'] // ���԰��
			,'study_object' => $member_detail['study_object'] // ���Ը���
		);

		$updateWhere = "mem_no = '". $member['member_no'] ."'";
		$memberUpdateRs = $this->haksa2080->update('zetyx_member_table_detail', $memberDetailUpdate, $updateWhere);
		
		if ($memberUpdateRs) {
			$result_array = array('result' => true, 'msg' => 'ȸ������ �����Ǿ����ϴ�.');
		} else {
			$result_array = array('result' => false, 'msg' => 'ȸ������ ��������!!! �����ڿ� �������ּ���.');
		}
		
		return $result_array;
	}
	
	
	## �α��� �α� ����
//	function save_access_log($id, $name) {
//
//		$data = array(
//			'user_id'       => $id,
//			'name'          => $name,
//			'ip'            => $_SERVER['XFFCLIENTIP'] ? $_SERVER['XFFCLIENTIP'] : $_SERVER['REMOTE_ADDR'],
//			'loginsite'     => '����������',
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
	

    // �޸�ȸ�� �������̵� ���翩��
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


    // (�̸�,�̸���,����) ȸ�� ���翩�� üũ
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

    // ���̹� ���� ���̵� ���翩�� üũ (�̸�,�̸���,����)
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

    //���̹� ���̵�� �α����� �������� üũ
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

    //���̹� ���� �޸���̵� ���翩�� üũ (�̸�,�̸���,����)
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

    
    //���� ���̹� ����ó��
//    public function setSnsUser($name, $email, $gender)
//    {
//        $this->champ->set("sns","N");
//        $this->champ->where("name",$name);
//        $this->champ->where("email",$email);
//        $this->champ->where("sex",$gender);
//        $this->champ->update("zetyx_member_table");
//    }

    //ȸ������ ���� ����ó �ߺ�üũ
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

    // ȸ������ ���� ���� ����
    public function setJoinUser($userData)
    {
        $userInfo = $this->checkUserId($userData['user_id']) ;
        $userDormantInfo = $this->checkDormant($userData['user_id']) ;

        if(!$userInfo && empty($userInfo) && empty($userDormantInfo) && !$userDormantInfo){

            $array_etc = array(
                "������" => $userData['company'] ? $userData['company'] : '' ,
                "�������" => $userData['hopejob'] ? $userData['hopejob'] : '' ,
                "����" => $userData['spec'] ? $userData['spec']  : ''
            );

            $phone = str_replace('-', '', $userData['handphone']);
            $home_tel = str_replace('-', '', $userData['home_tel']);
            $birth = str_replace('-', '', $userData['birth']);
            
            if($phone) $phone = strlen($phone) < 11 ? substr($phone,0,3).'-'.substr($phone,3,3).'-'.substr($phone,6,4) : substr($phone,0,3).'-'.substr($phone,3,4).'-'.substr($phone,7,4);
            if($home_tel) $home_tel = strlen($home_tel) < 10 ? substr($home_tel,0,2).'-'.substr($phone,2,3).'-'.substr($phone,5,3) : substr($phone,0,3).'-'.substr($phone,3,4).'-'.substr($phone,7,3);
            if($birth) $birth = substr($birth,0,4).'-'.substr($birth,4,2).'-'.substr($birth,6,2);
            
            $data = array(
                'group_no'              => $userData['group_no'] ,  // group_no : 4 => 14���̸�ȸ��
                'name'                  => $userData['name'] ,
                'name2'                 => $userData['name2'] ,
                'sex'                   => $userData['sex'],    // ���� (M:��  F:��)
                'birth'                 => $birth,
                'user_id'               => $userData['user_id'],
                'password_date'         => date("Y-m-d H:i:s") ,
                'email'                 => $userData['email'] ,
                'handphone'             => $phone,
                'home_tel'              => $home_tel ,
                'sms'                   => $userData['sms'] ,  //sms ���ŵ��ǿ��� (1:���� 0:�ź�)
                'mailing'               => $userData['mailing']  , //���ϼ��ŵ��ǿ��� (1:���� 0:�ź�)
                'join_path'             => $userData['join_path'] ? $userData['join_path'] : '' , //���԰��
                'privacy_agree'         => $userData['privacy_agree'] , //�������� ó������ ��Ź ���ǿ��� (Y:����)
                'interesting'           => $userData['sns'] != 'N' ? serialize($array_etc) : '' ,
                'new_confirm'           => 1 ,
                'reg_date'              => date("Y-m-d H:i:s") ,
                'new_confirm_date'      => date("Y-m-d H:i:s") ,
                'mem_modification_date' => date("Y-m-d H:i:s") ,
                'last_visit'            => date("Y-m-d H:i:s") ,
                'visit'                 => 1,
                'sns'                   => $userData['sns']     // ���̹���������  (N:���̹�ȸ��)
            );

            $query = $this->champ->insert('zetyx_member_table',$data);

             if($query){
                if($userData['sns'] != 'N'){

                    //������ insert�� ������ȣ ��������
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

            //14���̸� ȸ�� ��ȣ�� �������� ����
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

    //���̵� or ��й�ȣ ã��
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

    //��й�ȣ ����
    public function setPassWord($user_no,$password){

        $tbl_name = "zetyx_member_table";
        //�Ϲ�ȸ�� üũ
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

    //ȸ����������
    public function editUserInfo($userData)
    {
        $array_etc = array(
            "������" => $userData['company'] ? $userData['company'] : '' ,
            "�������" => $userData['hopejob'] ? $userData['hopejob'] : '' ,
            "����" => $userData['spec'] ? $userData['spec']  : ''
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

    //���̹����� ����
    public function removeSnsUser($user_id)
    {
        $this->champ->set("sns","");
        $this->champ->where("user_id",$user_id);
        $this->champ->where("sns","N");
        $this->champ->update("zetyx_member_table");

        return 'ok';
    }

    /*�α��� ������Ʈ*/
    public function login_date_update($user_no) {
        $this->champ->set("last_visit",date("Y-m-d H:i:s"));
        $this->champ->where("no",$user_no);
        $this->champ->update("zetyx_member_table");
    }

    /* ��ȣȭ php ������ �ʹ� ���� */
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

    /* ��ȣȭ php ������ �ʹ� ���� */
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