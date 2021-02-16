<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Index extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model("main/m_contents");
		$this->load->model("login/m_member");
        // $this->load->model("lecture/m_lecture");
    }
    
    /**
     * ���� ������
     */
    public function index()
    {
		// ���� �Ѹ����
		$main_slide_banner = $this->m_contents->getMainBanner();
	
		// ���� �ϴܹ��
		$main_slide_bottom_banner = $this->m_contents->getMainBottomBanner();
	
		// ���� ��Ÿ����
		$main_star_teacher_list = $this->m_contents->getMainStarTeacher();
	
		// ���� ��������
		$main_calender = $this->m_contents->getMainCalender();
		$main_calender->exam_day = date_of_week($main_calender->exam_date); // �������
	
		$nDate = date("Y-m-d",time()); // ���� ��¥
		$change_valDate = date('Y-m-d', strtotime($main_calender->exam_date)); // �����ͻ� ��¥
		$main_calender->exam_dday = intval((strtotime($change_valDate) - strtotime($nDate)) / 86400); // ������ ��¥���� ���ɴϴ�.
		
		// ���� ��������
		$main_notice = $this->m_contents->getMainNotice();
		
		// Header user_info, coupon_cnt
		$use_point = $this->m_member->getUserPoint($this->session->userdata('member_id')); // use_point (��������Ʈ)
		$used_point = $this->m_member->getUserUsedPoint($this->session->userdata('member_id')); // use_point (��������Ʈ)
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