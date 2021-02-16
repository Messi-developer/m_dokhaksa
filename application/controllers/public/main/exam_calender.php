<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Exam_calender extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->model("main/m_contents");
	}
	
	public function index()
	{
		$this->examCalender();
	}
	
	public function examCalender()
	{
		$head = array(
			'title' => '��Ŀ�� ���л� ��������',
			'css' => array(),
			'js' => array(
				'js/main/main.js'
			),
			'tab_on' => '',
			'pageType' => 'main'
		);
		
		// ���� ��������
		$exam_calender = $this->m_contents->getMainCalenderArray();
		
		foreach($exam_calender as $calender_key => $calender_item) {
			$exam_calender_day['receive_sdate_day'][] = date_of_week($calender_item['receive_date']); // �������
			$exam_calender_day['receive_edate_day'][] = date_of_week($calender_item['receive_end_date']); // �������
			$exam_calender_day['issue_day'][] = date_of_week($calender_item['issue_date']); // ��ǥ����
			$exam_calender_day['exam_day'][] = date_of_week($calender_item['exam_date']); // �������
		}
		
		$data = array(
			'images' => '//gscdn.hackers.co.kr/haksa2080/images'
			,'exam_calender' => $exam_calender
			,'exam_calender_day' => $exam_calender_day
		);
		
		$this->load->view('common/header', $head);
		$this->load->view('main/exam_calender', $data);
		$this->load->view('common/footer');
	}
	
}