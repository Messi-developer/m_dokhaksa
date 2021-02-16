<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Extension extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->model("login/m_member");
		$this->load->model("myclass/m_myclass");
		$this->load->model("main/m_file");
	}
	
	public function index()
	{
		$this->extensionList();
	}
	
	## ���������û ����Ʈ
	public function extensionList()
	{
		if ($this->session->userdata('member_id') == '') {
			echo "<script>alert('�α����� �ʿ��� ������ �Դϴ�. �α����� �̿����ּ���.'); location.href='/login/main?return_url=/myclass/main';</script>";
		}
		
		// Header user_info, coupon_cnt
		$use_point = $this->m_member->getUserPoint($this->session->userdata('member_id')); // use_point (��������Ʈ)
		$used_point = $this->m_member->getUserUsedPoint($this->session->userdata('member_id')); // use_point (��������Ʈ)
		$user_point = ((int)$use_point->use_point - (int)$used_point->used_point);
		$userInfo = $this->m_member->getUserInfo($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		$userCouponData = $this->m_member->getUserCoupon($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		
		$lecReqList = $this->m_myclass->getLecReqResultList($this->session->userdata('member_id'));
		if ($lecReqList) {
			foreach($lecReqList as $lecReq_key => $lecReq_item){
				$lecReqList[$lecReq_key]['lectureMemInfo'] = $this->m_myclass->orderNumLectureInfo($lecReq_item['lec_no']);
				// $lecReqList[$lecReq_key]['lectureMemInfoCnt'] = $this->m_myclass->orderNumLectureInfoCnt($lecReq_item['lec_no']);
			}
			$total_cnt = $this->m_myclass->getLecReqResultListCnt($this->session->userdata('member_id'));
		}
		
		$head = array(
			'_css' => 'myclass'
			,'_js' => 'myclass'
			,'user_info' => $userInfo
			,'user_point' => $user_point
			,'user_coupon_cnt' => count($userCouponData)
		);
		
		$data = array(
			'images' => 'https://gscdn.hackers.co.kr/haksa2080/images'
			,'lecReqList' => $lecReqList
			,'lecReqListCnt' => $total_cnt->cnt
		);
		
		$this->load->view('common/header', $head);
		$this->load->view('myclass/extension_list', $data);
		$this->load->view('common/footer');
	}
	
	## ���������û ����Ʈ (Ajax)
	public function extensionListAjax()
	{
		$lecReqList = $this->m_myclass->getLecReqResultList($this->session->userdata('member_id'), $this->input->post('list_cnt'));
		foreach($lecReqList as $lecReq_key => $lecReq_item) {
			$lectureMemNo[] = $lecReq_item['lec_no'];
			$lecReqNo[] = $lecReq_item['no'];
		}
		
		$lectureMemNo_rs= implode(',', $lectureMemNo);
		
		$lecReqGetPage = $this->m_myclass->orderNumLectureInfoAjax($lectureMemNo_rs);
		foreach($lecReqGetPage as $lecReq_key => $lecReq_item) {
			$lecReqGetPage[$lecReq_key]['mem_lec_name'] = iconv('EUC-KR', 'UTF-8', $lecReq_item['mem_lec_name']);
			$lecReqGetPage[$lecReq_key]['regdate'] = $lecReqList[$lecReq_key]['regdate'];
			$lecReqGetPage[$lecReq_key]['end_no'] = $lecReqList[$lecReq_key]['end_no'];
			$lecReqGetPage[$lecReq_key]['lecReqNo'] = $lecReqNo[$lecReq_key];
		}
		
		if ($lecReqGetPage) {
			echo json_encode(array('result' => true, 'msg' => iconv('EUC-KR', 'UTF-8', '����Ʈ ȣ�⼺��!!'), 'lecReqGetPage' => $lecReqGetPage));
		} else {
			echo json_encode(array('result' => true, 'msg' => iconv('EUC-KR', 'UTF-8', '����Ʈ ȣ�� ����!! �����ڿ� �������ּ���.')));
		}
		exit;
	}
	
	## �������� ��û
	public function extensionWrite()
	{
		if ($this->session->userdata('member_id') == '' || $this->session->userdata('member_no') == '') {
			echo "<script>alert('�α����� �ʿ��� ������ �Դϴ�. �α����� �̿����ּ���.'); location.href='/login/main?return_url=/myclass/extension/extensionWrite';</script>";
		}
		
		// �������� ������ ���Ǻҷ�����
		$getLecReqList = $this->m_myclass->getLecReqList($this->session->userdata('member_id'), $this->session->userdata('member_no'), $_GET);
		
		if ($this->input->get('lecReq') != '' && $this->input->get('lmno')) {
			$getLecReqView = $this->m_myclass->getLecReqView($this->session->userdata('member_id'), $_GET);
		}
		
		$head = array(
			'_css' => 'myclass'
			,'_js' => 'myclass'
		);
		
		$data = array(
			'images' => '//gscdn.hackers.co.kr/haksa2080/images'
			,'getLecReqList' => $getLecReqList
			,'getLecReqView' => $getLecReqView
		);
		
		$this->load->view('common/header', $head);
		$this->load->view('myclass/extension_write', $data);
        $this->load->view('common/allimpopup');
		$this->load->view('common/footer');
	}
	
	## �������� ��û ���
	public function extensionWriteInert()
	{
		if (!empty($_FILES['upload-name']['name'])) {
			$passFile = $this->m_file->fileUpload($_FILES['upload-name'], "https://www.haksa2080.com/zfiles/req_img/");
			$passFileName = $_FILES['upload-name']['name'];
			$upfile_name =  mktime().".".substr(strtolower($_FILES['upload-name']['name']),-3); // �����̸� (ex ���糯��_�����̸�
			move_uploaded_file ($_FILES['upload-name']['tmp_name'], $passFile . "$upfile_name"); // ���Ͼ��ε�
		}
		
		## �������� ������ִ��� Ȯ��
		foreach($this->input->post('lecReqSelect') as $lecReq_key => $lecReq_item){
			$insertCheck = $this->m_myclass->extensionInsertCheck($this->session->userdata('member_id'), $lecReq_item);
			if (empty($insertCheck)) {
				## �������� Insert
				$insertState = $this->m_myclass->extensionWriteInert($this->session->userdata('member_id'), $lecReq_item, $this->input->post('hackers_helper'), $this->input->post('hackers_hope'), $passFileName, $upfile_name);
			}
		}
		
		if ($insertState) {
			echo "<script>alert('�������� ��û�Ǿ����ϴ�.'); location.href='/myclass/extension';</script>";
		} else {
			echo "<script>alert('�������� ��û�����Ͽ����ϴ�. �����ڿ� �������ּ���.'); location.href='/myclass/extension/extensionWrite';</script>";
		}
	}
	
	## �������� �ڼ��� ����
	public function extensionView()
	{
		$LecReqView = $this->m_myclass->extensionLecReqView($this->input->get('lecReq'));
		$LecMemView = $this->m_myclass->extensionLecMemView($this->input->get('lmno'));
		$LectureCategoryBigView = $this->m_myclass->extensionCategoryBigView($LecMemView->lec_big_cart);
		
		switch($LecReqView->lec_req_state) {
			case '1' :
				$LecReqView->lec_req_state = '��û�Ϸ�';
				break;
			case '2' :
				$LecReqView->lec_req_state = '����Ϸ�';
				break;
			case '3' :
				$LecReqView->lec_req_state = '�ݷ�';
				break;
		}
		
		$head = array(
			'_css' => 'myclass'
			,'_js' => 'myclass'
		);
		
		$data = array(
			'images' => '//gscdn.hackers.co.kr/haksa2080/images'
			,'LecReqView' => $LecReqView
			,'LecMemView' => $LecMemView
			,'LectureCategoryBigView' => $LectureCategoryBigView
		);
		
		$this->load->view('common/header', $head);
		$this->load->view('myclass/extension_view', $data);
		$this->load->view('common/footer');
	}
	
	## �������� ���û
	public function extension_reapply()
	{
		$extension_reapply = $this->m_myclass->extension_reapply($this->session->userdata('member_id'), $this->input->post('lec_req_no'));
		if ($extension_reapply) {
			echo "<script>alert('��������� ��û�Ǿ����ϴ�.'); location.href='/myclass/extension';</script>";
		} else {
			echo "<script>alert('��������� ��û ����!!! �����ڿ� �������ּ���.'); location.href='/myclass/extension';</script>";
		}
		exit;
	}
	
}