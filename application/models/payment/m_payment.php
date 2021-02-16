<?php

class M_payment extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->model("lecture/m_lecture");
		$this->haksa2080 = $this->load->database("haksa2080", TRUE);
	}
	
	## �������� ��������
	function getCouponList($member_no, $member_id)
	{
		$query = $this->haksa2080->query("SELECT * FROM auth_list WHERE user_no = '". $member_no ."' AND user_id = '". $member_id ."' AND order_num = ''");
		return $query->result_array();
	}
	
	## ��ٱ��� ���
	function basketInert($lec_no, $member_no, $return_url)
	{
		$checkBasket = $this->m_payment->getUserBasket($lec_no, $member_no);
		if (count($checkBasket) > 0){
			echo json_encode(array('result' => false, 'msg' => iconv('CP949', 'UTF-8', '�ش簭�Ǵ� �̹� ��ٱ��Ͽ� ��ϵǾ��ֽ��ϴ�. Ȯ�����ּ���.'), 'return_url' => $return_url));
			exit;
		}
		
		$basketInertData = array(
			'mem_no' => $member_no
		,'lec_no' => $lec_no
		,'reg_date' => date('Y-m-d H:i:s')
		,'reg_ip' => $_SERVER['REMOTE_ADDR']
		);
		
		$insertRs = $this->haksa2080->insert('lecture_cart', $basketInertData);
		if ($insertRs) {
			echo json_encode(array('result' => true, 'msg' => iconv('CP949', 'UTF-8', '��ٱ��� ��ϼ���')));
		} else {
			echo json_encode(array('result' => false, 'msg' => iconv('CP949', 'UTF-8', '��ٱ��� ��Ͻ��� �����ڿ� �������ּ���.')));
		}
		exit;
	}
	
	## ��ٱ��� ����
	function basketDelete($lec_no, $member_no)
	{
		$deleteQuery = $this->haksa2080->query("DELETE FROM lecture_cart WHERE mem_no = '". $member_no ."' AND lec_no IN ($lec_no)");
		if ($deleteQuery) {
			echo json_encode(array('result' => true, 'msg' => iconv('CP949', 'UTF-8', '��ٱ��� ���� ����')));
		} else {
			echo json_encode(array('result' => false, 'msg' => iconv('CP949', 'UTF-8', '��ٱ��� �������� �����ڿ� �������ּ���.')));
		}
		exit;
	}
	
	## ��ٱ��� ��� (���ǻ󼼺��� ������)
	function basketInertArray($arrayLecNo, $member_no, $return_url)
	{
		foreach($arrayLecNo as $lec_key => $lec_item) {
			$checkBasket = $this->m_payment->getUserBasket($lec_item, $member_no);
			if (count($checkBasket) > 0){
				echo json_encode(array('result' => false, 'msg' => iconv('CP949', 'UTF-8', '�ش簭�Ǵ� �̹� ��ٱ��Ͽ� ��ϵǾ��ֽ��ϴ�. Ȯ�����ּ���.'), 'return_url' => $return_url));
				exit;
			} else {
				$basketInertData = array(
					'mem_no' => $member_no
				,'lec_no' => $lec_item
				,'reg_date' => date('Y-m-d H:i:s')
				,'reg_ip' => $_SERVER['REMOTE_ADDR']
				);
				
				$insertRs = $this->haksa2080->insert('lecture_cart', $basketInertData);
			}
		}
		
		if ($insertRs) {
			echo json_encode(array('result' => true, 'msg' => iconv('CP949', 'UTF-8', '��ٱ��� ��ϼ���')));
			exit;
		}
		
	}
	
	## ��ٱ��� ��Ͽ��� Ȯ��
	function getUserBasket($lec_no, $member_no)
	{
		$query = $this->haksa2080->query("SELECT sno, lec_no, mem_no FROM lecture_cart WHERE lec_no = '". $lec_no ."' AND mem_no = '". $member_no ."'");
		return $query->row();
	}
	
	## ��ٱ��� ����Ʈ �ҷ�����
	function getUserBasketCode($member_no)
	{
		$query = $this->haksa2080->query("SELECT lec_no FROM lecture_cart WHERE mem_no = '". $member_no ."' ORDER BY sno DESC");
		return $query->result_array();
	}
	
	## ��ٱ��Ͽ� ��ϵ� �������� ��������
	function getUserBasketLectureList($getLectureCode)
	{
		$query = $this->haksa2080->query("SELECT `no`, teacher_name, lec_name, lec_price, lec_real_price, lec_join, book_join FROM lecture WHERE `no` = '". $getLectureCode ."'");
		return $query->row();
	}
	
	## ��ٱ��Ͽ� ��ϵ� �������� ��������
	function getUserBasketBookList($getBookCode)
	{
		$query = $this->haksa2080->query("SELECT book_id, book_name, author, store_link, pamount FROM book_info WHERE book_id = '". $getBookCode ."'");
		return $query->row();
	}
	
	## ����/��۳��� ��ȸ (������) : total_price
	function getUserDeliveryPassBookTotalPrice($member_no, $member_id, $order_num)
	{
		$sql = "
				SELECT SUM(total_price) as total_price, order_num
				FROM
					lecture_mem AS lecMem
				WHERE mem_no = '". $member_no ."' AND mem_user_id = '". $member_id ."' AND order_num = '". $order_num ."' AND total_no = '0'
				";

		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
	
	## ����/��۳��� ��ȸ (������) : �θ��Ǹ� ����
	function getUserDeliveryPassBook($member_no, $member_id, $limit)
	{
		// after_check ( e = �Ա�, 0 = �Ա��� )
		$limit = (!empty($limit)) ? $limit : '0' ;
		$sql = "
				SELECT
					DISTINCT(order_num), lec_no, GROUP_CONCAT(mem_lec_name) AS mem_lec_name, mem_name, mem_user_id, real_price, wdate, bank_name, account_num, `no`
					,(SELECT COUNT(`order_num`) FROM haksa2080.lecture_mem WHERE lecMem.order_num = order_num AND total_no = '0') AS sub_lecture_cnt
				FROM
					haksa2080.`lecture_mem` AS lecMem
				WHERE
					mem_no = '". $member_no ."' AND mem_user_id = '". $member_id ."'
					AND regi_method IN ('0', '2') AND lec_state = '0'
					AND account_num != '' AND account_num IS NOT NULL
					AND bank_name IS NOT NULL AND bank_name != ''
					AND after_check = '0' AND total_no = '0'
					AND DATE_ADD(wdate, INTERVAL 1 WEEK) >= NOW()
					
				GROUP BY order_num
				ORDER BY wdate DESC
				LIMIT $limit, 5
				";
		
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}
	
	## ����/��۳��� ��ȸ (������)
	function getUserDeliveryPassBookCnt($member_no, $member_id)
	{
		$sql = "
				SELECT
					DISTINCT(order_num), lec_no, GROUP_CONCAT(mem_lec_name) AS mem_lec_name, mem_name, mem_user_id, real_price, wdate, bank_name, account_num, `no`
					,(SELECT COUNT(`order_num`) FROM haksa2080.lecture_mem WHERE lecMem.order_num = order_num AND total_no = '0') AS sub_lecture_cnt
				FROM
					haksa2080.`lecture_mem` AS lecMem
				WHERE
					mem_no = '". $member_no ."' AND mem_user_id = '". $member_id ."'
					AND regi_method IN ('0', '2') AND lec_state = '0'
					AND account_num != '' AND account_num IS NOT NULL
					AND bank_name IS NOT NULL AND bank_name != ''
					AND after_check = '0' AND total_no = '0'
					AND DATE_ADD(wdate, INTERVAL 1 WEEK) >= NOW()
					
				GROUP BY order_num
				ORDER BY wdate DESC
				";
		
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}

	## ����/��۳��� ��ȸ (�����Ϸ�/��ҳ���) : �θ��Ǹ� ����
	// lec_state (1:�������, 2:������, 3:�����Ϸ�, 4:�Ͻ�����, 5:����ȯ��, 6:�κ�ȯ��, 9:��������)
	function getUserLectureMemList($member_no, $member_id, $limit)
	{
		$limit = (!empty($limit)) ? $limit : '0' ;
		$sql = "
		SELECT
			order_num, lec_no, GROUP_CONCAT(mem_lec_name) AS mem_lec_name, mem_name, mem_user_id, real_price, total_price, LEFT(wdate, 10) as wdate, regi_method, `no`, lec_state
			,discount_div, discount_price, discount_percent
			,(SELECT COUNT(`order_num`) FROM haksa2080.lecture_mem WHERE lecMem.order_num = order_num AND total_no = '0') AS sub_lecture_cnt
		FROM
			lecture_mem AS lecMem
		WHERE
			mem_no = '". $member_no ."' AND mem_user_id = '". $member_id ."'
			AND regi_method IN (1, 2, 3)
			AND lec_state IN (1, 2, 3, 4, 5, 6, 9)
			AND total_no = '0'
			
		GROUP BY order_num
		ORDER BY `no` DESC
		LIMIT $limit, 5
		";
		
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}
	
	## ����/��۳��� ��ȸ (�����Ϸ�/��ҳ���)
	function getUserLectureMemListCnt($member_no, $member_id)
	{
		$sql = "
		SELECT
			order_num, lec_no, GROUP_CONCAT(mem_lec_name) AS mem_lec_name, mem_name, mem_user_id, real_price, total_price, LEFT(wdate, 10) as wdate, regi_method, `no`, lec_state
			,discount_div, discount_price, discount_percent
			,(SELECT COUNT(`order_num`) FROM haksa2080.lecture_mem WHERE lecMem.order_num = order_num AND total_no = '0') AS sub_lecture_cnt
		FROM
			lecture_mem AS lecMem
		WHERE
			mem_no = '". $member_no ."' AND mem_user_id = '". $member_id ."'
			AND regi_method IN (1, 2, 3)
			AND lec_state IN (1, 2, 3, 4, 5, 6, 9)
			AND total_no = '0'
			
		GROUP BY order_num
		ORDER BY `no` DESC
		"; // lec_state (1:�������, 2:������, 3:�����Ϸ�, 4:�Ͻ�����, 5:����ȯ��, 6:�κ�ȯ��, 9:��������)
		
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}
	
	## ����/��۳��� �ֹ���ȣ ����ó��
	function lectureMemDelete ($member_no, $member_id, $order_num)
	{
		$sql = "SELECT mem_no, mem_user_id, order_num FROM haksa2080.lecture_mem WHERE mem_no = '". $member_no ."' AND mem_user_id = '". $member_id ."' AND order_num = '". $order_num ."'";
		$query = $this->haksa2080->query($sql);
		$lectureMem = $query->result_array();
		
		foreach($lectureMem as $lecMem_key => $lecMem_item) {
			$this->haksa2080->where('order_num', $lecMem_item['order_num']);
			$this->haksa2080->where('mem_no', $member_no);
			$this->haksa2080->where('mem_user_id', $member_id);
			$this->haksa2080->delete('lecture_mem');
		}
		
		return $lectureMem;
	}
	
	## �ֹ���ȣ�� besong_info list Ȯ��
	function getBesongInfoList($order_num, $book_code)
	{
		$sql = "
				SELECT
					besong.book_code, besong.uno, besong.home_address, besong.tail_address, besong.book_sale, besong.book_order, besong.book_move_num
					, besong.booksender, besong.book_price, besong.sap_ship_state
				FROM besong_info AS besong
				WHERE besong.order_num = '". $order_num ."' AND besong.book_code = '". $book_code ."'
				";
		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
	
	## ������ ������� check
	function authListUpdateCheck($member_no, $member_id, $coupon_num)
	{
		// etc :  1.���������� ���, 2. �����Ϸ�� �������
		$updateData = array(
			'use_date_temp' => date('Y:m:d')
			,'etc' => '1'
		);
		$updateWhere = "user_no = '". $member_no ."' AND user_id = '". $member_id ."' AND cupone_number = '". $coupon_num ."'";
		return $this->haksa2080->update('auth_list', $updateData, $updateWhere);
	}
	
	## ���Ǳ��Ž� lecture_mem INSERT
	function lectureMemInert($orderNum, $lectureInfo)
	{
		## ���Ž� ���簡�������
		if (!empty($lectureInfo['books'])) { // ���� insert
			foreach($lectureInfo['books'] as $book_key => $book_item) {
				$lectureMemInsert = array(
					'no' => '',
					'order_num' => $orderNum,
					'mem_no' => $this->session->userdata('member_no', true),
					'mem_user_id' => $this->session->userdata('member_id', true),
					'lec_no' => ((int)$book_item->book_id + 50000),
					'total_no' => 0,
					'mem_name' => $this->session->userdata('member_name', true),
					'regi_method' => 0,
					'lec_big_cart' => $book_item->category,
					'mem_middle_cart' => $book_item->middle_cart,
					'mem_lec_name' => $book_item->book_name,
					'real_price' => $book_item->pamount,
					'total_price' => 0,
					'one_kang_price' => 0,
					'discount_price' => '',
					'discount_div' => '',
					'discount_detail' => '',
					'discount_etc' => '',
					'discount_kind' => '',
					// 'discount_kind' => '{$_GET['event_sno']}', // �̺�Ʈ�ڵ庰 ���ϱݾ� ���� ����
					'total_term' => 0,
					'lec_state' => 0,
					'book_sale' => '',
					'down_count' => 0,
					'once_stop_su' => 0,
					'wdate' => date('Y:m:d'),
					'tdate' => date('Y:m:d-H:i:s'),
				);
				
				$this->haksa2080->insert('lecture_mem', $lectureMemInsert);
			}
		}
		
		## ���Ž� ���ǰ� �������
		if (!empty($lectureInfo['lecture'])) {
			foreach($lectureInfo['lecture'] as $lecture_key => $lecture_item) {
				
				## �θ��� INSERT
				$parentLectureInfo = $this->m_lecture->getLectureInfo($lecture_item->no);
				if ($parentLectureInfo->lec_term < '30') {
					$p_stop_cnt = '2';
				} else if ($parentLectureInfo->lec_term < '20') {
					$p_stop_cnt = '1';
				} else if ($parentLectureInfo->lec_term < '10') {
					$p_stop_cnt = '0';
				} else {
					$p_stop_cnt = '3';
				}
				
				$parentInsert = array(
					'no' => '',
					'order_num' => $orderNum,
					'mem_no' => $this->session->userdata('member_no'),
					'mem_user_id' => $this->session->userdata('member_id'),
					'lec_no' => $parentLectureInfo->no, // �����ڵ�
					'total_no' => 0, // �θ��� �ڵ�
					'mem_name' => $this->session->userdata('member_name'),
					'regi_method' => 0,
					'lec_big_cart' => $parentLectureInfo->big_cart,
					'mem_middle_cart' => $parentLectureInfo->middle_cart,
					'mem_lec_name' => $parentLectureInfo->lec_name,
					'real_price' => ($parentLectureInfo->lec_real_price != '') ? $parentLectureInfo->lec_real_price : $parentLectureInfo->real_price ,
					'total_price' => 0,
					'one_kang_price' => 0,
					'discount_price' => '',
					'discount_div' => '',
					'discount_detail' => '',
					'discount_etc' => '',
					'discount_kind' => '',
					// 'discount_kind' => '{$_GET['event_sno']}', // �̺�Ʈ�ڵ庰 ���ϱݾ� ���� ����
					'total_term' => ($parentLectureInfo->lec_term != '') ? (int)$parentLectureInfo->lec_term : '' ,
					'lec_state' => 0,
					'book_sale' => '',
					'down_count' => 0,
					'once_stop_su' => $p_stop_cnt,
					'wdate' => date('Y:m:d'),
					'tdate' => date('Y:m:d-H:i:s'),
				);
				$this->haksa2080->insert('lecture_mem', $parentInsert);
				
				## ���� ���� INSERT
				if ($lecture_item->lec_join != '' && $lecture_item->lec_join != null) {
					$joinLectureNo = explode(',', $lecture_item->lec_join);
					foreach($joinLectureNo as $join_key => $join_item) {
						$joinLectureInfo = $this->m_lecture->getLectureInfo($join_item); // ������ȸ
						if ($joinLectureInfo->lec_term < '30') {
							$stop_cnt = '2';
						} else if ($joinLectureInfo->lec_term < '20') {
							$stop_cnt = '1';
						} else if ($joinLectureInfo->lec_term < '10') {
							$stop_cnt = '0';
						} else {
							$stop_cnt = '3';
						}
						
						$joinLectureInsert = array(
							'no' => '',
							'order_num' => $orderNum,
							'mem_no' => $this->session->userdata('member_no'),
							'mem_user_id' => $this->session->userdata('member_id'),
							'lec_no' => $join_item, // �����ڵ�
							'total_no' => $lecture_item->no, // �θ��� �ڵ�
							'mem_name' => $this->session->userdata('member_name'),
							'regi_method' => 0,
							'lec_big_cart' => $joinLectureInfo->big_cart,
							'mem_middle_cart' => $joinLectureInfo->middle_cart,
							'mem_lec_name' => $joinLectureInfo->lec_name,
							'real_price' => ($joinLectureInfo->lec_real_price != '') ? $joinLectureInfo->lec_real_price : $joinLectureInfo->real_price,
							'total_price' => 0,
							'one_kang_price' => 0,
							'discount_price' => '',
							'discount_div' => '',
							'discount_detail' => '',
							'discount_etc' => '',
							'discount_kind' => '',
							// 'discount_kind' => '{$_GET['event_sno']}', // �̺�Ʈ�ڵ庰 ���ϱݾ� ���� ����
							'total_term' => ($joinLectureInfo->lec_term != '') ? (int)$joinLectureInfo->lec_term : '' ,
							'lec_state' => 0,
							'book_sale' => '',
							'down_count' => 0,
							'once_stop_su' => $stop_cnt,
							'wdate' => date('Y:m:d'),
							'tdate' => date('Y:m:d-H:i:s'),
						);
						$this->haksa2080->insert('lecture_mem', $joinLectureInsert);
					}
				}
			}
		}
	}
	
	
	## ������ �ֹ���ȣ �θ��� ��ȸ
	function getOrderNumData($member_no, $member_id, $orderNum, $lecNo)
	{
		$sql = "
				SELECT *
				FROM lecture_mem
				WHERE order_num = '". $orderNum ."' AND total_no = '0' AND mem_no = '". $member_no ."' AND mem_user_id = '". $member_id ."' AND lec_no = '". $lecNo ."'
				";
		
		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
	
	## ������ �ֹ���ȣ �θ��� ��ȸ
	function getTradIncParentData($member_no, $orderNum, $lecNo)
	{
		$sql = "
				SELECT * FROM lecture_mem
				WHERE order_num = '". $orderNum ."' AND total_no = '0' AND mem_no = '". $member_no ."' AND lec_no = '". $lecNo ."'
				";
		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
	
	## ������ �ֹ���ȣ �ڽİ��� ��ȸ
	function getTradIncJoinData($member_no, $order_num, $lecNo)
	{
		$sql = "
				SELECT * FROM lecture_mem
				WHERE order_num = '". $order_num ."' AND mem_no = '". $member_no ."' AND total_no = '". $lecNo ."'
				";
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}
	
	## ���� ����Ʈ���� �ֹ���ȣ�� �θ� ���ǿ� ǥ��
	function paymentPointUpdate($order_num, $point_num, $member_id, $member_no, $lec_no)
	{
		$updateData = array(
			'discount_div' => 'P'
			,'discount_price' => $point_num
		);
		
		$updateWhere = "`order_num` = '". $order_num ."' AND total_no = '0' AND mem_user_id = '". $member_id ."' AND mem_no = '". $member_no ."' AND lec_no = '". $lec_no ."'";
		$updateRs = $this->haksa2080->update('lecture_mem', $updateData, $updateWhere);
		
		return $updateRs;
	}
	
	
	## ���� �������� �ֹ���ȣ�� �θ� ���ǿ� ǥ��
	function paymentCouponUpdate($order_num, $coupon_num, $member_id, $member_no, $lec_no)
	{
		// �ش����� ��ȸ
		$query = $this->haksa2080->query("SELECT lucky_price, lucky_percent, cupone_number FROM auth_list WHERE user_id = '". $member_id ."' AND user_no = '". $member_no ."' AND cupone_number = '". $coupon_num ."'");
		$couponList = $query->row();
		
		$updateData = array();
		if ($couponList->lucky_percent != 0) {
			$updateData['discount_div'] = 'C';
			$updateData['discount_percent'] = $couponList->lucky_percent;
			$updateData['discount_etc'] = $couponList->cupone_number;
		} else {
			$updateData['discount_div'] = 'C';
			$updateData['discount_price'] = $couponList->lucky_price;
			$updateData['discount_etc'] = $couponList->cupone_number;
		}
		
		$updateWhere = "`order_num` = '". $order_num ."' AND total_no = '0' AND mem_user_id = '". $member_id ."' AND mem_no = '". $member_no ."' AND lec_no = '". $lec_no ."'";
		$updateRs = $this->haksa2080->update('lecture_mem', $updateData, $updateWhere);
		
		return $updateRs;
	}
	
	
	
	#############
	
	## ���� �Ϸ� ������ �������� ��������
	public function resultPaymentInfo($order_num, $member_no, $member_id)
	{
		$sql = "
				SELECT
					`no`, order_num, mem_no, mem_name, mem_user_id, lec_no, total_no, mem_lec_name, regi_method, real_price, total_price,
					total_term, lec_state, bank_name, account_num, wdate, tdate, discount_div, discount_price, discount_percent, discount_etc
				FROM lecture_mem
				WHERE order_num = '". $order_num ."' AND mem_no = '". $member_no ."' AND mem_user_id = '". $member_id ."' AND total_no = '0'
				";
		$query = $this->haksa2080->query($sql);
		return $query->row();
	}
	
	## ���� �Ϸ� ������ (payment_result = �θ�����ȸ)
	public function resultParentArray($order_num, $member_no, $member_id)
	{
		$sql = "
				SELECT
					`no`, order_num, mem_no, mem_name, mem_user_id, lec_no, total_no, mem_lec_name, regi_method, real_price, total_price,
					total_term, lec_state, bank_name, account_num, wdate, tdate, discount_div, discount_price, discount_percent, discount_etc
				FROM lecture_mem
				WHERE order_num = '". $order_num ."' AND mem_no = '". $member_no ."' AND mem_user_id = '". $member_id ."' AND total_no = '0'
				";
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}
	
	## ���� �Ϸ� ������ (payment_result = �ڽİ��� ����)
	public function resultJoinLectureArray($order_num, $member_no, $member_id, $lec_no)
	{
		$sql = "
				SELECT
					`no`, order_num, mem_no, mem_name, mem_user_id, lec_no, total_no, mem_lec_name, regi_method, real_price, total_price,
					total_term, lec_state, bank_name, account_num, wdate, tdate
				FROM lecture_mem
				WHERE order_num = '". $order_num ."' AND total_no = '". $lec_no ."' AND mem_no = '". $member_no ."' AND mem_user_id = '". $member_id ."'
				";
		$query = $this->haksa2080->query($sql);
		return $query->result_array();
	}
	
	## �ֹ���ȣ �������� pay_method -> regi_method update
	public function lectureMemRegiMethod($member_no, $member_id, $order_num, $regi_method, $champ_person, $lmno, $total_price)
	{
		## order_num Update
		$updateData = array(
			'regi_method' => $regi_method
			,'champ_person' => $champ_person // �׽�Ʈ���� ���°�
		);
		$updateWhere = "order_num = '". $order_num ."' AND mem_no = '". $member_no ."' AND mem_user_id = '". $member_id ."'";
		$this->haksa2080->update('lecture_mem', $updateData, $updateWhere);
		
		## �θ��� Update
		$updateData = array(
			'total_price' => $total_price
			,'LGD_CLOSEDATE' => ($regi_method == '2') ? date("YmdHis", strtotime("+7 day", strtotime(date("Y-m-d H:i:s")))) : '' // �������Աݽ� �Աݱ���
		);
		
		$updateWhere = "order_num = '". $order_num ."' AND mem_no = '". $member_no ."' AND mem_user_id = '". $member_id ."' AND `no` = '". $lmno ."'";
		return $this->haksa2080->update('lecture_mem', $updateData, $updateWhere);
	}
	
	## ���� �ѻ�ǰ�ݾ� ��ȸ
	public function lectureMemRealPrice($order_num, $member_no, $member_id)
	{
		$query = $this->haksa2080->query("SELECT SUM(real_price) AS real_price FROM lecture_mem WHERE order_num = '". $order_num ."' AND mem_no = '". $member_no ."' AND mem_user_id = '". $member_id ."' AND total_no = '0'");
		return $query->row();
	}
	
	## ���������ݾ� ��ȸ
	public function lectureMemTotalPrice($order_num, $member_no, $member_id)
	{
		$query = $this->haksa2080->query("SELECT SUM(total_price) AS total_price FROM lecture_mem WHERE order_num = '". $order_num ."' AND mem_no = '". $member_no ."' AND mem_user_id = '". $member_id ."' AND total_no = '0'");
		return $query->row();
	}
	
	/**
	 * ���� 3�ܰ� : LGU+����â ���� ����ó��
	 */
	public function set_LGU_data($lecture_mem, $member_info, $getPost, $total_realPrice)
	{
		## �ʼ��� Ȯ��
		if ($this->session->userdata('member_no') == '' || $this->session->userdata('member_id') == '') {
			echo "<script>alert('�α����� �ʿ��� �������Դϴ�. �α����� �̿����ּ���.'); location.href='/login/main?return_url=' + location.pathname</script>";
		}
		
		$payReqMap['returnUrl'] = '/payment/main?lec_no=' . $getPost['lec_no'] . '&orderNum=' . $getPost['order_num'];
		
		/**********************************
		 * =====���� ���=====
		 * test : �׽�Ʈ����
		 * service : �ǰ������
		 ***********************************/
		$testId = explode("|",PAYADMIN);
		$testDomain = array(
			'mq.haksa2080.com'
			,'ml.haksa2080.com'
		);
		
		if (in_array($this->session->userdata('member_id'), $testId) || in_array($_SERVER['HTTP_HOST'], $testDomain)){
			$HTTP_POST_VARS["CST_PLATFORM"] = "test";
		} else {
			$HTTP_POST_VARS["CST_PLATFORM"] = "service";
		}
		
		/* ���� ���� ���� */
		$customtype = ($getPost['pay_method'] == '1') ? 'SC0010' : 'SC0040'; // $pay_method [ 1:ī�� , 2:������ ]
		$HTTP_POST_VARS["CST_MID"] = "edu20802"; // �������� ID
		
		$HTTP_POST_VARS["LGD_OID"] = (!empty($getPost['order_num'])) ? $getPost['order_num'] : $lecture_mem['0']->order_num ;
		$HTTP_POST_VARS["LGD_AMOUNT"] = $total_realPrice->total_price;
		$HTTP_POST_VARS["LGD_BUYER"] = ($this->session->userdata('member_name') != "") ? $this->session->userdata('member_name') : $member_info->name ; // �������̸�
		$HTTP_POST_VARS["LGD_BUYERID"] = ($this->session->userdata('member_no') != "") ? $this->session->userdata('member_no') : $member_info->no ; // ������ mem_no
		$HTTP_POST_VARS["LGD_BUYEREMAIL"] = $member_info->email; // ������ �̸���
		$HTTP_POST_VARS["LGD_BUYERADDRESS"] = (!empty($getPost['home_address'])) ? $getPost['home_address'] . $getPost['tail_address'] . '(' . $getPost['uno_new'] .')' : $member_info->home_address . $member_info->hobby . '(' . $member_info->uno_new . ')'; // ������ �ּ�
		$HTTP_POST_VARS["LGD_BUYERPHONE"] = (!empty($getPost['phone_mobi'])) ? $getPost['phone_mobi'] : $member_info->handphone_index; // ������ ��ȭ��ȣ
		$HTTP_POST_VARS["LGD_BUYERSSN"] = $member_info->new_birth; // ������ �ֹι�ȣ
		$HTTP_POST_VARS["LGD_PRODUCTCODE"] = $getPost['lec_no']; // ���� �����ڵ�
		$HTTP_POST_VARS["LGD_RECEIVER"] = $member_info->name; // ������
		$HTTP_POST_VARS["LGD_RECEIVERPHONE"] = $member_info->handphone; // ������ ��ȭ��ȣ
		$HTTP_POST_VARS["LGD_DELIVERYINFO"] = (!empty($getPost['uno_new'])) ? $getPost['uno_new'] : $member_info->uno_new; // ������ �ּ�
		$HTTP_POST_VARS["LGD_PRODUCTINFO"] = ((int)count($lecture_mem) >= 2) ? $lecture_mem['0']->mem_lec_name . ' ��' . (count($lecture_mem) - 1) . '��' : $lecture_mem['0']->mem_lec_name;
		
		$HTTP_POST_VARS["LGD_BUYERIP"] = $_SERVER['REMOTE_ADDR'];
		$HTTP_POST_VARS["LGD_CUSTOM_FIRSTPAY"] = $customtype; //�����ؾ��� ���� �� �ּ� ����
		
		// �鼼��
		// $HTTP_POST_VARS["LGD_TAXFREEAMOUNT"] = ($data["vat_type"] == 2 || $data["vat_type"] ==4 ) ? $data['tot_price'] : "";
		
		/* �������(������) �Ա� ���� �ð� ���� �����Ϸκ��� 7�� ���ı���*/
		$LGD_CLOSEDATE = date("YmdHis", strtotime("+7 day", strtotime(date("Y-m-d H:i:s"))));
		
		/*
		 * [���� ������û ������(STEP2-1)]
		 *
		 * ���������������� �⺻ �Ķ���͸� ���õǾ� ������, ������ �ʿ��Ͻ� �Ķ���ʹ� �����޴����� �����Ͻþ� �߰� �Ͻñ� �ٶ��ϴ�.
		 */
		
		/*
		 * 1. �⺻���� ������û ���� ����
		 *
		 * �⺻������ �����Ͽ� �ֽñ� �ٶ��ϴ�.(�Ķ���� ���޽� POST�� ����ϼ���)
		 */
		
		/***********************************/
		$CST_PLATFORM               = $HTTP_POST_VARS["CST_PLATFORM"];      //LG���÷��� ���� ���� ����(test:�׽�Ʈ, service:����)
		$CST_MID                    = $HTTP_POST_VARS["CST_MID"];           //�������̵�(LG���÷������� ���� �߱޹����� �������̵� �Է��ϼ���)
		//�׽�Ʈ ���̵�� 't'�� �ݵ�� �����ϰ� �Է��ϼ���.
		$LGD_MID                    = (("test" == $CST_PLATFORM) ? "t" : "").$CST_MID;  //�������̵�(�ڵ�����)
		$LGD_OID                    = $HTTP_POST_VARS["LGD_OID"];           //�ֹ���ȣ(�������� ����ũ�� �ֹ���ȣ�� �Է��ϼ���)
		$LGD_AMOUNT                 = $HTTP_POST_VARS["LGD_AMOUNT"];        //�����ݾ�("," �� ������ �����ݾ��� �Է��ϼ���)
		$LGD_TAXFREEAMOUNT			= $HTTP_POST_VARS["LGD_TAXFREEAMOUNT"]; //�鼼�ݾ�
		
		$LGD_BUYER                  = $HTTP_POST_VARS["LGD_BUYER"];         			// �����ڸ�
		$LGD_BUYERID                = $HTTP_POST_VARS["LGD_BUYERID"];       			// ������ ���̵�
		$LGD_PRODUCTINFO            = $HTTP_POST_VARS["LGD_PRODUCTINFO"];   			// ��ǰ��
		$LGD_BUYERADDRESS        	= $HTTP_POST_VARS["LGD_BUYERADDRESS"];        		// ������ �ּ�
		$LGD_BUYERPHONE          	= $HTTP_POST_VARS["LGD_BUYERPHONE"];          		// ������ ��ȭ��ȣ
		$LGD_BUYEREMAIL          	= $HTTP_POST_VARS["LGD_BUYEREMAIL"];          		// ������ �̸���
		$LGD_BUYERSSN            	= $HTTP_POST_VARS["LGD_BUYERSSN"];            		// ������ �ֹι�ȣ
		$LGD_RECEIVER            	= $HTTP_POST_VARS["LGD_RECEIVER"];            		// ������
		$LGD_RECEIVERPHONE      	= $HTTP_POST_VARS["LGD_RECEIVERPHONE"];        		// ������ ��ȭ��ȣ
		$LGD_DELIVERYINFO        	= $HTTP_POST_VARS["LGD_DELIVERYINFO"];        		// �����
		$LGD_PRODUCTCODE			= $HTTP_POST_VARS["LGD_PRODUCTCODE"];				// ���� �����ڵ�
		
		
		$LGD_CUSTOM_FIRSTPAY        = $HTTP_POST_VARS["LGD_CUSTOM_FIRSTPAY"];    //�������� �ʱ��������
		$LGD_TIMESTAMP              = date('YmdHms');                         //Ÿ�ӽ�����
		$LGD_CUSTOM_SKIN            = "blue";                        //�������� ����â ��Ų
		$LGD_MERTKEY				= "a10bdbec2b1f22eec8dcb63ba7feeb3e";	//����� �������̵𲬷� �ٲ� �� �ּ� ����
		$configPath 				= $_SERVER["DOCUMENT_ROOT"]."/application/views/payment/LGU/lgdacom";  						//LG���÷������� ������ ȯ������("/conf/lgdacom.conf") ��ġ ����.
		
		/*
		 * �������(������) ���� ������ �Ͻô� ��� �Ʒ� LGD_CASNOTEURL �� �����Ͽ� �ֽñ� �ٶ��ϴ�.
		 */
		$LGD_CASNOTEURL				= "https://".$_SERVER['HTTP_HOST']."/payment/payment_lgu/cas_noteurl";
		
		/*
		 * LGD_RETURNURL �� �����Ͽ� �ֽñ� �ٶ��ϴ�. �ݵ�� ���� �������� ������ ����Ʈ�� ��  ȣ��Ʈ�̾�� �մϴ�. �Ʒ� �κ��� �ݵ�� �����Ͻʽÿ�.
		 */
		$LGD_RETURNURL				= "//".$_SERVER['HTTP_HOST']."/payment/payment_lgu/returnurl";
		// $LGD_RETURNURL				= "//".$_SERVER['HTTP_HOST']."/payment/main?pay_type=" . $_SESSION['PAYREQ_MAP']['pay_type'] . '&lec_no=' . $_SESSION['PAYREQ_MAP']['lec_no'] . '&orderNum=' . $_SESSION['PAYREQ_MAP']['order_num'];
		
		/*
		 * ISP ī����� ������ �����ISP���(�������� ���������ʴ� �񵿱���)�� ���, LGD_KVPMISPNOTEURL/LGD_KVPMISPWAPURL/LGD_KVPMISPCANCELURL�� �����Ͽ� �ֽñ� �ٶ��ϴ�.
		 */
		$LGD_KVPMISPNOTEURL       	= "https://".$_SERVER['HTTP_HOST']."/payment/payment_lgu/note_url";
		$LGD_KVPMISPWAPURL			= "https://".$_SERVER['HTTP_HOST']."/payment/payment_lgu/mispwapurl?LGD_OID=".$LGD_OID;   //ISP ī�� ������, URL ��� �۸� �Է½�, ��ȣ����
		$LGD_KVPMISPCANCELURL     	= "https://".$_SERVER['HTTP_HOST']."/payment/payment_lgu/cancel_url";
		
		//����ũ�� ���뿩��
//		if ($data['BSCnt'] > 0) {
//			$LGD_ESCROW_USEYN = ""; //��������
//		} else {
//			$LGD_ESCROW_USEYN = "N";
//		}
		
		/*
		 *************************************************
		 * 2. MD5 �ؽ���ȣȭ (�������� ������) - BEGIN
		 *
		 * MD5 �ؽ���ȣȭ�� �ŷ� �������� �������� ����Դϴ�.
		 *************************************************
		 *
		 * �ؽ� ��ȣȭ ����( LGD_MID + LGD_OID + LGD_AMOUNT + LGD_TIMESTAMP + LGD_MERTKEY )
		 * LGD_MID          : �������̵�
		 * LGD_OID          : �ֹ���ȣ
		 * LGD_AMOUNT       : �ݾ�
		 * LGD_TIMESTAMP    : Ÿ�ӽ�����
		 * LGD_MERTKEY      : ����MertKey (mertkey�� ���������� -> ������� -> ���������������� Ȯ���ϽǼ� �ֽ��ϴ�)
		 *
		 * MD5 �ؽ������� ��ȣȭ ������ ����
		 * LG���÷������� �߱��� ����Ű(MertKey)�� ȯ�漳�� ����(lgdacom/conf/mall.conf)�� �ݵ�� �Է��Ͽ� �ֽñ� �ٶ��ϴ�.
		 */
		require_once($_SERVER["DOCUMENT_ROOT"]."/application/views/payment/LGU/lgdacom/XPayClient.php");
		
		$xpay = &new XPayClient($configPath, $LGD_PLATFORM);
		$xpay->Init_TX($LGD_MID);
		$LGD_HASHDATA = md5($LGD_MID . $LGD_OID . $LGD_AMOUNT . $LGD_TIMESTAMP . $xpay->config[$LGD_MID]);
		$LGD_CUSTOM_PROCESSTYPE = "TWOTR";
		
		######### GIT error Ȯ��
		
		/*
		 *************************************************
		 * 2. MD5 �ؽ���ȣȭ (�������� ������) - END
		 *************************************************
		 */
		$CST_WINDOW_TYPE = "submit";                                       // �����Ұ�
		$payReqMap['CST_PLATFORM']           = $CST_PLATFORM;              // �׽�Ʈ, ���� ����
		$payReqMap['CST_WINDOW_TYPE']        = $CST_WINDOW_TYPE;           // �����Ұ�
		$payReqMap['CST_MID']                = $CST_MID;                   // �������̵�
		$payReqMap['LGD_MID']                = $LGD_MID;                   // �������̵�
		$payReqMap['LGD_OID']                = $LGD_OID;                   // �ֹ���ȣ
		$payReqMap['LGD_AMOUNT']             = $LGD_AMOUNT;                // �����ݾ�
		$payReqMap['LGD_TAXFREEAMOUNT']      = $LGD_TAXFREEAMOUNT;         // �鼼�ݾ�
		$payReqMap['LGD_CLOSEDATE']			 = $LGD_CLOSEDATE;			   // �������ɽð�
		
		
		$payReqMap['LGD_BUYER']              	= $LGD_BUYER;            	// ������
		$payReqMap['LGD_BUYERID']            	= $LGD_BUYERID;             // �����ھ��̵�
		$payReqMap['LGD_PRODUCTINFO']        	= $LGD_PRODUCTINFO;     	// ��ǰ����
		$payReqMap['LGD_BUYERADDRESS']        	= $LGD_BUYERADDRESS;     	// ������ �ּ�
		$payReqMap['LGD_BUYERPHONE']        	= $LGD_BUYERPHONE;     	   	// ������ ��ȭ��ȣ
		$payReqMap['LGD_BUYEREMAIL']         	= $LGD_BUYEREMAIL;          // ������ �̸���
		$payReqMap['LGD_BUYERSSN']        		= $LGD_BUYERSSN;     	   	// ������ �ֹι�ȣ
		$payReqMap['LGD_PRODUCTCODE']		 	= $LGD_PRODUCTCODE;			// �����ڵ�
		$payReqMap['LGD_RECEIVER']        		= $LGD_RECEIVER;     	   	// ������
		$payReqMap['LGD_RECEIVERPHONE']        	= $LGD_RECEIVERPHONE;     	// ������ ��ȭ��ȣ
		$payReqMap['LGD_DELIVERYINFO']        	= $LGD_DELIVERYINFO;     	// �����
		
		
		$payReqMap['LGD_CUSTOM_SKIN']        = $LGD_CUSTOM_SKIN;           // ����â SKIN
		$payReqMap['LGD_CUSTOM_PROCESSTYPE'] = $LGD_CUSTOM_PROCESSTYPE;    // Ʈ����� ó�����
		$payReqMap['LGD_TIMESTAMP']          = $LGD_TIMESTAMP;             // Ÿ�ӽ�����
		$payReqMap['LGD_HASHDATA']           = $LGD_HASHDATA;              // MD5 �ؽ���ȣ��
		$payReqMap['LGD_RETURNURL']   		 = $LGD_RETURNURL;      	   // �������������
		$payReqMap['LGD_VERSION']         	 = "PHP_SmartXPay_1.0";		   // �������� (�������� ������)
		$payReqMap['LGD_CUSTOM_FIRSTPAY']  	 = $LGD_CUSTOM_FIRSTPAY;	   // ����Ʈ ��������
		$payReqMap['LGD_CUSTOM_SWITCHINGTYPE']  = "SUBMIT";	       		   // �ſ�ī�� ī��� ���� ������ ���� ���
		$payReqMap['LGD_ESCROW_USEYN']       =  $LGD_ESCROW_USEYN;         // ����ũ�� ��뿩��
		/*
		****************************************************
		* �ȵ���̵��� �ſ�ī�� ISP(����/BC)�������� ���� (����)*
		****************************************************

		(����)LGD_CUSTOM_ROLLBACK �� ����  "Y"�� �ѱ� ���, LG U+ ���ڰ������� ���� ISP(����/��) ���������� �������� note_url���� ���Ž�  "OK" ������ �ȵǸ�  �ش� Ʈ�������  ������ �ѹ�(�ڵ����)ó���ǰ�,
		LGD_CUSTOM_ROLLBACK �� �� �� "C"�� �ѱ� ���, �������� note_url���� "ROLLBACK" ������ �� ���� �ش� Ʈ�������  �ѹ�ó���Ǹ�  �׿��� ���� ���ϵǸ� ���� ���οϷ� ó���˴ϴ�.
		����, LGD_CUSTOM_ROLLBACK �� ���� "N" �̰ų� null �� ���, �������� note_url����  "OK" ������  �ȵɽ�, "OK" ������ �� ������ 3�а������� 2�ð�����  ���ΰ���� �������մϴ�.
		*/
		
		$payReqMap['LGD_CUSTOM_ROLLBACK']    = "";			   	   				     // �񵿱� ISP���� Ʈ����� ó������
		$payReqMap['LGD_KVPMISPNOTEURL']  	 = $LGD_KVPMISPNOTEURL;			   // �񵿱� ISP(ex. �ȵ���̵�) ���ΰ���� �޴� URL
		$payReqMap['LGD_KVPMISPWAPURL']  	 = $LGD_KVPMISPWAPURL;			   // �񵿱� ISP(ex. �ȵ���̵�) ���οϷ��� ����ڿ��� �������� ���οϷ� URL
		$payReqMap['LGD_KVPMISPCANCELURL']   = $LGD_KVPMISPCANCELURL;		   // ISP �ۿ��� ��ҽ� ����ڿ��� �������� ��� URL
		
		/*
		****************************************************
		* �ȵ���̵��� �ſ�ī�� ISP(����/BC)�������� ����    (��) *
		****************************************************
		*/
		// �ȵ���̵� ���� �ſ�ī�� ����  ISP(����/BC)�������� ���� (����)
		// $payReqMap['LGD_KVPMISPAUTOAPPYN'] = "Y";
		// Y: �ȵ���̵忡�� ISP�ſ�ī�� ������, ���翡�� 'App To App' ������� ����, BCī��翡�� ���� ���� ������ �ް� ������ ���� �����ϰ��� �Ҷ� ���
		
		// �������(������) ���������� �Ͻô� ���  �Ҵ�/�Ա� ����� �뺸�ޱ� ���� �ݵ�� LGD_CASNOTEURL ������ LG ���÷����� �����ؾ� �մϴ� .
		$payReqMap['LGD_CASNOTEURL'] = $LGD_CASNOTEURL;               // ������� NOTEURL
		
		//Return URL���� ���� ��� ���� �� ���õ� �Ķ���� �Դϴ�.*/
		$payReqMap['LGD_RESPCODE']           = "";
		$payReqMap['LGD_RESPMSG']            = "";
		$payReqMap['LGD_PAYKEY']             = "";
		
		// $_SESSION['ev'] = $evtCode;
		$_SESSION['PAYREQ_MAP'] = $payReqMap;
		
		return $payReqMap;
	}
	
	
	/**
	 * ���� ������� ó�� ����
	 */
	function payres_model()
	{
		/*
		 * [����������û ������(STEP2-2)]
		 *
		 * LG���÷������� ���� �������� LGD_PAYKEY(����Key)�� ������ ���� ������û.(�Ķ���� ���޽� POST�� ����ϼ���)
		 */
		
		/* �� �߿�
		* ȯ�漳�� ������ ��� �ݵ�� �ܺο��� ������ ������ ��ο� �νø� �ȵ˴ϴ�.
		* �ش� ȯ�������� �ܺο� ������ �Ǵ� ��� ��ŷ�� ������ �����ϹǷ� �ݵ�� �ܺο��� ������ �Ұ����� ��ο� �νñ� �ٶ��ϴ�.
		* ��) [Window �迭] C:\inetpub\wwwroot\lgdacom ==> ����Ұ�(�� ���丮)
		*/
		
		$configPath = $_SERVER["DOCUMENT_ROOT"]."/application/views/payment/LGU/lgdacom";  //LG���÷������� ������ ȯ������("/conf/lgdacom.conf,/conf/mall.conf") ��ġ ����.
		
		$CST_PLATFORM				= $_POST["CST_PLATFORM"];
		$CST_MID                    = $_POST["CST_MID"];
		$LGD_MID                    = (("test" == $CST_PLATFORM) ? "t" : "") . $CST_MID;
		$LGD_PAYKEY                 = $_POST["LGD_PAYKEY"];
		
		require_once($_SERVER["DOCUMENT_ROOT"]."/application/views/payment/LGU/lgdacom/XPayClient.php");
		$xpay = &new XPayClient($configPath, $CST_PLATFORM);
		$xpay->Init_TX($LGD_MID);
		
		$xpay->Set("LGD_TXNAME", "PaymentByKey");
		$xpay->Set("LGD_PAYKEY", $LGD_PAYKEY);
		//�ݾ��� üũ�Ͻñ� ���ϴ� ��� �Ʒ� �ּ��� Ǯ� �̿��Ͻʽÿ�.
		//$DB_AMOUNT = "DB�� ���ǿ��� ������ �ݾ�"; //�ݵ�� �������� �Ұ����� ��(DB�� ����)���� �ݾ��� �������ʽÿ�.
		//$xpay->Set("LGD_AMOUNTCHECKYN", "Y");
		//$xpay->Set("LGD_AMOUNT", $DB_AMOUNT);
		
		/*
		*************************************************
		* 1.�������� ��û(�������� ������) - END
		*************************************************
		*/
		/*
		* 2. �������� ��û ���ó��
		*
		* ���� ������û ��� ���� �Ķ���ʹ� �����޴����� �����Ͻñ� �ٶ��ϴ�.
		*/
		if ($xpay->TX()) {
			//1)������� ȭ��ó��(����,���� ��� ó���� �Ͻñ� �ٶ��ϴ�.)
			$LGD_RESPCODE				= $xpay->Response_Code();
			$LGD_RESPMSG				= $xpay->Response_Msg();
			$LGD_MID					= $xpay->Response("LGD_MID",0);
			$LGD_OID					= $xpay->Response("LGD_OID",0);
			$LGD_AMOUNT					= $xpay->Response("LGD_AMOUNT",0);
			$LGD_TAXFREEAMOUNT			= $xpay->Response("LGD_TAXFREEAMOUNT",0);
			$LGD_TID					= $xpay->Response("LGD_TID",0);
			$LGD_PAYTYPE				= $xpay->Response("LGD_PAYTYPE",0);
			$LGD_PAYDATE				= $xpay->Response("LGD_PAYDATE",0);
			$LGD_HASHDATA				= $xpay->Response("LGD_HASHDATA",0);
			$LGD_FINANCECODE			= $xpay->Response("LGD_FINANCECODE",0);
			$LGD_FINANCENAME			= $xpay->Response("LGD_FINANCENAME",0);
			$LGD_ESCROWYN				= $xpay->Response("LGD_ESCROWYN",0);
			$LGD_TIMESTAMP				= $xpay->Response("LGD_TIMESTAMP",0);
			$LGD_ACCOUNTNUM				= $xpay->Response("LGD_ACCOUNTNUM",0);
			$LGD_CASTAMOUNT				= $xpay->Response("LGD_CASTAMOUNT",0);
			$LGD_CASCAMOUNT				= $xpay->Response("LGD_CASCAMOUNT",0);
			$LGD_CASFLAG				= $xpay->Response("LGD_CASFLAG",0);
			$LGD_CASSEQNO				= $xpay->Response("LGD_CASSEQNO",0);
			$LGD_CASHRECEIPTNUM			= $xpay->Response("LGD_CASHRECEIPTNUM",0);
			$LGD_CASHRECEIPTSELFYN		= $xpay->Response("LGD_CASHRECEIPTSELFYN",0);
			$LGD_CASHRECEIPTKIND		= $xpay->Response("LGD_CASHRECEIPTKIND",0);
			$LGD_PAYER					= $xpay->Response("LGD_PAYER",0);
			
			$LGD_BUYER					= $xpay->Response("LGD_BUYER",0);
			$LGD_PRODUCTINFO			= $xpay->Response("LGD_PRODUCTINFO",0);
			$LGD_BUYERID				= $xpay->Response("LGD_BUYERID",0);
			$LGD_BUYERADDRESS			= $xpay->Response("LGD_BUYERADDRESS",0);
			$LGD_BUYERPHONE				= $xpay->Response("LGD_BUYERPHONE",0);
			$LGD_BUYEREMAIL				= $xpay->Response("LGD_BUYEREMAIL",0);
			$LGD_BUYERSSN				= $xpay->Response("LGD_BUYERSSN",0);
			$LGD_PRODUCTCODE			= $xpay->Response("LGD_PRODUCTCODE",0);
			$LGD_RECEIVER				= $xpay->Response("LGD_RECEIVER",0);
			$LGD_RECEIVERPHONE			= $xpay->Response("LGD_RECEIVERPHONE",0);
			$LGD_DELIVERYINFO			= $xpay->Response("LGD_DELIVERYINFO",0);
			
			// ������� �α� (LGU �α� insert)
			$log_array = array(
				'LGD_MID' => $LGD_MID,
				'LGD_OID' => $LGD_OID,
				'LGD_TID' => $LGD_TID,
				'LGD_BUYERID' => $LGD_BUYERID,
				'LGD_PRODUCTCODE' => $LGD_PRODUCTCODE,
				'LGD_PAYTYPE' => $LGD_PAYTYPE,
				'LGD_ESCROWYN' => $LGD_ESCROWYN,
				'LGD_AMOUNT' => $LGD_AMOUNT,
				'LGD_RESPCODE' => $LGD_RESPCODE,
				'LGD_RESPMSG' => $LGD_RESPMSG,
				'Reg_DT' => date('Y-m-d H:i:s')
			);
			$result = $this->haksa2080->insert("Payment_Log", $log_array);
			
			if( "0000" == $xpay->Response_Code() ) { // �������� ����
				
				// ����������û ��� ���� DBó��
				echo "����������û ��� ���� DBó���Ͻñ� �ٶ��ϴ�.<br>";
				
				$isDBOK = true; // DBó�� ���н� false�� ������ �ּ���.
				
				switch($LGD_PAYTYPE) {
					## ������ ����
					case 'SC0040' :
						// �������Աݽ� �������� update
						$updateData = array(
							'account_num' => $LGD_ACCOUNTNUM
							,'bank_name' => $LGD_FINANCENAME
							,'receiptnumber' => $LGD_CASHRECEIPTNUM
						);
						$updateWhere = "`order_num` = '". $LGD_OID ."' AND total_no = '0' AND mem_no = '". $LGD_BUYERID ."'";
						$this->haksa2080->update('lecture_mem', $updateData, $updateWhere);
						
						echo "<script type='text/javascript'>top.location.href='/payment/payment_lgu/pay_result?order_num=" . $LGD_OID . "'</script>";
						break;
					
					## ī�����
					case 'SC0010' :
						## trade_inc (�����Ϸ�� DB ó��)
						
						$this->trade_inc($CST_PLATFORM, $LGD_OID, $LGD_PAYTYPE, $LGD_BUYER, $LGD_BUYERID, $LGD_AMOUNT, $LGD_PLATFORM, $LGD_ESCROWYN, $LGD_CASHRECEIPTNUM, $LGD_TID, $LGD_BUYERPHONE, $LGD_BUYERADDRESS, $LGD_PRODUCTCODE);
						
						// ����Ȯ������ INSERT
						$hcPay_set = array(
							"tid"	=> $xpay->Response("LGD_TID",0),
							"pay_valid_yn"	=> "Y",
							"pay_final_yn"	=> "Y",
							"amount"	=> $LGD_AMOUNT,
							"financename"	=> $LGD_FINANCENAME,
							"financecode"	=> $LGD_FINANCECODE,
							"financeauthnum"=> $xpay->Response("LGD_FINANCEAUTHNUM",0),
							"pay_stat"	=> "1",
							"paytype"	=> "SC0010",
							"paydate"	=> $LGD_PAYDATE,
							"pay_src_type"	=> '2',
							"buyer"	=> $LGD_BUYER,
							"buyerid"	=> $LGD_BUYERID,
							"buyeremail"	=> $LGD_BUYEREMAIL,
							"buyerphone"	=> $LGD_BUYERPHONE,
							"reg_date"	=> $LGD_PAYDATE,
							// "reg_id"	=> $_SESSION[zb_logged_user_id],
							"reg_id"	=> ($this->session->userdata('member_id') != '') ? $this->session->userdata('member_id') : '' ,
							// "reg_name"	=> $_SESSION[zb_logged_name],
							"reg_name"	=> ($this->session->userdata('member_name') != '') ? $this->session->userdata('member_name') : '',
							"site_code"	=> '1',
							"mid"	=> $LGD_MID,
							"oid"	=> $LGD_OID,
							"cardacquirer"	=> $LGD_FINANCECODE,
							"cardnointyn"	=> $xpay->Response("LGD_CARDNOINTYN",0),
							"cardinstallmonth"	=> $xpay->Response("LGD_CARDINSTALLMONTH",0),
							"cardnum"	=> $xpay->Response("LGD_CARDNUM",0),
							"respmsg"	=> $xpay->Response("LGD_RESPMSG",0),
							"respcode"	=> $xpay->Response("LGD_RESPCODE",0),
							"exchangerate"	=> $xpay->Response("LGD_EXCHANGERATE",0),
							"ref_id"	=> "�ֹ���ȣ",
							"ref_type"	=> "1",
							"mod_id"	=> $xpay->Response("LGD_PAYER",0),
							"escrowyn"	=> "N",
							"hashdata"	=> $xpay->Response("LGD_HASHDATA",0),
							"transamount"	=> $xpay->Response("LGD_TRANSAMOUNT",0)
						);
						
						$isDBOK = $this->haksa2080->insert('hc_pay_log', $hcPay_set);
						
						// ����������û ��� ���� DBó�� ���н� Rollback ó��
						if( !$isDBOK ) {
							echo "<p>";
							$xpay->Rollback("���� DBó�� ���з� ���Ͽ� Rollback ó�� [TID:" . $xpay->Response("LGD_TID",0) . ",MID:" . $xpay->Response("LGD_MID",0) . ",OID:" . $xpay->Response("LGD_OID",0) . "]");
							
							echo "TX Rollback Response_code = " . $xpay->Response_Code() . "<br>";
							echo "TX Rollback Response_msg = " . $xpay->Response_Msg() . "<p>";
							
							if( "0000" == $xpay->Response_Code() ) {
								echo "<script>alert('�Ͻ����� ��Ʈ��ũ ��ַ� ���� ������ �߻� �Ͽ����ϴ�. ������ �ٽ� �õ� ���ּ���.');top.location.href='/lecture/lecture_view?lec_no=".$LGD_PRODUCTCODE."';</script>";
								$this->champ->set("LGD_RESPMSG", '���� �ڵ���� ����');
								$this->champ->where("LGD_OID", $LGD_OID);
								$this->champ->update("Payment_Log");
							} else {
								echo "<script>alert('�Ͻ����� ��Ʈ��ũ ��ַ� ���� ������ �߻� �Ͽ����ϴ�. ������ �ٽ� �õ� ���ּ���.');top.location.href='/lecture/lecture_view?lec_no=".$LGD_PRODUCTCODE."';</script>";
								$this->champ->set("LGD_RESPMSG", '���� �ڵ���� ����');
								$this->champ->where("LGD_OID", $LGD_OID);
								$this->champ->update("Payment_Log");
							}
						} else { // ���� ���� ����
							echo "<script type='text/javascript'>top.location.href='/payment/payment_lgu/pay_result?order_num=" . $LGD_OID . "'</script>";
						}
						break;
				}
				
			} else {
				// ����������û ��� ���� DBó��
				echo ("<script>alert('�߸��� �����Դϴ�. ������������ ���ư��ϴ�.'); location.href ='/';</script>");
			}
		} else {
			//2) API ��û���� ȭ��ó��
			echo "������û�� �����Ͽ����ϴ�.  <br>";
			echo "TX Response_code = " . $xpay->Response_Code() . "<br>";
			echo "TX Response_msg = " . $xpay->Response_Msg() . "<p>";
			
			// ����������û ��� ���� DBó��
			echo "����������û ��� ���� DBó���Ͻñ� �ٶ��ϴ�.<br>";
		}
	}
	
	/**
	 * ������ ó�� ����
	 */
	function cas_noteurl_model($LGD_RESPCODE,$LGD_RESPMSG,$LGD_MID,$LGD_OID,$LGD_AMOUNT,$LGD_TID,$LGD_PAYTYPE,$LGD_PAYDATE,$LGD_HASHDATA,$LGD_FINANCECODE,$LGD_FINANCENAME,$LGD_ESCROWYN,$LGD_TIMESTAMP,$LGD_ACCOUNTNUM,$LGD_CASTAMOUNT,$LGD_CASCAMOUNT,$LGD_CASFLAG,$LGD_CASSEQNO,$LGD_CASHRECEIPTNUM,$LGD_CASHRECEIPTSELFYN,$LGD_CASHRECEIPTKIND,$LGD_PAYER,$LGD_BUYER,$LGD_PRODUCTINFO,$LGD_BUYERID,$LGD_BUYERADDRESS,$LGD_BUYERPHONE,$LGD_BUYEREMAIL,$LGD_BUYERSSN,$LGD_PRODUCTCODE,$LGD_RECEIVER,$LGD_RECEIVERPHONE,$LGD_DELIVERYINFO)
	{
		$LGD_MERTKEY = "a10bdbec2b1f22eec8dcb63ba7feeb3e";  // LG�ڷ��޿��� �߱��� ����Ű�� ������ �ֽñ� �ٶ��ϴ�.
		$LGD_HASHDATA2 = md5($LGD_MID.$LGD_OID.$LGD_AMOUNT.$LGD_RESPCODE.$LGD_TIMESTAMP.$LGD_MERTKEY);
		
		/*
		 * ���� ó����� ���ϸ޼���
		 *
		 * OK  : ���� ó����� ����
		 * �׿� : ���� ó����� ����
		 *
		 * �� ���ǻ��� : ������ 'OK' �����̿��� �ٸ����ڿ��� ���ԵǸ� ����ó�� �ǿ��� �����Ͻñ� �ٶ��ϴ�.
		 */
		$resultMSG = "������� ���� DBó��(LGD_CASNOTEURL) ������� �Է��� �ֽñ� �ٶ��ϴ�.";
		
		// ������� �α�
		$log_array = array(
			'LGD_MID' => $LGD_MID,
			'LGD_OID' => $LGD_OID,
			'LGD_TID' => $LGD_TID,
			'LGD_BUYERID' => $LGD_BUYERID,
			'LGD_PRODUCTCODE' => $LGD_PRODUCTCODE,
			'LGD_PAYTYPE' => $LGD_PAYTYPE,
			'LGD_ESCROWYN' => $LGD_ESCROWYN,
			'LGD_AMOUNT' => $LGD_AMOUNT,
			'LGD_RESPCODE' => $LGD_RESPCODE,
			'LGD_RESPMSG' => $LGD_RESPMSG,
			'Reg_DT' => date('Y-m-d H:i:s')
		);
		$result = $this->haksa2080->insert("Payment_Log", $log_array);
		
		if ( $LGD_HASHDATA2 == $LGD_HASHDATA ) { // �ؽ��� ������ �����̸�
			if ( "0000" == $LGD_RESPCODE ) { // ������ �����̸�
				if( "R" == $LGD_CASFLAG ) { // �������Ա� �÷���(�������Ա�) - 'R':�����Ҵ�, 'I':�Ա�, 'C':�Ա����
					$after_check = ($LGD_ESCROWYN == 'Y') ? 'e' : '0' ;
					
					$updateData = array(
						'account_num' => $LGD_ACCOUNTNUM
						,'bank_name' => $LGD_FINANCENAME
						// ,'regi_method' => 2
						,'receiptnumber' => $LGD_CASHRECEIPTNUM
						,'after_check' => $after_check
					);
					$updateWhere = "`order_num` = '". $LGD_OID ."' AND mem_no = '". $LGD_BUYERID ."'";
					$this->haksa2080->update('lecture_mem', $updateData, $updateWhere);
					
					/*
					 * ������ �Ҵ� ���� ��� ���� ó��(DB) �κ�
					 * ���� ��� ó���� �����̸� "OK"
					 */
					//if( ������ �Ҵ� ���� ����ó����� ���� )
					$resultMSG = "OK";
					
				} else if ( "I" == $LGD_CASFLAG ) { // �������Ա� �Ϸ�� �����Ϸ� ����
					
					$this->haksa2080->set("receiptnumber", $LGD_CASHRECEIPTNUM); // $LGD_CASHRECEIPTNUM == ���ݿ����� ���ι�ȣ
					$this->haksa2080->where("order_num", $LGD_OID, "total_no", 0);
					$this->haksa2080->update("lecture_mem");
					
					// �������Ա� �Ϸ�� �����Ϸ� ����
					$this->trade_inc($CST_PLATFORM, $LGD_OID, $LGD_PAYTYPE, $LGD_BUYER, $LGD_BUYERID, $LGD_AMOUNT, $LGD_PLATFORM, $LGD_ESCROWYN, $LGD_CASHRECEIPTNUM, $LGD_TID , $LGD_BUYERPHONE, $LGD_BUYERADDRESS, $LGD_PRODUCTCODE);
					
					/*
					* ������ �Ա� ���� ��� ���� ó��(DB) �κ�
					* ���� ��� ó���� �����̸� "OK"
					*/
					//if( ������ �Ա� ���� ����ó����� ���� )
					$resultMSG = "OK";
					
					########################�����Ϸ� �˸��� ���� - �ΰ�(������)###########################
					/*$sql = "SELECT handphone FROM zetyx_member_table WHERE user_id='{$LGD_BUYERID}'";
					$query = $this->champ->query($sql);
					$val = $query->row_array();

					$data = array($LGD_PRODUCTINFO);
					sendTalk($val["handphone"],'F00015',$data);*/
					#########################################################################################
					
				}else if( "C" == $LGD_CASFLAG ) {
					
					/*
					* ������ �Ա���� ���� ��� ���� ó��(DB) �κ�
					* ���� ��� ó���� �����̸� "OK"
					*/
					//if( ������ �Ա���� ���� ����ó����� ���� )
					$resultMSG = "OK";
				}
			} else { //������ �����̸�
				/*
				 * �ŷ����� ��� ���� ó��(DB) �κ�
				 * ������� ó���� �����̸� "OK"
				 */
				//if( �������� ����ó����� ���� )
				$resultMSG = "OK";
			}
		} else { //�ؽ����� ������ �����̸�
			/*
			 * hashdata���� ���� �α׸� ó���Ͻñ� �ٶ��ϴ�.
			 */
			$resultMSG = "������� ���� DBó��(LGD_CASNOTEURL) �ؽ��� ������ �����Ͽ����ϴ�.";
		}
		
		echo $resultMSG;
	}
	
	/**
	 * ���� ���� �� DBó�� ����
	 */
	function trade_inc($CST_PLATFORM, $LGD_OID, $LGD_PAYTYPE, $LGD_BUYER, $LGD_BUYERID, $LGD_AMOUNT, $LGD_PLATFORM, $LGD_ESCROWYN, $LGD_CASHRECEIPTNUM, $LGD_TID, $LGD_BUYERPHONE, $LGD_BUYERADDRESS, $LGD_PRODUCTCODE)
	{
		/* �������� Ȯ�� */
		$regi_method = $LGD_PAYTYPE;
		switch($regi_method){
			case 'SC0010': $regi_method = '1'; break;	//�ſ�ī��
			case 'SC0030': $regi_method = '3'; break;	//������ü
			case 'SC0040': $regi_method = '2'; break;	//������
			case 'SC0060': $regi_method = '5'; break;	//�޴���
			case 'SC0070': $regi_method = '0'; break;	//������ȭ����
			case 'SC0090': $regi_method = '0'; break;	//OKĳ����
			case 'SC0111': $regi_method = '0'; break;	//��ȭ��ǰ��
			case 'SC0112': $regi_method = '0'; break;	//���ӹ�ȭ��ǰ��
			case 'homeplus': $regi_method = '4'; break;	//Ȩ�÷�����ǰ��
			default : $regi_method = '0'; break;
		}
		
		
		## �ֹ������ڵ�� ��ȸ (�θ��� Array)
		$lectureMemParentInfo = array();
		$paymentLecNo = explode(',', $LGD_PRODUCTCODE); // ���Ű����ڵ� : $LGD_PRODUCTCODE
		foreach($paymentLecNo as $lecNo_key => $lecNo_item) {
			$lectureMemParentInfo[] = $this->m_payment->getTradIncParentData($LGD_BUYERID, $LGD_OID, $lecNo_item); ## �θ��ǻ���
		}
		
		## �ڽİ��� ��ȸ ����, �ǰ����ݾ� ����
		$paymentLectureName = array(); // �����Ѱ��� �̸�
		foreach($lectureMemParentInfo as $lecMem_key => $lecMem_item) {
			$paymentLectureName[] = $lecMem_item->mem_lec_name;
			
			## �ڽİ��� ��ȸ
			$lectureMemParentInfo[$lecMem_key]->join_lecture_list = $this->m_payment->getTradIncJoinData($LGD_BUYERID, $LGD_OID, $lecMem_item->lec_no);
		}
		
		## �������� ��ȸ
		$query = $this->haksa2080->query("SELECT * FROM zetyx_member_table WHERE `no` = '". $LGD_BUYERID ."'");
		$memberInfo = $query->row();
		
		## �������� start
		if (!empty($lectureMemParentInfo)) {
			
			## ������ �ֹ���ȣ�� Update
			$updateData = array(
				'regi_method' => $regi_method
				,'lec_state' => '1'
				,'wdate' => date('Y:m:d')
				,'tdate' => date('Y:m:d-H:i:s')
			);
			$updateWhere = "`order_num` = '". $LGD_OID ."' AND mem_no = '". $LGD_BUYERID ."'";
			$this->haksa2080->update('lecture_mem', $updateData, $updateWhere);
			
			## �θ� lecture_mem Update
			$updateData = array(
				'tid' => $LGD_TID
			);
			$updateWhere = "`order_num` = '". $LGD_OID ."' AND mem_no = '". $LGD_BUYERID ."' AND total_no = '0'";
			$this->haksa2080->update('lecture_mem', $updateData, $updateWhere);
			
			
			/************** ���Ű��� �屸�� ���� START ****************/
			foreach ($paymentLecNo as $lecNo_key => $lecNo_item) { // ���Ű��� �ڵ� -> ��ٱ��� ��ȸ
				$this->haksa2080->where("mem_no", $LGD_BUYERID);
				$this->haksa2080->where("lec_no", $lecNo_item);
				$this->haksa2080->delete("lecture_cart");
			}
			/************** ���Ű��� �屸�� ���� end ****************/
			
			
			/************** ���� ���Ȯ�� START ****************/
			foreach ($lectureMemParentInfo as $lecMem_key => $lecMem_item) {
				if ($lecMem_item->discount_div == 'C') {
					// �������� �ֹ���ȣ, ���°�����
					$updateData = array(
						'order_num' => $LGD_OID
						, 'use_date_temp' => date('Y:m:d')
						, 're_buy' => 3
						, 'etc' => $lecMem_item->lec_no
					);
					$updateWhere = "cupone_number = '" . $lecMem_item->discount_etc . "' AND user_no = '" . $LGD_BUYERID . "'";
					$this->haksa2080->update('auth_list', $updateData, $updateWhere);
					
					// ������� INSERT
					$this->haksa2080->query("INSERT INTO auth_list_used (SELECT * FROM auth_list WHERE cupone_number = '" . $lecMem_item->discount_etc . "' AND user_no = '" . $LGD_BUYERID . "')");
					
					// �������� DELETE
					$this->haksa2080->where("cupone_number", $lecMem_item->discount_etc);
					$this->haksa2080->where("user_no", $LGD_BUYERID);
					$this->haksa2080->delete("auth_list");
				}
			}
			/************** ���� ���Ȯ�� END ****************/
			
			
			/************** ����Ʈ ���Ȯ�� START ****************/
			foreach ($lectureMemParentInfo as $lecMem_key => $lecMem_item) {
				if ($lecMem_item->discount_div == 'P') {
					// ����Ʈ ��� ���̺� ��볻�� INSERT
					$pointUsedData = array(
						'order_num' => $LGD_OID,
						'mem_user_id' => $lecMem_item->mem_user_id,
						'lecture_no' => $lecMem_item->lec_no,
						'point_title' => $lecMem_item->mem_lec_name,
						'point' => $lecMem_item->discount_price,
						'point_term' => date('Y:m:d-H:i:s'),
						'point_state' => '1',
						'wdate' => date('Y:m:d-H:i:s')
					);
					$this->haksa2080->insert('used_point', $pointUsedData);
					
					$updateData = array(
						'usepoint' => ((int)$memberInfo->usepoint - (int)$lecMem_item->discount_price)
					);
					$updateWhere = "user_id = '" . $memberInfo->user_id . "' AND `no` = '" . $LGD_BUYERID . "'";
					$this->haksa2080->update('zetyx_member_table', $updateData, $updateWhere);
				}
			}
			/************** ����Ʈ ���Ȯ�� END ****************/
			
			
			/************** ����Ʈ ���� start ****************/
			## member table usepoint update
			$savePoint = (int)$memberInfo->usepoint + ((int)$LGD_AMOUNT * 0.05);
			$updateData = array(
				'usepoint' => $savePoint
			);
			$updateWhere = "user_id = '" . $memberInfo->user_id . "' AND `no` = '" . $LGD_BUYERID . "'";
			$this->haksa2080->update('zetyx_member_table', $updateData, $updateWhere);
			
			// use_point table insert
			$insertData = array(
				'order_num' => $LGD_OID
				, 'mem_user_id' => $memberInfo->user_id
				, 'lecture_no' => $LGD_PRODUCTCODE
				, 'point_title' => (count($paymentLectureName) >= 2) ? $paymentLectureName['0'] . ' �� ' . (count($paymentLectureName) - 1) . '��' : $paymentLectureName['0']
				, 'point' => ((int)$LGD_AMOUNT * 0.05)
				, 'point_term' => date('Y:m:d-H:i:s')
				, 'point_state' => '1'
				, 'wdate' => date('Y:m:d-H:i:s')
			);
			$this->haksa2080->insert('use_point', $insertData);
			/************** ����Ʈ ���� end ****************/
			
			
			## ���籸�Ž� besong_info Insert
			foreach($lectureMemParentInfo as $lecMem_key => $lecMem_item) {
				# �θ����� ���籸�Ž�
				if ((int)$lecMem_item->lec_no > (int)50000) { // lecture_mem ���Ϸ� ���縸 ����������
					$book_code = (int)$lecMem_item->lec_no - (int)50000;
					$besongData = array(
						'order_num' => $LGD_OID
						,'user_id' => $memberInfo->user_id
						,'phone_home' => (!empty($LGD_RECEIVERPHONE)) ? $LGD_RECEIVERPHONE : $memberInfo->home_tel
						,'phone_mobi' => (!empty($LGD_BUYERPHONE)) ? $LGD_BUYERPHONE : $memberInfo->handphone
						,'uno' => (!empty($LGD_DELIVERYINFO)) ? $LGD_DELIVERYINFO : $memberInfo->uno_new
						,'home_address' => (!empty($LGD_BUYERADDRESS)) ? $LGD_BUYERADDRESS : $memberInfo->home_address
						,'tail_address' => $memberInfo->hobby
						,'lec_code' => $lecMem_item->lec_no
						,'book_code' => $book_code
						,'sap_ship_state' => '1'
						,'vsbed' => ''
						,'remark2' => ''
						,'discount_rate' => ''
					);
					$this->haksa2080->insert('besong_info', $besongData);
				}
				
				## �ڽİ���
				if ($lecMem_item->join_lecture_list != '') {
					foreach($lecMem_item->join_lecture_list as $joinLecture_key => $joinLecture_item) {
						## �ڽİ��� ���籸�Ž�
						if ((int)$joinLecture_item['lec_no'] > 50000) { // lecture_mem ���Ϸ� ���縸 ����������
							$book_code = (int)$joinLecture_item['lec_no'] - 50000;
							$besongData = array(
								'order_num' => $LGD_OID
								,'user_id' => $memberInfo->user_id
								,'phone_home' => (!empty($LGD_RECEIVERPHONE)) ? $LGD_RECEIVERPHONE : $memberInfo->home_tel
								,'phone_mobi' => (!empty($LGD_BUYERPHONE)) ? $LGD_BUYERPHONE : $memberInfo->handphone
								,'uno' => (!empty($LGD_DELIVERYINFO)) ? $LGD_DELIVERYINFO : $memberInfo->uno_new
								,'home_address' => (!empty($LGD_BUYERADDRESS)) ? $LGD_BUYERADDRESS : $memberInfo->home_address
								,'tail_address' => $memberInfo->hobby
								,'lec_code' => $joinLecture_item['lec_no']
								,'book_code' => $book_code
								,'sap_ship_state' => '1'
								,'vsbed' => ''
								,'remark2' => ''
								,'discount_rate' => ''
							);
							$this->haksa2080->insert('besong_info', $besongData);
						}
					}
				}
			}
			
		} else {
			// �������� DBó�� ���� �� Rollback
			$isDBOK = false;
			$this->haksa2080->set("SQL_ERRORMSG","isDBOK = false : Empty Payment Data. (lec_state = 1)");
			$this->haksa2080->where("LGD_OID", $LGD_OID);
			$this->haksa2080->set("Payment_Log");
			
			// �������н� update ����
			$updateData = array(
				'tid' => ''
				,'lec_state' => '0'
				,'wdate' => date('Y:m:d')
				,'tdate' => date('Y:m:d-H:i:s')
			);
			$updateWhere = "`order_num` = '". $LGD_OID ."' AND member_no = '". $LGD_BUYERID ."'";
			$this->haksa2080->update('lecture_mem', $updateData, $updateWhere);
		}
		
		## �������� end
		
	}
	
	
}