<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
		$this->load->model("lecture/m_lecture");
		$this->load->model("login/m_member");
	}
	
	public function index()
	{
		$this->lectureListContent();
	}
	
	public function lectureListContent()
	{
		// Header user_info, coupon_cnt
		$use_point = $this->m_member->getUserPoint($this->session->userdata('member_id')); // use_point (보유포인트)
		$used_point = $this->m_member->getUserUsedPoint($this->session->userdata('member_id')); // use_point (보유포인트)
		$user_point = ((int)$use_point->use_point - (int)$used_point->used_point);
		$userInfo = $this->m_member->getUserInfo($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		$userCouponData = $this->m_member->getUserCoupon($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		
		// 강의/교재신청 메인배너
		$lecture_main_banner = $this->m_lecture->getLectureBanner();
		
		// 강의리스트
		$getPost = (!empty($_POST)) ? $_POST : '' ;
		
		if (!empty($_POST['page_cnt'])) {
			// 검색조건이 있을시
			$lecture_plus_list = $this->m_lecture->getLectureList($getPost);
			
			foreach($lecture_plus_list as $lecture_key => $lecture_item) {
				if (!empty($lecture_item['lec_level1'])) {
					$lecture_plus_list[$lecture_key]['lec_level'] = '1';
				} else if (!empty($lecture_item['lec_level2'])) {
					$lecture_plus_list[$lecture_key]['lec_level'] = '2';
				} else if (!empty($lecture_item['lec_level3'])) {
					$lecture_plus_list[$lecture_key]['lec_level'] = '3';
				} else if (!empty($lecture_item['lec_level4'])) {
					$lecture_plus_list[$lecture_key]['lec_level'] = '4';
				}
				
				$lecture_plus_list[$lecture_key]['lec_point'] = ((int)$lecture_item['lec_real_price'] * 0.05); // 구매시 지급될 포인트 금액 0.05
				$lecture_plus_list[$lecture_key]['lec_percent'] = ceil((((int)$lecture_item['lec_price'] - (int)$lecture_item['lec_real_price']) / $lecture_item['lec_price']) * 100); // ((원가 - 판매가) / 원가) * 100
				
				// New icon 생성
				$changeDate = str_replace(':', '-', $lecture_item['wdate']);
				$changeDate_rs = strtotime($changeDate . "+1 month"); // 2020-01-01
				$changeDay = date('Y-m-d H:i:s', $changeDate_rs); // 날짜변환
				$lecture_plus_list[$lecture_key]['new_icon'] = ($changeDay >= date('Y-m-d H:i:s')) ? 'NEW' : '';
				
				$lecture_plus_list[$lecture_key]['etc'] = iconv("CP949","UTF-8", $lecture_item['etc']);
				$lecture_plus_list[$lecture_key]['lec_name'] = iconv("CP949","UTF-8", $lecture_item['lec_name']);
				$lecture_plus_list[$lecture_key]['major_name'] = iconv("CP949","UTF-8", $lecture_item['major_name']);
				$lecture_plus_list[$lecture_key]['t_name'] = iconv("CP949","UTF-8", $lecture_item['t_name']);
			}
			
			if (!empty($lecture_plus_list)) {
				echo json_encode(array('result' => true, 'lecture_list' => $lecture_plus_list, 'member_id' => $this->session->userdata('member_id')));
			} else {
				echo json_encode(array('result' => false, 'msg' => iconv('EUC-KR', 'UTF-8', '불러오기 실패!!! 관리자에 문의해주세요.')));
			}
			exit;
			
		} else {
			// 검색조건이 없을시
			$lecture_list = $this->m_lecture->getLectureList($getPost);
			$lecture_list_cnt = $this->m_lecture->getLectureListCnt($getPost);
			$lecture_teahcer_list = $this->m_lecture->getTeacherName($getPost);
		}
		
		// 강의별 level
		foreach($lecture_list as $lecture_key => $lecture_item) {
			if (!empty($lecture_item['lec_level1'])) {
				$lecture_list[$lecture_key]['lec_level'] = '1';
			} else if (!empty($lecture_item['lec_level2'])) {
				$lecture_list[$lecture_key]['lec_level'] = '2';
			} else if (!empty($lecture_item['lec_level3'])) {
				$lecture_list[$lecture_key]['lec_level'] = '3';
			} else if (!empty($lecture_item['lec_level4'])) {
				$lecture_list[$lecture_key]['lec_level'] = '4';
			}
			
			$lecture_list[$lecture_key]['lec_point'] = ((int)$lecture_item['lec_real_price'] * 0.05); // 구매시 지급될 포인트 금액 0.05
			$lecture_list[$lecture_key]['lec_percent'] = ceil((((int)$lecture_item['lec_price'] - (int)$lecture_item['lec_real_price']) / $lecture_item['lec_price']) * 100); // ((원가 - 판매가) / 원가) * 100
			
			// New icon 생성
			$changeDate = str_replace(':', '-', $lecture_item['wdate']);
			$changeDate_rs = strtotime($changeDate . "+1 month"); // 2020-01-01
			$changeDay = date('Y-m-d H:i:s', $changeDate_rs); // 날짜변환
			$lecture_list[$lecture_key]['new_icon'] = ($changeDay >= date('Y-m-d H:i:s')) ? 'NEW' : '';
		}
		
		$head = array(
			'_css' => 'lecture'
			,'_js' => 'lecture'
			,'user_info' => $userInfo
			,'user_point' => $user_point
			,'user_coupon_cnt' => count($userCouponData)
		);
		
		$data = array(
			'images' => 'https://gscdn.hackers.co.kr/haksa2080/images'
			,'lecture_main_banner' => $lecture_main_banner
			,'lecture_list' => $lecture_list
			,'lecture_list_cnt' => $lecture_list_cnt
			// ,'lecture_level' => $lecture_level
			// ,'lectureMem_point' => $lectureMem_point
			// ,'lectureMem_percent' => $lectureMem_percent
			,'lecture_teahcer_list' => $lecture_teahcer_list
		);
		
		$this->load->view('common/header', $head);
		$this->load->view('lecture/lecture_list', $data);
        $this->load->view('common/allimpopup');
		$this->load->view('common/footer');
	}
	
}