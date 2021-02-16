<?php
class Search extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model("login/m_member");
	}
	
	public function index()
	{
		$searchType = (!empty($_GET['searchType'])) ? $_GET['searchType'] : '' ;
		$search = (!empty($_GET['search'])) ? $_GET['search'] : '' ;
		$this->member_search($searchType, $search);
	}
	
	## 유저 아이디찾기
	public function member_search($searchType, $search)
	{
		// Header user_info, coupon_cnt
		$use_point = $this->m_member->getUserPoint($this->session->userdata('member_id')); // use_point (보유포인트)
		$used_point = $this->m_member->getUserUsedPoint($this->session->userdata('member_id')); // use_point (보유포인트)
		$user_point = ((int)$use_point->use_point - (int)$used_point->used_point);
		$userInfo = $this->m_member->getUserInfo($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		$userCouponData = $this->m_member->getUserCoupon($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		
		$head = array(
			'title' => '해커스독학사 유저아이디찾기',
			'js' => array(
				'js/member/member.js'
			),
			'pageType' => 'member'
			,'user_info' => $userInfo
			,'user_point' => $user_point
			,'user_coupon_cnt' => count($userCouponData)
		);
		
		$data = array(
			'images' => 'https://gscdn.hackers.co.kr/haksa2080/images',
		);
		
		$this->load->view('common/header',$head);
		$this->load->view('member/search_' . $searchType . '_' . $search, $data);
		$this->load->view('common/footer');
	}
	
	
}