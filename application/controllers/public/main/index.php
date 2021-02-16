<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Index extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model("main/m_contents");
		$this->load->model("login/m_member");
        // $this->load->model("lecture/m_lecture");
    }
    
    /**
     * 메인 페이지
     */
    public function index()
    {
		// 메인 롤링배너
		$main_slide_banner = $this->m_contents->getMainBanner();
	
		// 메인 하단배너
		$main_slide_bottom_banner = $this->m_contents->getMainBottomBanner();
	
		// 메인 스타교수
		$main_star_teacher_list = $this->m_contents->getMainStarTeacher();
	
		// 메인 시험일정
		$main_calender = $this->m_contents->getMainCalender();
		$main_calender->exam_day = date_of_week($main_calender->exam_date); // 시험요일
	
		$nDate = date("Y-m-d",time()); // 오늘 날짜
		$change_valDate = date('Y-m-d', strtotime($main_calender->exam_date)); // 데이터상 날짜
		$main_calender->exam_dday = intval((strtotime($change_valDate) - strtotime($nDate)) / 86400); // 나머지 날짜값이 나옵니다.
		
		// 메인 공지사항
		$main_notice = $this->m_contents->getMainNotice();
		
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
			,'_css' => 'main'
		);
		
        $data = array(
			'images' => 'https://gscdn.hackers.co.kr/haksa2080/images',
			'main_slide_banner' => $main_slide_banner,
			'main_slide_bottom_banner' => $main_slide_bottom_banner,
			'main_star_teacher' => $main_star_teacher_list,
			'main_calender' => $main_calender,
			'main_notice' => $main_notice
        );
        
        $this->load->view('common/header', $head);
        $this->load->view('main/main',$data);
        $this->load->view('common/footer');
    }
}