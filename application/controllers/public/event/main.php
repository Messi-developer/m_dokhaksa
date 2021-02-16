<?php
class Main extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model("login/m_member");
	}
	
	public function index()
	{
		$evt_code = ($this->input->get('evt_code') != '') ? $this->input->get('evt_code') : date('Ym') ;
		$evt_key = ($this->input->get('evt_content') != '') ? $this->input->get('evt_content') : 'event' ;
		
		if (empty($evt_key)) {
			echo "<script>alert('이벤트 고유키를 입력해주세요.'); location.href='/';</script>";
			exit;
		}
		
		$this->eventContents($evt_code, $evt_key);
	}
	
	public function eventContents($evt_code, $evt_key)
	{
		// Header user_info, coupon_cnt
		$use_point = $this->m_member->getUserPoint($this->session->userdata('member_id')); // use_point (보유포인트)
		$used_point = $this->m_member->getUserUsedPoint($this->session->userdata('member_id')); // use_point (보유포인트)
		$user_point = ((int)$use_point->use_point - (int)$used_point->used_point);
		$userInfo = $this->m_member->getUserInfo($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		$userCouponData = $this->m_member->getUserCoupon($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		
		$head = array(
			'user_info' => $userInfo
			,'user_point' => $user_point
			,'user_coupon_cnt' => count($userCouponData)
			,'event_common_js' => 'event/event_common' // 공통 event js
			,'_css' => 'event/' . $evt_code . '/' .$evt_key
			,'_js' => 'event/' . $evt_code . '/' . $evt_key
		);
		
		$this->load->view('common/header',$head);
		$this->load->view('common/allimpopup');
		$this->load->view('event/' . $evt_code . '/' . $evt_key);
		$this->load->view('common/footer');
	}
}