<?php

class Main extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model("login/m_member");
		$this->load->model("main/m_contents");
	}

    ## 회원가입
    public function index()
    {
		$join_step = (!empty($_GET['step'])) ? $_GET['step'] : '01'; // 회원가입 페이지 전환
		$join_page_type = ($_GET['pageType'] == 'ut') ? 'join_ut_step_' : 'join_step_' ;
        $this->member_join($join_page_type, $join_step);
    }

    ## 회원가입 step_01
    public function member_join($join_page_type, $join_step)
    {
		// Header user_info, coupon_cnt
		$use_point = $this->m_member->getUserPoint($this->session->userdata('member_id')); // use_point (보유포인트)
		$used_point = $this->m_member->getUserUsedPoint($this->session->userdata('member_id')); // use_point (보유포인트)
		$user_point = ((int)$use_point->use_point - (int)$used_point->used_point);
		$userInfo = $this->m_member->getUserInfo($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		$userCouponData = $this->m_member->getUserCoupon($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
  
		$head = array(
			'title' => '해커스독학사 회원가입'
			,'_css' => 'member'
			// ,'_js' => 'member'
			,'user_info' => $userInfo
			,'user_point' => $user_point
			,'user_coupon_cnt' => count($userCouponData)
		);
		
		$data = array(
			'images' => 'https://gscdn.hackers.co.kr/haksa2080/images'
			,'ut_session' => $_SESSION // 편입 유저정보
		);
    	
        $this->load->view('common/header',$head);
        $this->load->view('member/'. $join_page_type . $join_step, $data);
        $this->load->view('common/footer');
    }

    ## 회원가입 step_02 (중복아이디 확인)
    public function memberIdCheck()
	{
		$checkUserId = $this->m_member->checkUserId($_POST['member_id']);
		
		if (count($checkUserId) > 0) {
			$json = array('result' => false , 'msg' => '이미 사용중인 아이디입니다.');
		} else {
			$json = array('result' => true , 'msg' => '사용 가능한 아이디입니다.');
		}
		
		convertText('euc-kr','utf-8',$json);
		echo json_encode($json);
		exit;
	}
 
	## 회원가입 step_02 (휴대폰인증)
	public function memberHandPhone()
	{
		if ($this->input->post('handphone') == '' || $this->input->post('user_birth') == '' || $this->input->post('cert_type') == '') {
			$json = array('result' => false , 'msg' => '필수값이 없습니다.');
			
			convertText('euc-kr','utf-8',$json);
			echo json_encode($json);
			exit;
		}
		
		// 회원가입된 번호있는지 확인
		$check_handphone = $this->m_member->checkUserHandPhone($this->input->post('handphone', true), $this->input->post('user_birth', true), $this->input->post('cert_type', true));
		if (count($check_handphone) > 0) {
			$json = array('result' => false , 'msg' => '이미 등록된 번호입니다.');
			
			convertText('euc-kr','utf-8',$json);
			echo json_encode($json);
			exit;
		}
		
		// 문자발송 로직 핸드폰번호 변경
		if(substr($this->input->post('handphone'),0,3) == "010") {
			// -로 구분
			$handphone = substr($this->input->post('handphone'),0,3). "-" . substr($this->input->post('handphone'),3,4). "-" . substr($this->input->post('handphone'),7,4);
		} else {
			$handphone = substr($this->input->post('handphone'),0,3). "-" . substr($this->input->post('handphone'),3,3). "-" . substr($this->input->post('handphone'),6,4);
		}
		
		// 문자발송 로직
		$smsSend = $this->m_member->certSend("join", $handphone, $this->input->post('cert_type', true));
		
		if(!$smsSend){
			$json = array('result' => false , 'msg' => '이미 등록된 번호입니다.');
		}else{
			$json = array('result' => true , 'msg' => '인증 번호가 발송되었습니다. 인증인증', 'send_code' => $smsSend[1]); // $smsSend[1] == insert_id
		}
		
		convertText('euc-kr','utf-8',$json);
		echo json_encode($json);
		exit;
	}
	
	## 회원정보수정 (휴대폰인증)
	public function memberInfoHandPhone()
	{
		if ($this->input->post('handphone') == '' || $this->input->post('user_birth') == '' || $this->input->post('cert_type') == '') {
			$json = array('result' => false , 'msg' => '필수값이 없습니다.');
			
			convertText('euc-kr','utf-8',$json);
			echo json_encode($json);
			exit;
		}
		
		// 문자발송 로직 핸드폰번호 변경
		if(substr($this->input->post('handphone'),0,3) == "010") {
			// -로 구분
			$handphone = substr($this->input->post('handphone'),0,3). "-" . substr($this->input->post('handphone'),3,4). "-" . substr($this->input->post('handphone'),7,4);
		} else {
			$handphone = substr($this->input->post('handphone'),0,3). "-" . substr($this->input->post('handphone'),3,3). "-" . substr($this->input->post('handphone'),6,4);
		}
		
		// 문자발송 로직
		$smsSend = $this->m_member->certSend("join", $handphone, $this->input->post('cert_type', true));
		
		if(!$smsSend){
			$json = array('result' => false , 'msg' => '이미 등록된 번호입니다.');
		}else{
			$json = array('result' => true , 'msg' => '인증 번호가 발송되었습니다.', 'send_code' => $smsSend[1]); // $smsSend[1] == insert_id
		}
		
		convertText('euc-kr','utf-8',$json);
		echo json_encode($json);
		exit;
	}
	
	## 회원가입 step_02 (휴대폰인증 번호 확인)
	public function memberCertification()
	{
		$certificationt = $this->m_member->checkCertification($_POST['insert_id'], $_POST['send_code']);
		
		echo json_encode(array('result' => $certificationt['result'], 'msg' => iconv('CP949', 'UTF-8', $certificationt['msg'])));
		exit;
	}
	
	## 회원가입 step_02 (추천인 조회)
	public function memberRecommender()
	{
		$checkUserId = $this->m_member->checkUserId($this->input->post('user_id'));
		
		if (count($checkUserId) > 0) {
			$json = array('result' => true , 'msg' => '확인되었습니다');
		} else {
			$json = array('result' => false , 'msg' => '해당 ID가 존재하지 않습니다.');
		}
		
		convertText('euc-kr','utf-8',$json);
		echo json_encode($json);
		exit;
	}
	
	## 회원가입 완료
	public function memberJoinFinsh()
	{
		
		$userJoinCheck = $this->m_member->getUserJoinCheck($this->input->post('birth'), $this->input->post('handphone_index'));
		if (!empty($userJoinCheck)) {
			echo "<script>alert('이미 회원가입된 정보가 있습니다. 관리자에 문의헤주세요.'); location.reload();</script>";
			exit;
		}
		
		// zetyx_member_table
		$member = array();
		
		$member['group_no'] = (!empty($_POST['group_no'])) ? $this->input-post('group_no') : 1 ;
		$member['user_id'] = $this->input->post('user_id');
		$member['password_new'] = hash('sha256', $this->input->post('password_new'));
		$member['name'] = $this->input->post('name');
		$member['email'] = $this->input->post('email');
		$member['join_email'] = $this->input->post('join_email');
		$member['job'] = $this->input->post('user_job'); // 현재직업
		
		$handphone = $this->input->post('handphone_index');
		
		if(substr($handphone,0,3) == "010") {
			// -로 구분
			$member['handphone'] = substr($handphone,0,3). "-" . substr($handphone,3,4). "-" . substr($handphone,7,4);
		} else {
			$member['handphone'] = substr($handphone,0,3). "-" . substr($handphone,3,3). "-" . substr($handphone,6,4);
		}
		$member['handphone_index'] = $handphone;
		
		$member['handphone_cert'] = ($this->input->post('agree') == 'on') ? 'y' : 'n' ;
		$member['mailing'] = ($this->input->post('private_agree') == 'on') ? 'y' : '' ;
		
		$member['birth'] = substr($this->input->post('birth'),0,4) . "-" . substr($this->input->post('birth'),4,2) . "-" . substr($this->input->post('birth'),6,2);
		$member['new_birth'] = $this->input->post('birth');
		
		$member['reg_date'] = time();
		$member['rc_date'] = date('Y-m-d H:i:s');
		$member['rc_ip'] = $_SERVER['REMOTE_ADDR'];
		
		$member['sms'] = ($this->input->post('agree') == 'on') ? '1' : '0' ;
		$member['sex'] = $this->input->post('sex');
		
		$member['handphone_index'] = $this->input->post('handphone_index');
		$member['uno_new'] = $this->input->post('uno_new'); // 우편번호
		$member['home_address'] = $this->input->post('home_address'); // 주소정보
		$member['hobby'] = $this->input->post('tail_address'); // 주소상세정보
		
		$member['join_channel'] = 'm'; // 회원가입경로
		$member['email_agree_site'] = 'haksa2080'; // 이메일수신동의사이트
		$member['wdate'] = date('Y-m-d H:i:s');
		
		// zetyx_member_table_detail
		$member_detail = array();
		
		$member_detail['user_id'] = $this->input->post('user_id');
		$member_detail['level_edu'] = ($this->input->post('level_edu') != '') ? (int)$this->input->post('level_edu') : '' ; // 최종학력
		$member_detail['level_edu_major'] = ($this->input->post('level_edu_major') != '') ? $this->input->post('level_edu_major') : ''; // 최종학력 상세정보
		
		$member_detail['question'] = ($this->input->post('question') != '') ? $this->input->post('question') : '미입력'; // 문의사항
		$member_detail['hope_majer'] = ($this->input->post('hope_majer') != '') ? $this->input->post('hope_majer') : '7' ; // 희망학과
		$member_detail['hope_majer_sub'] = ($this->input->post('hope_majer_sub') != '') ? $this->input->post('hope_majer_sub') : '' ; // 희망학과 sub
		$member_detail['lec_object'] = ($this->input->post('lec_object') != '') ? implode(',', $this->input->post('lec_object')) : '4';
		$member_detail['user_job'] = ($this->input->post('user_job') != '') ? $this->input->post('user_job') : '1'; // 현재직업
		$member_detail['join_route'] = ($this->input->post('join_route') != '') ? implode(',', $this->input->post('join_route')) : '7'; // 가입경로
		$member_detail['study_object'] = ($this->input->post('study_object') != '') ? $this->input->post('study_object') : '미입력' ; // 학습목적
		
		$this->m_member->memberJoinFinsh($member, $member_detail);
		
		echo "<script>location.href='/member/main?step=03'</script>";
		exit;
		
	}
	
	
	########## 회원가입 end ##########
	
	
	## 유저정보 찾기
	public function searchMemberId()
	{
		if ($_POST['searchType'] == 'search_tel') {
			$searchUser = $this->m_member->searchMemberId($this->input->post('searchType'), iconv('UTF-8', 'CP949', $this->input->post('user_name', true)), $this->input->post('birth'), $this->input->post('handphone_index'));
		} else {
			$search_email = $this->input->post('email') . '@' . $this->input->post('join_email');
			$searchUser = $this->m_member->searchMemberId($this->input->post('searchType'), iconv('UTF-8', 'CP949', $this->input->post('user_name', true)), $this->input->post('birth'), $search_email);
		}
		
		
		if (count($searchUser) > 0) {
			$searchUser->name = iconv('EUC-KR', 'UTF-8', $searchUser->name);
			echo json_encode(array('result' => true, 'msg' => iconv('EUC-KR', 'UTF-8', '회원아이디 찾기 성공!!'), 'search_data' => $searchUser));
		} else {
			echo json_encode(array('result' => false, 'msg' => iconv('EUC-KR', 'UTF-8', '회원아이디 찾기 실패!!')));
		}
		exit;
	}
	
	## 유저 비밀번호 찾기 (휴대폰인증)
	public function memberHandPhoneSearch()
	{
		if ($this->input->post('searchType') == 'search_tel') {
			$memberInfo = $this->m_member->searchMemberPassword($this->input->post('searchType'), $this->input->post('member_id'), iconv('UTF-8', 'CP949', $this->input->post('member_name', true)), $this->input->post('member_birth'), $this->input->post('handphone'));
			if (empty($memberInfo)) {
				echo json_encode(array('result' => false, 'msg' => iconv('CP949', 'UTF-8', '입력하신 유저정보가 없습니다. 확인해주세요.')));
				exit;
			}
		} else {
			$search_email = $this->input->post('email') . '@' . $this->input->post('join_email');
			$memberInfo = $this->m_member->searchMemberPassword($this->input->post('searchType'), $this->input->post('member_id'), iconv('UTF-8', 'CP949', $this->input->post('member_name', true)), $this->input->post('member_birth'), $search_email);
			if (empty($memberInfo)) {
				echo json_encode(array('result' => false, 'msg' => iconv('CP949', 'UTF-8', '입력하신 유저정보가 없습니다. 확인해주세요.')));
				exit;
			}
		}
		
		// 회원가입된 번호있는지 확인
		$check_handphone = $this->m_member->checkUserHandPhone($this->input->post('handphone', true), $this->input->post('user_birth', true), $this->input->post('cert_type', true));
		if (count($check_handphone) > 0) {
			echo json_encode(array('result' => false, 'msg' => iconv('CP949', 'utf-8', '이미 등록된 번호입니다.')));
			exit;
		}
		
		// 문자발송 로직 핸드폰번호 변경
		if(substr($this->input->post('handphone'),0,3) == "010") {
			// -로 구분
			$handphone = substr($this->input->post('handphone'),0,3). "-" . substr($this->input->post('handphone'),3,4). "-" . substr($this->input->post('handphone'),7,4);
		} else {
			$handphone = substr($this->input->post('handphone'),0,3). "-" . substr($this->input->post('handphone'),3,3). "-" . substr($this->input->post('handphone'),6,4);
		}
		
		// 문자발송 로직
		$smsSend = $this->m_member->certSend("join", $handphone, $_POST['cert_type']);
		
		if(!$smsSend){
			$json = array('result' => false , 'msg' => '이미 등록되어있는 번호입니다.');
		}else{
			$json = array('result' => true , 'msg' => '인증 번호가 발송되었습니다.', 'send_code' => $smsSend[1]); // $smsSend[1] == insert_id
		}
		
		convertText('euc-kr','utf-8',$json);
		echo json_encode($json);
		exit;
	}
	
	
	## 유저 비밀번호 찾기
	public function memberSearchInfo()
	{
		if ($this->input->post('searchType') == 'search_tel') {
			$certificationt = $this->m_member->checkCertification($this->input->post('insert_id'), $this->input->post('send_code')); // 인증번호 확인
			$memberInfo = $this->m_member->searchMemberPassword($this->input->post('searchType'), $this->input->post('member_id'), iconv('UTF-8', 'EUC-KR', $this->input->post('member_name', true)), $this->input->post('member_birth'), $this->input->post('handphone'));
			$memberInfo->name = iconv('EUC-KR', 'UTF-8', $memberInfo->name);
			
			if (!empty($memberInfo)) {
				echo json_encode(array('result' => $certificationt['result'], 'msg' => iconv('CP949', 'UTF-8', $certificationt['msg']), 'member_info' => $memberInfo));
			} else {
				echo json_encode(array('result' => false, 'msg' => iconv('CP949', 'UTF-8', '입력하신 유저정보가 존재하지않습니다. 확인해주세요.')));
			}
		} else {
			$certificationt = $this->m_member->checkCertification($this->input->post('insert_id'), $this->input->post('send_code')); // 인증번호 확인
			$search_email = $this->input->post('email') . '@' . $this->input->post('join_email');
			$memberInfo = $this->m_member->searchMemberPassword($this->input->post('searchType'), $this->input->post('member_id'), iconv('UTF-8', 'EUC-KR', $this->input->post('member_name', true)), $this->input->post('member_birth'), $search_email);
			$memberInfo->name = iconv('EUC-KR', 'UTF-8', $memberInfo->name);
			
			if (!empty($memberInfo)) {
				echo json_encode(array('result' => $certificationt['result'], 'msg' => iconv('CP949', 'UTF-8', $certificationt['msg']), 'member_info' => $memberInfo));
			} else {
				echo json_encode(array('result' => false, 'msg' => iconv('CP949', 'UTF-8', '입력하신 유저정보가 존재하지않습니다. 확인해주세요.')));
			}
		}
		
		exit;
	}
	
	## 유저 정보 업데이트
	public function memberInfoUpdate()
	{
		if ($this->input->post('update_type') == 'password') {
			
			if (empty($_POST['memberNo'])) {
				echo json_encode(array('result' => false, 'msg' => iconv('CP949', 'UTF-8', '필수값이 없습니다. 휴대폰인증부터 다시 시도해주세요.')));
				exit;
			}
			
			$password_new = hash('sha256', $this->input->post('password_new'));
			$this->m_member->memberInfoUpdate($this->input->post('update_type'), $this->input->post('memberNo'), $password_new);
			
			echo json_encode(array('result' => true, 'msg' => iconv('CP949', 'UTF-8', '비밀번호 변경되었습니다. 로그인 시도해주세요.')));
			exit;
		}
		
	}
	
}