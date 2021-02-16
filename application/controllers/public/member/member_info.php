<?php

class Member_info extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->model("login/m_member");
	}
	
	public function index()
	{
		$get = (!empty($_GET['memType'])) ? $this->input->get('memType') : '' ;
		$this->memberInfo();
	}
	
	function memberInfo()
	{
		## 필수값 확인
		if ($this->session->userdata('member_no') == '' || $this->session->userdata('member_id') == '') {
			echo "<script>alert('로그인이 필요한 페이지입니다. 로그인후 이용해주세요.'); location.href='/login/main?return_url=' + location.pathname</script>";
		}
		
		// Header user_info, coupon_cnt
		$use_point = $this->m_member->getUserPoint($this->session->userdata('member_id')); // use_point (보유포인트)
		$used_point = $this->m_member->getUserUsedPoint($this->session->userdata('member_id')); // use_point (보유포인트)
		$user_point = ((int)$use_point->use_point - (int)$used_point->used_point);
		$userInfo = $this->m_member->getUserInfo($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		$userCouponData = $this->m_member->getUserCoupon($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		
		$head = array(
			'_css' => 'member'
			,'_js' => 'member'
			,'user_info' => $userInfo
			,'user_point' => $user_point
			,'user_coupon_cnt' => count($userCouponData)
		);
		
		if ($this->input->get('memType') == 'memUpdate') { // 유저정보 수정
			$memberInfo = $this->m_member->memberInfo($this->session->userdata('member_no', true));
			$memberInfo->lec_object = explode(',', $memberInfo->lec_object);
			$memberInfo->join_route = explode(',', $memberInfo->join_route);
			$memberInfo->email = explode('@', $memberInfo->email);
			$view_page = 'member_info';
			
			$data = array(
				'images' => '//gscdn.hackers.co.kr/haksa2080/images'
				,'memberInfo' => $memberInfo
			);
		}
		
		$this->load->view('common/header',$head);
		$this->load->view('member/'. $view_page, $data);
		$this->load->view('common/footer');
	}
	
	function memberUpdate()
	{
		// zetyx_member_table
		$member = array();
		
		$member_info = $this->m_member->memberInfo($this->input->post('member_no'));
		if ($member_info->password_new != hash('sha256', $this->input->post('password_new'))) {
			echo "<script>alert('입력하신 비밀번호는 맞지않습니다.'); location.href='/member/member_info?memType=memUpdate';</script>";
			exit;
		}
		
		$member['member_no'] = $this->input->post('member_no');
		$member['email'] = $this->input->post('email') . '@' . $this->input->post('join_email');
		$member['job'] = $this->input->post('user_job'); // 현재직업
		
		$handphone = $this->input->post('handphone_index');
		if(substr($handphone,0,3) == "010") {
			// -로 구분
			$member['handphone'] = substr($handphone,0,3). "-" . substr($handphone,3,4). "-" . substr($handphone,7,4);
		} else {
			$member['handphone'] = substr($handphone,0,3). "-" . substr($handphone,3,3). "-" . substr($handphone,6,4);
		}
		$member['handphone_index'] = $this->input->post('handphone_index');
		
		$member['birth'] = substr($this->input->post('birth'),0,4) . "-" . substr($this->input->post('birth'),4,2) . "-" . substr($this->input->post('birth'),6,2);
		$member['new_birth'] = $this->input->post('birth');
		
		$member['reg_date'] = time();
		$member['rc_date'] = date('Y-m-d H:i:s');
		$member['rc_ip'] = $_SERVER['REMOTE_ADDR'];
		$member['rc_cnt'] = $this->input->post('rc_cnt');
		
		$member['uno_new'] = $this->input->post('postCode'); // 우편번호
		$member['home_address'] = $this->input->post('roadAddress_01'); // 주소정보
		$member['hobby'] = $this->input->post('roadAddress_02'); // 주소상세정보
		
		// zetyx_member_table_detail
		$member_detail = array();
		
		$member_detail['level_edu'] = (int)$this->input->post('level_edu'); // 최종학력
		$member_detail['level_edu_major'] = $this->input->post('level_edu_major'); // 최종학력 상세정보
		
		$member_detail['question'] = $this->input->post('question'); // 문의사항
		$member_detail['hope_majer'] = $this->input->post('hope_majer'); // 희망학과
		$member_detail['hope_majer_sub'] = $this->input->post('hope_majer_sub'); // 희망학과 sub
		$member_detail['lec_object'] = implode(',', $this->input->post('lec_object'));
		$member_detail['user_job'] = $this->input->post('user_job'); // 현재직업
		$member_detail['join_route'] = implode(',', $this->input->post('join_route')); // 가입경로
		$member_detail['study_object'] = $this->input->post('study_object'); // 학습목적
		$member_detail['sms'] = $this->input->post('evt_agree'); // 이메일, 문자 수신동의
		
		$memberUpdate = $this->m_member->memberInfoUpdateQuery($member, $member_detail);
		if ($memberUpdate['result'] == 1) {
			echo "<script>alert('회원정보 수정되었습니다.'); location.href='/';</script>";
		} else {
			echo "<script>alert('회원정보 수정실패!!! 관리자에 문의해주세요.'); history.back(-1);</script>";
		}
		exit;
	}
	
	
}