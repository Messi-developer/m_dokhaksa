<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Books_view extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->model("lecture/m_lecture");
		$this->load->model("login/m_member");
	}
	
	public function index()
	{
		$this->bookStoreView();
	}
	
	public function bookStoreView()
	{
		// Header user_info, coupon_cnt
		$use_point = $this->m_member->getUserPoint($this->session->userdata('member_id')); // use_point (보유포인트)
		$used_point = $this->m_member->getUserUsedPoint($this->session->userdata('member_id')); // use_point (보유포인트)
		$user_point = ((int)$use_point->use_point - (int)$used_point->used_point);
		$userInfo = $this->m_member->getUserInfo($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		$userCouponData = $this->m_member->getUserCoupon($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		
		// view page
		$bookView = (!empty($_GET['preview'])) ? 'books_preview' : 'books_view' ;
		$bookId = (!empty($_GET['book_id'])) ? $_GET['book_id'] : $_GET['preview'];
		
		// 교재리스트
		$book_view = $this->m_lecture->getBooksView($bookId);
		$book_point = number_format(($book_view->pamount * 5) / 100); // 구매시 지급될 포인트 금액 (5% 지급) == 판매가 * 5% / 100
		
		switch ($book_view->category)
		{
			case 1 : $book_view->category = '교양공통'; break;
			case 2 : $book_view->category = '경영학과'; break;
			case 3 : $book_view->category = '간호학과'; break;
			case 20 : $book_view->category = '기타'; break;
			case 21 : $book_view->category = '영어영문'; break;
		}
		
		// 연계강의
		$book_lecture_list = $this->m_lecture->getBooksLecture($bookId);
		
		// 교재 미리보기
		$book_preview = $this->m_lecture->getBooksPreView($bookId);
		$book_preview_buy = $this->m_lecture->getBooksView($bookId);
		
		$head = array(
			'_css' => 'books'
			,'_js' => 'lecture'
			,'user_info' => $userInfo
			,'user_point' => $user_point
			,'user_coupon_cnt' => count($userCouponData)
		);
		
		$data = array(
			'images' => '//gscdn.hackers.co.kr/haksa2080/images'
			,'book_view' => $book_view
			,'book_point' => $book_point
			,'book_lecture_list' => $book_lecture_list
			,'book_preview' => $book_preview
			,'book_preview_buy' => $book_preview_buy
		);
		
		$this->load->view('common/header',$head);
		$this->load->view('books/'. $bookView, $data);
        $this->load->view('common/allimpopup');
		$this->load->view('common/footer');
	}
	
}