<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class View extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->model("login/m_member");
		$this->load->model("notice/m_notice");
		$this->load->model("main/m_contents");
	}
	
	public function index()
	{
		$checkPage = (!empty($_GET['viewType'])) ? $_GET['viewType'] : 'consulting';
		$checkNo = (!empty($_GET['notice_no'])) ? $_GET['notice_no'] : '';
		
		$this->noticeView($checkPage, $checkNo);
	}
	
	public function noticeView($getType, $checkNo)
	{
		// Header user_info, coupon_cnt
		$use_point = $this->m_member->getUserPoint($this->session->userdata('member_id')); // use_point (보유포인트)
		$used_point = $this->m_member->getUserUsedPoint($this->session->userdata('member_id')); // use_point (보유포인트)
		$user_point = ((int)$use_point->use_point - (int)$used_point->used_point);
		$userInfo = $this->m_member->getUserInfo($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		$userCouponData = $this->m_member->getUserCoupon($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		
		$header = array(
			'_css' => 'customer'
			,'_js' => 'customer'
			,'user_info' => $userInfo
			,'user_point' => $user_point
			,'user_coupon_cnt' => count($userCouponData)
		);
		
		if ($getType == 'notice') {
			// 공지사항 상세보기 (notice)
			$noticeView = $this->m_contents->getNoticeView($checkNo);
			
			$data = array(
				'images' => 'https://gscdn.hackers.co.kr/haksa2080/images'
				,'notice_view' => $noticeView
			);
		}
		
		$this->load->view('common/header', $header);
		$this->load->view('notice/' . $getType . '_view', $data);
		$this->load->view('common/footer');
	}
	
}