<?php

class M_myclass extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->haksa2080 = $this->load->database("haksa2080", TRUE);
	}
	
	## �������� ��û�� ����Ʈ �ҷ�����
	function getLecReqResultList($member_id, $list_cnt)
	{
		$list_cnt = (!empty($list_cnt)) ? $list_cnt : '0' ;
		$sql = "SELECT * FROM haksa2080.`lec_req` WHERE user_id = '". $member_id ."' AND (lec_req_state != '2' OR lec_req_state != '3') AND lec_no != 'N;' ORDER BY `no` DESC LIMIT $list_cnt, 5";
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}
	
	## �������� ��û�� ����Ʈ cnt
	function getLecReqResultListCnt($member_id)
	{
		$sql = "SELECT COUNT(`no`) as cnt FROM haksa2080.`lec_req` WHERE user_id = '". $member_id ."' AND (lec_req_state != '2' OR lec_req_state != '3') AND lec_no != 'N;'";
		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
	
	## �ֹ���ȣ�� ����� ����Ȯ��
	function orderNumLectureInfo($lectureMemNo)
	{
		$sql = "SELECT `no`, lec_no, mem_lec_name, mem_user_id FROM haksa2080.`lecture_mem` WHERE `no` = '". $lectureMemNo ."' ORDER BY tdate DESC limit 0, 2";
		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
	
	## �ֹ���ȣ�� ����� ����Ȯ��
	function orderNumLectureInfoCnt($lectureMemNo)
	{
		$sql = "SELECT count(`no`) as cnt FROM haksa2080.`lecture_mem` WHERE `no` = '". $lectureMemNo ."'";
		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
	
	## �ֹ���ȣ�� ����� ����Ȯ�� (Ajax)
	function orderNumLectureInfoAjax($lectureMemNo)
	{
		$sql = "SELECT `no`, lec_no, mem_lec_name, mem_user_id FROM haksa2080.`lecture_mem` WHERE `no` IN ($lectureMemNo) ORDER BY tdate DESC";
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}
	
	## ���������û �������� ����Ʈ �ҷ����� (�θ��Ǹ� ����)
	function getLecReqList($member_id, $member_no, $get)
	{
		$addWhere = (!empty($get['lecReq']) && !empty($get['lmno'])) ? "AND lecMem.`no` = '". $get['lmno'] ."'" : "AND lecMem.`no` NOT IN (SELECT lec_no FROM lec_req)" ;
		
		$sql = "
				SELECT
					lecMem.`no`, lecMem.mem_user_id, lecMem.mem_no, lecMem.lec_no, lecMem.total_no, lecMem.mem_lec_name
				FROM lecture_mem AS lecMem
				WHERE lecMem.mem_user_id = '". $member_id ."' AND lecMem.mem_no = '". $member_no ."' AND lecMem.total_no = '0' AND lecMem.lec_state = '3'
				$addWhere
				";
		
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}
	
	## �������� ��û�� �� ����Ȯ��
	function getLecReqView($member_id, $get)
	{
		$sql = "SELECT * FROM lec_req WHERE user_id = '". $member_id ."' AND `no` = '". $get['lecReq'] ."' AND lec_no = '". $get['lmno'] ."'";
		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
	
	## �������� ��û���� �ִ��� Ȯ��
	function extensionInsertCheck($member_id, $lmno)
	{
		// lec_req_state (1.���, 2.����, 3.�ݷ�)
		$sql = "SELECT `no`, user_id, lec_no FROM lec_req WHERE user_id = '". $member_id ."' AND lec_no = '". $lmno ."' AND lec_req_state IN (1, 2, 3)";
		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
	
	## ���������û INSERT
	function extensionWriteInert($member_id, $lec_no, $hackers_helper, $hackers_hope, $passFileName, $upfile_name)
	{
		$insertData = array(
			'user_id'    		=> $member_id
			,'lec_no'    		=> $lec_no
			,'memo1'    		=> (!empty($hackers_helper)) ? $hackers_helper : '-'
			,'memo2'    		=> (!empty($hackers_hope)) ? $hackers_hope : '-'
			,'reason' 			=> '1' // ���հ� üũ default
			,'img_file' 		=> $upfile_name
			,'img_file_real' 	=> $passFileName
			,'regdate'			=> date('Y-m-d H:i:s')
		);
		
		return $this->haksa2080->insert('lec_req', $insertData);
	}
	
	## �������� ���û
	function extension_reapply($member_id, $no)
	{
		$updateData = array(
			'lec_req_state' => '1'
			,'adm_memo' => ''
		);
		$updateWhere = "`no` = '". $no ."' AND user_id = '". $member_id ."'";
		return $this->haksa2080->update('lec_req', $updateData, $updateWhere);
	}
	
	## �������� �ڼ�������
	function extensionLecReqView($lecReq)
	{
		$sql = "SELECT memo1, memo2, img_file, img_file_real, adm_memo, LEFT(regdate, 10) AS regdate, lec_req_state FROM haksa2080.`lec_req` WHERE `no` = '". $lecReq ."'";
		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
	
	## �������� lectureMem ��ȸ
	function extensionLecMemView($mem_no)
	{
		$sql = "SELECT mem_lec_name, lec_no, lec_big_cart FROM haksa2080.lecture_mem WHERE `no` = '". $mem_no ."'";
		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
	
	## �������� Category_big ��ȸ
	function extensionCategoryBigView($big_category)
	{
		$sql = "SELECT * FROM haksa2080.Category_big WHERE `no` = '". $big_category ."'";
		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
	
}