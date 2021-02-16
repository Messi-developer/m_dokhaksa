<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->model("login/m_member");
		$this->load->model("lecture/m_lecture");
	}
	
	public function index()
	{
		$this->myclassContent();
	}
	
	public function myclassContent()
	{
		if ($this->session->userdata('member_id') == '') {
			echo "<script>alert('로그인이 필요한 페이지 입니다. 로그인후 이용해주세요.'); location.href='/login/main?return_url=/myclass/main';</script>";
		}
		
		// Header user_info, coupon_cnt
		$use_point = $this->m_member->getUserPoint($this->session->userdata('member_id')); // use_point (보유포인트)
		$used_point = $this->m_member->getUserUsedPoint($this->session->userdata('member_id')); // use_point (보유포인트)
		$user_point = ((int)$use_point->use_point - (int)$used_point->used_point);
		$userInfo = $this->m_member->getUserInfo($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		$userCouponData = $this->m_member->getUserCoupon($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		
		$nowDate = date("Y:m:d h:i:s", time());  // 오늘 날짜 시간 가져옴.
		
		## 부모강의추출
		$getLectureParent = $this->m_lecture->getMyclassLectureParent($this->input->get('page_type'), $this->session->userdata('member_id'), $this->session->userdata('member_no'));
		
		## 자식강의추출
		$getLectureList = array();
		foreach($getLectureParent as $parent_key => $parent_item) {
			
			$enddate = $parent_item['end_date'] . ' 00:00:00'; // 강의종료일
			$day_diff = strtotime($enddate) - strtotime($nowDate); // 종료일 - 현재시간
			$day_rs = ceil(($day_diff) / (60*60*24));
			
			$getLectureList[$parent_key] = $parent_item;
			$getLectureList[$parent_key]['end_day'] = $day_rs; // 부모강의 종료일 구하기
			
			$getTeacherInfo = $this->m_lecture->getMyclassTeacherInfo($parent_item['lec_no']);
			
			$lecKangListTotalTime = $this->m_lecture->getMyclassLectureKangListAllTotalTime($parent_item['lec_no']);
			$lecKangUserTotalTime = $this->m_lecture->getMyclassAllTotalTime($this->session->userdata('member_id'), $parent_item['no'] ,$parent_item['lec_no']);
			
			$getLectureList[$parent_key]['user_loading_cnt'] = $this->m_lecture->getMyclassLecKangUserLoadingCnt($this->session->userdata('member_id'), $parent_item['lec_no']);
			
			if ((int)$lecKangUserTotalTime->user_loading_time > 0) {
				$getLectureList[$parent_key]['user_loading_percent'] = ((int)$lecKangUserTotalTime->user_loading_time) / ((int)$lecKangListTotalTime->total_time * 60)  * 100;
			} else {
				$getLectureList[$parent_key]['user_loading_percent'] = 0;
			}
			
			$getLectureList[$parent_key]['teacher_info'] = $getTeacherInfo;
			$getLectureList[$parent_key]['child'] = $this->m_lecture->getMyclassLectureList($this->input->get('page_type'), $parent_item['lec_no'], $parent_item['order_num'], $this->session->userdata('member_id'), $this->session->userdata('member_no'));
			
			if (!empty($getLectureList[$parent_key]['child'])) {
				foreach($getLectureList[$parent_key]['child'] as $child_key => $child_item) {
					
					$enddate = $child_item['end_date'] . ' 00:00:00'; // 강의종료일
					$day_diff = strtotime($enddate) - strtotime($nowDate); // 종료일 - 현재시간
					$day_rs = ceil(($day_diff) / (60*60*24));
					$getLectureList[$parent_key]['child'][$child_key]['child_end_day'] = $day_rs; // 자식강의 종료일
					
					$getTeacherInfo = $this->m_lecture->getMyclassTeacherInfo($child_item['lec_no']);
					$getLectureList[$parent_key]['child'][$child_key]['child_teacher_info'] = $getTeacherInfo;
				}
			}
		}
		
		// 단과강의 패키지 강의분리
		$single_lecture_list = array();
		$package_lecture_list = array();
		foreach($getLectureList as $lecture_key => $lecture_item) {
			if (count($lecture_item['child']) >= 1) {
				$package_lecture_list[] = $lecture_item;
			} else {
				$single_lecture_list[] = $lecture_item;
			}
		}
		
		$head = array(
			'_css' => 'myclass'
			,'_js' => 'myclass'
			,'user_info' => $userInfo
			,'user_point' => $user_point
			,'user_coupon_cnt' => count($userCouponData)
		);
		
		$data = array(
			'images' => '//gscdn.hackers.co.kr/haksa2080/images'
			,'single_lecture_list' => $single_lecture_list
			,'package_lecture_list' => $package_lecture_list
		);
		
		$this->load->view('common/header', $head);
		// $this->load->view('common/allimpopup');
		$this->load->view('myclass/myclass', $data);
		$this->load->view('common/footer');
	}
	
	## 마이페이지 그리기
	public function checkMyClassType()
	{
		if ($this->session->userdata('member_id') == '') {
			echo "<script>alert('로그인이 필요한 페이지 입니다. 로그인후 이용해주세요.'); location.href='/login/main?return_url=/myclass/main';</script>";
		}
		
		$nowDate = date("Y:m:d h:i:s", time());  // 오늘 날짜 시간 가져옴.
		
		## 부모강의추출
		$getLectureParent = $this->m_lecture->getMyclassLectureParent($this->input->post('page_type'), $this->session->userdata('member_id'), $this->session->userdata('member_no'));
		
		## 자식강의추출
		$getLectureList = array();
		foreach($getLectureParent as $parent_key => $parent_item) {
			$enddate = $parent_item['end_date'] . ' 00:00:00'; // 강의종료일
			$day_diff = strtotime($enddate) - strtotime($nowDate); // 종료일 - 현재시간
			$day_rs = ceil(($day_diff) / (60*60*24));
			
			$getLectureList[$parent_key] = $parent_item;
			$getLectureList[$parent_key]['start_date'] = (!empty($parent_item['start_date'])) ? str_replace(':', '-', $parent_item['start_date']) : '-';
			$getLectureList[$parent_key]['end_date'] = (!empty($parent_item['end_date'])) ? str_replace(':', '-', $parent_item['end_date']) : '-';
			
			$getLectureList[$parent_key]['mem_lec_name'] = iconv("EUC-KR","UTF-8", $parent_item['mem_lec_name']);
			$getLectureList[$parent_key]['end_day'] = $day_rs; // 부모강의 종료일 구하기
			
			$getTeacherInfo = $this->m_lecture->getMyclassTeacherInfo($parent_item['lec_no']);
			$getLectureList[$parent_key]['teacher_info'] = $getTeacherInfo;
			$getLectureList[$parent_key]['teacher_info']->teacher_name = iconv("EUC-KR","UTF-8", $getTeacherInfo->teacher_name);
			
			$changeTeacherName = explode(',', $getLectureList[$parent_key]['teacher_info']->teacher_name);
			if (count($changeTeacherName) >= 2) {
				$getLectureList[$parent_key]['teacher_info']->teacher_name = $changeTeacherName[0]. iconv('EUC-KR', 'UTF-8', ' 교수님 외') . (count($changeTeacherName) - 1) . iconv('EUC-KR', 'UTF-8', '명');
			} else {
				$getLectureList[$parent_key]['teacher_info']->teacher_name = $changeTeacherName[0] . iconv('EUC-KR', 'UTF-8', '교수님');
			}
			
			$getLectureList[$parent_key]['child'] = $this->m_lecture->getMyclassLectureList($this->input->post('page_type'), $parent_item['lec_no'], $parent_item['order_num'], $this->session->userdata('member_id'), $this->session->userdata('member_no'));
			
			foreach($getLectureList[$parent_key]['child'] as $child_key => $child_item) {
				$enddate = $child_item['end_date'] . ' 00:00:00'; // 강의종료일
				$day_diff = strtotime($enddate) - strtotime($nowDate); // 종료일 - 현재시간
				$day_rs = ceil(($day_diff) / (60*60*24));
				$getLectureList[$parent_key]['child'][$child_key]['child_end_day'] = $day_rs; // 자식강의 종료일
				
				$getLectureList[$parent_key]['child'][$child_key]['mem_lec_name'] = iconv("EUC-KR","UTF-8", $child_item['mem_lec_name']);
				
				$getLectureList[$parent_key]['child'][$child_key]['start_date'] = (!empty($parent_item['start_date'])) ? str_replace(':', '-', $parent_item['start_date']) : '-';
				$getLectureList[$parent_key]['child'][$child_key]['end_date'] = (!empty($parent_item['end_date'])) ? str_replace(':', '-', $parent_item['end_date']) : '-';
				
				$getTeacherInfo = $this->m_lecture->getMyclassTeacherInfo($child_item['lec_no']);
				$getLectureList[$parent_key]['child'][$child_key]['child_teacher_info'] = $getTeacherInfo;
				$getLectureList[$parent_key]['child'][$child_key]['child_teacher_info']->teacher_name = iconv("EUC-KR","UTF-8", $getTeacherInfo->teacher_name);
				
				$changeTeacherName = explode(',', $getLectureList[$parent_key]['child'][$child_key]['child_teacher_info']->teacher_name);
				if (count($changeTeacherName) >= 2) {
					$getLectureList[$parent_key]['child'][$child_key]['child_teacher_info']->teacher_name = $changeTeacherName[0]. iconv('EUC-KR', 'UTF-8', ' 교수님 외') . (count($changeTeacherName) - 1) . iconv('EUC-KR', 'UTF-8', '명');
				} else {
					$getLectureList[$parent_key]['child'][$child_key]['child_teacher_info']->teacher_name = $changeTeacherName[0] . iconv('EUC-KR', 'UTF-8', '교수님');
				}
			}
		}
		
		
		// 단과강의 패키지 강의분리
		$single_lecture_list = array();
		$package_lecture_list = array();
		foreach($getLectureList as $lecture_key => $lecture_item) {
			if (count($lecture_item['child']) >= 1) {
				$package_lecture_list[] = $lecture_item;
			} else {
				$single_lecture_list[] = $lecture_item;
			}
		}
		
		echo json_encode(array('result' => true, 'package_lecture_list' => $package_lecture_list, 'single_lecture_list' => $single_lecture_list));
		exit;
	}
	
	## 해당강의 수강대기 -> 수강중 변경
	public function updateLectureState()
	{
		$updateState = $this->m_lecture->updateLectureState($this->input->post('no'), $this->input->post('lec_no'), $this->input->post('total_term'));
		if ($updateState) {
			echo json_encode(array('result' => true, 'msg' => iconv("EUC-KR","UTF-8", "유저 상태값 변경되었습니다.")));
		} else {
			echo json_encode(array('result' => true, 'msg' => iconv("EUC-KR","UTF-8", "유저 상태값 변경 실패!! 관리자에 문의해주세요.")));
		}
	}
	
	## 패키기 강의 리스트 페이지
	public function getMyclassParentListView()
	{
		if ($this->session->userdata('member_id') == '') {
			echo "<script>alert('로그인이 필요한 페이지 입니다. 로그인후 이용해주세요.'); location.href='/login/main?return_url=/myclass/main';</script>";
		}
		
		$nowDate = date("Y:m:d h:i:s", time());  // 오늘 날짜 시간 가져옴.
		
		## 부모강의추출
		$getLectureParent = $this->m_lecture->getMyclassLectureParentList($this->input->get('no'), $this->session->userdata('member_id'), $this->session->userdata('member_no'));
		
		## 자식강의추출
		$getLectureList = array();
		$enddate = $getLectureParent->end_date . ' 00:00:00'; // 강의종료일
		$day_diff = strtotime($enddate) - strtotime($nowDate); // 종료일 - 현재시간
		$day_rs = ceil(($day_diff) / (60*60*24));
		
		$getLectureList = $getLectureParent;
		$getLectureList->end_day = $day_rs; // 부모강의 종료일 구하기
		
		$getTeacherInfo = $this->m_lecture->getMyclassTeacherInfo($getLectureParent->lec_no);
		$getLectureList->teacher_info = $getTeacherInfo;
		
		## 총강의 진도율 표시
		$lecKangListTotalTime = $this->m_lecture->getMyclassLectureKangListAllTotalTime($getLectureParent->lec_no);
		$lecKangUserTotalTime = $this->m_lecture->getMyclassAllTotalTime($this->session->userdata('member_id'), $this->input->get('no'), $getLectureParent->lec_no);
		$getLectureList->lecKangTotalLoadingTime = ($lecKangListTotalTime->total_time != '') ? ($lecKangListTotalTime->total_time * 60) : '0'; // 강좌 -> 강의 총시간
		$getLectureList->lecKangUserTotalLoadingTime = ($lecKangUserTotalTime->user_loading_time != 0) ? $lecKangUserTotalTime->user_loading_time : '0' ; // 수강한 총강의시간
		$getLectureList->lecKangUserLoadingCnt = $this->m_lecture->getMyclassLecKangUserLoadingCnt($this->session->userdata('member_id'), $this->input->get('no'), $getLectureParent->lec_no);
		
		if ((int)$lecKangUserTotalTime->user_loading_time > 0) {
			$getLectureList->lecKangUserTotalLoadingPercent = ((int)$lecKangUserTotalTime->user_loading_time) / ((int)$lecKangListTotalTime->total_time * 60)  * 100;
		} else {
			$getLectureList->lecKangUserTotalLoadingPercent = 0;
		}
		
		$getLectureList->child = $this->m_lecture->getMyclassLectureList($this->input->post('page_type'), $getLectureParent->lec_no, $getLectureParent->order_num, $this->session->userdata('member_id'), $this->session->userdata('member_no'));
		
		
		foreach($getLectureParent->child as $child_key => $child_item) {
			$enddate = $child_item['end_date'] . ' 00:00:00'; // 강의종료일
			$day_diff = strtotime($enddate) - strtotime($nowDate); // 종료일 - 현재시간
			$day_rs = ceil(($day_diff) / (60*60*24));
			$getLectureList->child[$child_key]['child_end_day'] = $day_rs; // 자식강의 종료일
			$getTeacherInfo = $this->m_lecture->getMyclassTeacherInfo($child_item['lec_no']); // 자식강의 강의정보
			$getLectureList->child[$child_key]['child_teacher_info'] = $getTeacherInfo;
			
			
			## 총강의 진도율 표시
			$lecKangListTotalTime = $this->m_lecture->getMyclassLectureKangListAllTotalTime($child_item['lec_no']);
			$lecKangUserTotalTime = $this->m_lecture->getMyclassAllTotalTime($this->session->userdata('member_id'), $child_item['no'], $child_item['lec_no']);
			$getLectureList->child[$child_key]['lecKangTotalLoadingTime'] = ($lecKangListTotalTime->total_time != '') ? ($lecKangListTotalTime->total_time * 60) : '0' ; // 강좌 -> 강의 총시간
			$getLectureList->child[$child_key]['lecKangUserTotalLoadingTime'] = ($lecKangUserTotalTime->user_loading_time != 0) ? $lecKangUserTotalTime->user_loading_time : '0' ; // 수강한 총강의시간
			$getLectureList->child[$child_key]['lecKangUserLoadingCnt'] = $this->m_lecture->getMyclassLecKangUserLoadingCnt($this->session->userdata('member_id'), $child_item['no'], $child_item['lec_no']);
			
			if ((int)$lecKangUserTotalTime->user_loading_time > 0) {
				$getLectureList->child[$child_key]['lecKangUserTotalLoadingPercent'] = ((int)$lecKangUserTotalTime->user_loading_time) / ((int)$lecKangListTotalTime->total_time * 60)  * 100;
			} else {
				$getLectureList->child[$child_key]['lecKangUserTotalLoadingPercent'] = 0;
			}
		}
		
		$head = array(
			'_css' => 'myclass'
			,'_js' => 'myclass'
		);
		
		$data = array(
			'images' => 'https://gscdn.hackers.co.kr/haksa2080/images'
			,'getLectureList' => $getLectureList
		);
		
		$this->load->view('common/header', $head);
		// $this->load->view('common/allimpopup');
		$this->load->view('myclass/myclass_package', $data);
		$this->load->view('common/footer');
	}
	
}