<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Learning_qna extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->model("login/m_member");
		$this->load->model("myclass/m_myclass");
		$this->load->model("board/m_board");
		$this->load->model("lecture/m_lecture");
		$this->load->model("main/m_file");
	}
	
	public function index()
	{
		$this->learning_qna_list();
	}
	
	## �н��������� ����Ʈ
	public function learning_qna_list()
	{
		if ($this->session->userdata('member_id') == '') {
			echo "<script>alert('�α����� �ʿ��� ������ �Դϴ�. �α����� �̿����ּ���.'); location.href='/login/main?return_url=/myclass/learning_qna';</script>";
		}
		
		// Header user_info, coupon_cnt
		$use_point = $this->m_member->getUserPoint($this->session->userdata('member_id')); // use_point (��������Ʈ)
		$used_point = $this->m_member->getUserUsedPoint($this->session->userdata('member_id')); // use_point (��������Ʈ)
		$user_point = ((int)$use_point->use_point - (int)$used_point->used_point);
		$userInfo = $this->m_member->getUserInfo($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		$userCouponData = $this->m_member->getUserCoupon($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		
		// �н������ϱ� ����Ʈ
		$boardList = $this->m_board->getBoardList($this->session->userdata('member_no'), 'learnqna');
		$boardListCnt = $this->m_board->getBoardListCnt($this->session->userdata('member_no'), 'learnqna');
		
		foreach($boardList as $lecInfo_key => $lecInfo_item) {
			// ������� ��������
			$boardList[$lecInfo_key]['board_review'] = $this->m_board->getBoardListReview('learnqna', $lecInfo_item['no']);
			
			// ī�װ� ������������
			$boardList[$lecInfo_key]['big_category'] = $this->m_board->getBoardBigcategory($lecInfo_item['big_cate']);
			
			// sm_cate �� lebel check
			$boardList[$lecInfo_key]['lec_level'] = $this->m_lecture->getLectureInfoLevel($lecInfo_item['sm_cate']);
			
			if ($boardList[$lecInfo_key]['lec_level']->lec_level1 == '1') {
				$boardList[$lecInfo_key]['lec_level'] = '1';
			} else if ($boardList[$lecInfo_key]['lec_level']->lec_level2 == '1') {
				$boardList[$lecInfo_key]['lec_level'] = '2';
			} else if ($boardList[$lecInfo_key]['lec_level']->lec_level3 == '1') {
				$boardList[$lecInfo_key]['lec_level'] = '3';
			} else if ($boardList[$lecInfo_key]['lec_level']->lec_level4 == '1') {
				$boardList[$lecInfo_key]['lec_level'] = '4';
			} else if ($boardList[$lecInfo_key]['lec_level']->lec_level5 == '1') {
				$boardList[$lecInfo_key]['lec_level'] = '5';
			} else if ($boardList[$lecInfo_key]['lec_level']->lec_level6 == '1') {
				$boardList[$lecInfo_key]['lec_level'] = '6';
			} else if ($boardList[$lecInfo_key]['lec_level']->lec_level7 == '1') {
				$boardList[$lecInfo_key]['lec_level'] = '7';
			} else {
				$boardList[$lecInfo_key]['lec_level'] = '';
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
			,'boardList' => $boardList
			,'boardListCnt' => $boardListCnt
		);
		
		$this->load->view('common/header', $head);
		$this->load->view('myclass/learning_qna_list', $data);
		$this->load->view('common/footer');
	}
	
	## �н��������� ����Ʈ(Ajax)
	public function learning_qna_listAjax()
	{
		$boardList = $this->m_board->getBoardList($this->session->userdata('member_no'), 'learnqna', $this->input->post('list_cnt'));
		foreach($boardList as $lecInfo_key => $lecInfo_item) {
			// ������� ��������
			$boardList[$lecInfo_key]['board_review'] = $this->m_board->getBoardListReview('learnqna', $lecInfo_item['no']);
			
			// ī�װ� ������������
			$boardList[$lecInfo_key]['big_category'] = $this->m_board->getBoardBigcategory($lecInfo_item['big_cate']);
			
			$boardList[$lecInfo_key]['division'] = iconv('EUC-KR', 'UTF-8', $lecInfo_item['division']);
			$boardList[$lecInfo_key]['memo'] = iconv('EUC-KR', 'UTF-8', $lecInfo_item['memo']);
			$boardList[$lecInfo_key]['subject'] = iconv('EUC-KR', 'UTF-8', $lecInfo_item['subject']);
			$boardList[$lecInfo_key]['big_category']->name = iconv('EUC-KR', 'UTF-8', $boardList[$lecInfo_key]['big_category']->name);
			
			foreach($boardList[$lecInfo_key]['board_review'] as $review_key => $review_item) {
				$boardList[$lecInfo_key]['board_review'][$review_key]['division'] = iconv('EUC-KR', 'UTF-8', $review_item['division']);
				$boardList[$lecInfo_key]['board_review'][$review_key]['name'] = iconv('EUC-KR', 'UTF-8', $review_item['name']);
				$boardList[$lecInfo_key]['board_review'][$review_key]['memo'] = iconv('EUC-KR', 'UTF-8', $review_item['memo']);
			}
		}
		if ($boardList) {
			echo json_encode(array('result' => true, 'msg' => iconv('EUC-KR', 'UTF-8', '�ҷ����� ����!!'), 'board_list' => $boardList));
		} else {
			echo json_encode(array('result' => false, 'msg' => iconv('EUC-KR', 'UTF-8', '�ҷ����� ����!! �����ڿ� �������ּ���.')));
		}
		exit;
	}
	
	## �н��������� ����
	public function learning_qna_write()
	{
		if ($this->session->userdata('member_id') == '') {
			echo "<script>alert('�α����� �ʿ��� ������ �Դϴ�. �α����� �̿����ּ���.'); location.href='/login/main?return_url=/myclass/learning_qna/learning_qna_write';</script>";
		}
		
		// select box option ��������
		$selectBoxOption = $this->m_board->getLectureMemOption($this->session->userdata('member_no'), $this->session->userdata('member_id'), 'lecture', $this->input->get('no'));
		
		$head = array(
			'_css' => 'myclass'
			,'_js' => 'board'
		);
		
		$data = array(
			'images' => '//gscdn.hackers.co.kr/haksa2080/images'
			,'selectBoxOption' => $selectBoxOption
		);
		
		$this->load->view('common/header', $head);
		$this->load->view('myclass/learning_qna_write', $data);
        $this->load->view('common/allimpopup');
		$this->load->view('common/footer');
	}
	
	## select box option �������� (Ajax)
	public function learning_qna_option() {
		// select box option ��������
		$selectBoxOption = $this->m_board->getLectureMemOption($this->session->userdata('member_no'), $this->session->userdata('member_id'), $this->input->post('check_select_type'));
		if (!empty($selectBoxOption)) {
			foreach($selectBoxOption as $option_key => $option_item) {
				$selectBoxOption[$option_key]['mem_lec_name'] = iconv('EUC-KR', 'UTF-8', $option_item['mem_lec_name']);
			}
			echo json_encode(array('result' => true, 'msg' => iconv('EUC-KR', 'UTF-8', '�˻��ʱ�ȭ ����!!'), 'selectBoxOption' => $selectBoxOption));
		} else {
			echo json_encode(array('result' => false, 'msg' => iconv('EUC-KR', 'UTF-8', '�˻��ʱ�ȭ ����!! �����ڿ� �������ּ���.')));
		}
		exit;
	}
	
	## �н����� ����
	public function learning_qna_delete()
	{
		$board_rs = $this->m_board->getBoardDelete($this->input->post('board_id', true), $this->input->post('board_no', true));
		if ($board_rs) {
			echo json_encode(array('result' => true, 'msg' => iconv('EUC-KR', 'UTF-8', '�Խñ� �����Ǿ����ϴ�.')));
		} else {
			echo json_encode(array('result' => true, 'msg' => iconv('EUC-KR', 'UTF-8', '�Խñ� ��������!! �����ڿ� �������ּ���.')));
		}
		exit;
	}
	
	## ���¸�, ����� ����
	public function learning_qna_lecture_info()
	{
		$lectureInfo = $this->m_board->getLearningQnaLectureInfo($this->session->userdata('member_no'), $this->session->userdata('member_id'), $this->input->post('lecMemNo'));
		if (!empty($lectureInfo)) {
			$lectureInfo->teacher_name = iconv('EUC-KR', 'UTF-8', $lectureInfo->teacher_name);
			$lectureInfo->lec_name = iconv('EUC-KR', 'UTF-8', $lectureInfo->lec_name);
			echo json_encode(array('result' => true, 'msg' => iconv('EUC-KR', 'UTF-8', '�˻��ʱ�ȭ ����!!'), 'lectureInfo' => $lectureInfo));
		} else {
			echo json_encode(array('result' => false, 'msg' => iconv('EUC-KR', 'UTF-8', '�˻��ʱ�ȭ ����!! �����ڿ� �������ּ���.')));
		}
		exit;
	}
	
	## �н��������� ���
	public function learning_qna_Insert() {
		$formData = $_POST;
		$insertRs = $this->m_board->HKboardInsert($this->session->userdata('member_no'), $this->session->userdata('member_name'), $formData);
		
		if ($insertRs) {
			echo "<script>alert('�н����� ��ϵǾ����ϴ�.'); location.href='/myclass/learning_qna';</script>";
			exit;
		}
	}
}