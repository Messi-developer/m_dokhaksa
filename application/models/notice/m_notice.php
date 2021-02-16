<?php
class M_notice extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		// $this->load->model("banner/m_banner");
		$this->haksa2080 = $this->load->database("haksa2080", TRUE);
	}
	
	## 1:1 문의상담 30초 이내에 등록되었다면 차단
	function consultingCheck($getPost)
	{
		$query = $this->haksa2080->query("SELECT reg_date, now() as chk_date, TIMESTAMPDIFF(SECOND, reg_date, NOW()) AS diff FROM qna_list WHERE `name` = '". $getPost['name'] ."' AND phone = '". $getPost['phone'] ."' AND email = '". $getPost['email'] ."' ORDER BY `no` DESC");
		return $query->result_array();
	}
	
	## 1:1 문의상담 insert
	function consultingInsertQuery($getPost, $getAgree, $getNowdate)
	{
		$insertData = array(
			'code'    => 1 // 1:1 문의상담 = 1
			,'name' => $getPost['name']
			,'phone' => $getPost['phone']
			,'email' => $getPost['email'] . '@' . $getPost['join_email']
			,'subject' => $getPost['subject']
			,'contents' => $getPost['contents']
			,'school' => $getPost['school']
			,'course' => $getPost['course']
			,'major' => $getPost['major']
			,'ask' => $getPost['ask']
			,'rsv_date' => '0000-00-00 00:00:00'
			,'reg_date' => $getNowdate
			,'ip_addr' => $_SERVER['REMOTE_ADDR']
			,'agree1' => $getAgree
			,'agree2' => $getAgree
			,'channel' => 'M'
		);
		
		 return $this->haksa2080->insert('qna_list', $insertData);
	}
	
	## FAQ
	function getFaqListQuery($getPost)
	{
		$query = $this->haksa2080->query("SELECT cate_big, sort_big, sort_small, question, answer FROM faq_list WHERE cate_big = '". $getPost ."' ORDER BY sno ASC");
		return $query->result_array();
	}
	
	## 해바 ip 체크
	function hackersHelpIp($getIp)
	{
		$query = $this->haksa2080->query("SELECT * FROM customer_proposal WHERE ip_addr = '". $getIp ."' AND createdate >= CURDATE() ORDER BY `no` DESC LIMIT 1");
		return $query->row();
	}
	
	## 해커스에 바란다
	function hackersHelpInsert($getPost, $getUserInfo)
	{
		$insertQuery = array(
			'name' => (!empty($getPost['name'])) ? $getPost['name'] : $this->session->userdata('member_name')
			,'id' => (!empty($getUserInfo['member_id'])) ? $getUserInfo['member_id'] : ''
			,'customer_type' => (!empty($getPost['help_type'])) ? (int)$getPost['help_type'] : 2
			,'email' => (!empty($getPost['email'])) ? $getPost['email'] : ''
			,'subject' => (!empty($getPost['subject'])) ? $getPost['subject'] : ''
			,'contents' => (!empty($getPost['contents'])) ? $getPost['contents'] : ''
			,'createdate' => date('Y-m-d H:i:s')
			,'ip_addr' => (!empty($getUserInfo['ip_addr'])) ? $getUserInfo['ip_addr'] : ''
			,'channel' => 'M'
		);
		
		$insert = $this->haksa2080->insert('customer_proposal',$insertQuery);
		return $insert;
	}
	
}