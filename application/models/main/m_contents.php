<?php

class M_contents extends CI_Model
{
    function __construct()
    {
        parent::__construct();
		$this->load->model("banner/m_banner");
		$this->haksa2080 = $this->load->database("haksa2080", TRUE);
    }
    
    ## 메인 롤링배너
	function getMainBanner()
	{
		$main_slide_banner = $this->m_banner->getMainBanner_list('NEW_M_MAIN_BIG');
		return $main_slide_banner;
	}
	
	## 메인 하단배너
	function getMainBottomBanner()
	{
		$main_slide_bottom_banner = $this->m_banner->getMainSubBanner('NEW_M_MAIN_BOTTOM');
		return $main_slide_bottom_banner;
	}
 
	## 메인 스타교수
	function getMainStarTeacher()
	{
		$this->haksa2080->select("star.*, tea.profile, tea.profile_img, tea.intro_img");
		$this->haksa2080->from("star_teacher AS star");
		$this->haksa2080->join("teacher_list AS tea", "star.sno = tea.sno");
		$this->haksa2080->where("star.recommand_yn", "y");
		$this->haksa2080->order_by("star.view_order", "ASC");
		$query = $this->haksa2080->get();
		
		return $query->result_array();
	}
	
	## 메인 시험일정
	function getMainCalender()
	{
		$query = $this->haksa2080->query("
										SELECT
											`no`,`name`, DATE_FORMAT(receive_date, '%m/%d') AS receive_date_md, receive_date, exam_date, DATE_FORMAT(exam_date, '%m/%d') AS exam_date_md , `type`
										FROM
											calender
										WHERE exam_date >= NOW()
										ORDER BY `no` ASC
										");
		return $query->row();
	}
	
	function getMainCalenderArray()
	{
		$query = $this->haksa2080->query("
										SELECT
											`name`
											, DATE_FORMAT(receive_date, '%m.%d') AS receive_sdate
											, DATE_FORMAT(receive_date, '%H:%i') AS receive_sdate_time
											, receive_date
											, DATE_FORMAT(receive_end_date, '%m.%d') AS receive_edate
											, DATE_FORMAT(receive_end_date, '%H:%i') AS receive_edate_time
											, receive_end_date
											, DATE_FORMAT(exam_date, '%m.%d') AS exam_date_c
											, exam_date
											, `type`
											, DATE_FORMAT(issue_date, '%m.%d') AS issue_date_c
											, DATE_FORMAT(issue_date, '%H:%i') AS issue_date_time
											, issue_date
											,link, link_2
										FROM
											calender
										ORDER BY `no` DESC
									");
		return $query->result_array();
	}
	
	## 메인 공지사항
	function getMainNotice($page_type)
	{
		$limit = (!empty($page_type)) ? "" : "LIMIT 0, 5" ;
    	$query = $this->haksa2080->query("SELECT notice.`no`, notice.division, notice.subject, DATE_FORMAT(notice.wdate, '%Y . %m . %d') AS wdate, (SELECT COUNT(NO) AS cnt FROM HKboard_notice WHERE father = notice.`no`) AS answer_cnt FROM HKboard_notice AS notice WHERE notice.x != 'del' AND notice.`main_notice` != '' AND notice.`main_notice` != '0' ORDER BY main_notice = 0, main_notice, notice.`no` DESC $limit");
    	return $query->result_array();
	}
	
	## 공지사항 페이지 리스트
	function getNoticeList($getCnt)
	{
		$query = $this->haksa2080->query("SELECT notice.`no`, notice.division, notice.big_cate, notice.subject, DATE_FORMAT(notice.wdate, '%Y . %m . %d') AS wdate, (SELECT COUNT(NO) AS cnt FROM HKboard_notice WHERE father = notice.`no`) AS answer_cnt FROM HKboard_notice AS notice WHERE notice.x != 'del' ORDER BY main_notice = 0, main_notice, notice.`no` DESC LIMIT $getCnt, 5");
		return $query->result_array();
	}
	
	## 공지사항 페이지 리스트 Cnt
	function getNoticeListCnt()
	{
		$query = $this->haksa2080->query("SELECT COUNT(`no`) as cnt FROM HKboard_notice AS notice WHERE notice.x != 'del'");
		return $query->row();
	}
	
	## 공지사항 상세보기
	function getNoticeView($getNo)
	{
    	$query = $this->haksa2080->query("SELECT notice.`no`, notice.hit, notice.memo, notice.big_cate, notice.subject, DATE_FORMAT(notice.wdate, '%Y . %m . %d') AS wdate, (SELECT COUNT(NO) AS cnt FROM HKboard_notice WHERE father = notice.`no`) AS answer_cnt FROM HKboard_notice AS notice WHERE notice.x != 'del' AND `no` = '". $getNo ."' ORDER BY main_notice = 0, main_notice, notice.`no` DESC");
		return $query->row();
	}
	
	
}