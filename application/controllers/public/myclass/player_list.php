<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Player_list extends CI_Controller {

    function __construct() {
        parent::__construct();
		$this->load->model("login/m_member");
        $this->load->model("lecture/m_lecture");
		$this->load->model("main/m_file");
		// $this->load->helper('download');
    }

    public function index()
    {
        $this->myclassContent();
    }

    public function myclassContent()
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
		
    	## ���󸮽�Ʈ cnt
		$getLecKangCnt = $this->m_lecture->getMyclassLecKangCnt($this->input->get('lec_no'));
		
		
    	## ��������
    	$lectureView = $this->m_lecture->getMyclassLectureView($this->session->userdata('member_id'), $this->session->userdata('member_no'), $this->input->get('lec_no'), $this->input->get('no'));
		$nowDate = date("Y:m:d h:i:s", time());  // ���� ��¥ �ð� ������.
		$enddate = $lectureView->end_date . ' 00:00:00'; // ����������
		$day_diff = strtotime($enddate) - strtotime($nowDate); // ������ - ����ð�
		$day_rs = ceil(($day_diff) / (60*60*24));
		$lectureView->end_day = $day_rs; // ���� �����Ⱓ
  
		## ���ǿ��󸮽�Ʈ
		$lectureKangList = $this->m_lecture->getMyclassLectureKangList($this->input->get('lec_no'));
		$lectureView->lecKangList = $lectureKangList;
		foreach($lectureView->lecKangList as $lecKang_key => $lecKang_item) {
			$lectureView->lecKangList[$lecKang_key]['loading_time'] = $this->m_lecture->getMyclassLecKangUserLoadingTime($this->session->userdata('member_id'), $this->input->get('no') ,$lecKang_item['lecture_no'], $lecKang_item['leck_kang_name']);
		}
	
		## �Ѱ��� ������ ǥ��
		$lecKangListTotalTime = $this->m_lecture->getMyclassLectureKangListAllTotalTime($this->input->get('lec_no'));
		$lecKangUserTotalTime = $this->m_lecture->getMyclassAllTotalTime($this->session->userdata('member_id'), $this->input->get('no'), $this->input->get('lec_no'));
		
		$lectureView->lecKangUserLoadingCnt = $this->m_lecture->getMyclassLecKangUserLoadingCnt($this->session->userdata('member_id'), $this->input->get('no'), $this->input->get('lec_no'));
		$lectureView->lecKangTotalLoadingTime = ($lecKangListTotalTime->total_time != '') ? ($lecKangListTotalTime->total_time * 60) : '0' ; // ���� -> ���� �ѽð�
		$lectureView->lecKangUserTotalLoadingTime = ($lecKangUserTotalTime->user_loading_time != 0) ? $lecKangUserTotalTime->user_loading_time : '0' ; // ������ �Ѱ��ǽð�
		
		if ((int)$lecKangUserTotalTime->user_loading_time > 0) {
			$lectureView->lecKangUserTotalLoadingPercent = ((int)$lecKangUserTotalTime->user_loading_time) / ((int)$lecKangListTotalTime->total_time * 60)  * 100;
		} else {
			$lectureView->lecKangUserTotalLoadingPercent = 0;
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
			,'lectureView' => $lectureView
			,'getLecKangCnt' => $getLecKangCnt
        );

        $this->load->view('common/header', $head);
        $this->load->view('myclass/player_list', $data);
        $this->load->view('common/footer');
    }

    ## ������ ��ư
    public function getMyclassLecKang()
	{
		// ���� ���󼷳��� ��������
		$lectuerInfo = $this->m_lecture->getLectureInfo($this->input->post('lec_no'));
		
		$getLecKang = $this->m_lecture->getMyclassLectureKangList($this->input->post('lec_no'), $this->input->post('page_cnt'));
		if ($getLecKang) {
			foreach($getLecKang as $lecKang_key => $lecKang_item) {
				$getLecKang[$lecKang_key]['lec_content'] = iconv('EUC-KR', 'UTF-8', $lecKang_item['lec_content']);
				$getLecKang[$lecKang_key]['lec_content'] = explode(']', $getLecKang[$lecKang_key]['lec_content']);
				$getLecKang[$lecKang_key]['player_img'] = $lectuerInfo->lecLImg;
				$getLecKang[$lecKang_key]['loading_time'] = $this->m_lecture->getMyclassLecKangUserLoadingTime($this->session->userdata('member_id'), $lecKang_item['lecture_no'], $lecKang_item['leck_kang_name']);
			}
			
			echo json_encode(array('result' => true, 'msg' => iconv('EUC-KR', 'UTF-8', '���Ǻҷ����� ����!!!'), 'getLecKang' => $getLecKang));
		} else {
			echo json_encode(array('result' => false, 'msg' => iconv('EUC-KR', 'UTF-8', '���Ǻҷ����� ����!! �����ڿ� �������ּ���.')));
		}
		exit;
	}
	
	## ���� �ٿ�ε�
	public function fileDownLoad()
	{
		## �������� Ȯ��
		$lectureState = $this->m_lecture->checkLectureState($this->session->userdata('member_no', true), $this->session->userdata('member_id', true), $this->input->get('lec_no', true));
		if ($lectureState->no != '') {
			$file_dir = "https://www.haksa2080.com/lec_data/data/";
			$file_name = $lectureState->lec_no . '_data.';
			$file_type = array('pdf','zip');
			foreach($file_type as $check_key => $check_item) {
				$this->m_file->re_force_download($file_dir . $file_name . $check_item);
			}
		} else {
			echo "<script>alert('������ ���������ʽ��ϴ�. �����ڿ� �������ּ���.'); history.back(-1);</script>";
		}
	}
}