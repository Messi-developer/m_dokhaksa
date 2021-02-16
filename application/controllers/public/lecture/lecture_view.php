<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lecture_view extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->model("lecture/m_lecture");
		$this->load->model("login/m_member");
	}
	
	public function index()
	{
		$this->lectureView();
	}
	
	public function lectureView()
	{
		// Header user_info, coupon_cnt
		$use_point = $this->m_member->getUserPoint($this->session->userdata('member_id')); // use_point (보유포인트)
		$used_point = $this->m_member->getUserUsedPoint($this->session->userdata('member_id')); // use_point (보유포인트)
		$user_point = ((int)$use_point->use_point - (int)$used_point->used_point);
		$userInfo = $this->m_member->getUserInfo($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		$userCouponData = $this->m_member->getUserCoupon($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		
		$lecture_list = $this->m_lecture->getLectureView($this->input->get('lec_no')); // 강좌 정보
		$lecture_list->lecture_kang_info = $this->m_lecture->getLectureKang($lecture_list->lec_no); // 1번강의 호출
		$lecture_kang = $this->m_lecture->getLectureKangList($lecture_list->lec_no); // 1번강의 호출
		
		// 강의코드 교재정보확인
		$lecture_list->lecToBookCheck = $this->m_lecture->getLectureLecToBookCnt($lecture_list->lec_no); // LecToBook 설정값있는지 확인
		$lecture_list->book_info = $this->m_lecture->getLectureBook($lecture_list->lecToBookCheck->book_code); // 강의 bookcode 값이있는지 확인
		
		
		// 구매하기시 교재리스트 불러오기
		// $getLectureBookInfoList = $this->m_lecture->getLectureBookInfo($getLectureLecToBookCnt->book_code);
		
		// 강의별 level
		if ($lecture_list->lec_level1 != '' || $lecture_list->lec_level2 != '' || $lecture_list->lec_level3 != '' || $lecture_list->lec_level4 != '') {
			if (!empty($lecture_list->lec_level1)) {
				$lecture_list->lec_level = '1';
			} else if (!empty($lecture_list->lec_level2)) {
				$lecture_list->lec_level = '2';
			} else if (!empty($lecture_list->lec_level3)) {
				$lecture_list->lec_level = '3';
			} else if (!empty($lecture_list->lec_level4)) {
				$lecture_list->lec_level = '4';
			}
		}
		
		// 교재 타입설정
		if ($lecture_list->book_type != '') {
			switch($lecture_list->book_type) {
				case '1' :
					$lecture_list->lec_book_type = '무료제공';
					break;
				case '2' :
					$lecture_list->lec_book_type = '서점구매';
					break;
				case '3' :
					$lecture_list->lec_book_type = '교재비 포함';
					break;
				case '4' :
					$lecture_list->lec_book_type = '교재비 별도';
					break;
				case '5' :
					$lecture_list->lec_book_type = '미선택';
					break;
			}
		}
		
		$lectureMem_point = number_format(($lecture_list->lec_price * 5) / 100); // 구매시 지급될 포인트 금액 (5% 지급) == 판매가 * 5% / 100
		$lectureMem_percent = ceil((($lecture_list->lec_real_price - $lecture_list->lec_price) / $lecture_list->lec_real_price) * 100); // ((원가 - 판매가) / 원가) * 100
		
		$head = array(
			'_css' => 'lecture'
			,'_js' => 'lecture'
			,'user_info' => $userInfo
			,'user_point' => $user_point
			,'user_coupon_cnt' => count($userCouponData)
		);
		
		$data = array(
			'images' => '//gscdn.hackers.co.kr/haksa2080/images'
			,'lecture_list' => $lecture_list
			// ,'lecture_level' => $lecture_level
			,'lectureMem_point' => $lectureMem_point
			,'lectureMem_percent' => $lectureMem_percent
			// ,'lectureBookInfo' => $lectureBookInfo
			,'lecture_kang' => $lecture_kang
			// ,'lectureLecToBookCnt' => $getLectureLecToBookCnt
			// ,'lectureBookInfoList' => $getLectureBookInfoList
		);
		
		$this->load->view('common/header', $head);
		$this->load->view('lecture/lecture_view', $data);
        $this->load->view('common/allimpopup');
		$this->load->view('common/footer');
	}
	
}