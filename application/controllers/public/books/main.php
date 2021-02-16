<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
		$this->load->model("lecture/m_lecture");
		$this->load->model("login/m_member");
	}
	
	public function index()
	{
		$this->bookStoreListContent();
	}
	
	
	public function bookStoreListContent()
	{
		// Header user_info, coupon_cnt
		$use_point = $this->m_member->getUserPoint($this->session->userdata('member_id')); // use_point (보유포인트)
		$used_point = $this->m_member->getUserUsedPoint($this->session->userdata('member_id')); // use_point (보유포인트)
		$user_point = ((int)$use_point->use_point - (int)$used_point->used_point);
		$userInfo = $this->m_member->getUserInfo($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		$userCouponData = $this->m_member->getUserCoupon($this->session->userdata('member_no', true), $this->session->userdata('member_id', true));
		
		// BEST 추천 교재
		$best_books_list = $this->m_lecture->getBestBookList();
		foreach($best_books_list as $best_key => $best_item) {
			$best_books_list[$best_key]['books_point'] = number_format(($best_item['pamount'] * 0.05)); // 구매시 지급될 포인트 금액 (5% 지급) == 판매가 * 5% / 100
			$best_books_list[$best_key]['books_percent'] = ceil((($best_item['pamount'] - $best_item['amount']) / $best_item['pamount']) * 100); // ((원가 - 판매가) / 원가) * 100
			$best_books_list[$best_key]['payment_book_code'] = 50000 + (int)$best_item['book_id'];
			$best_books_list[$best_key]['books_preview'] = $this->m_lecture->getBooksPreView($best_item['book_id']);
		}
		
		$getPost = (!empty($_POST)) ? $_POST : '' ;
		
		// 교재리스트
		$books_list = $this->m_lecture->getBooksList($getPost);
		
		// 교재리스트 cnt
		$book_list_cnt = $this->m_lecture->getBooksListCnt($getPost);
		
		// 저자명 리스트
		$author_list = $this->m_lecture->getBookInfoAuthor();
		
		foreach($books_list as $book_key => $book_item){
			$books_list[$book_key]['books_point'] = number_format(($book_item['pamount'] * 0.05)); // 구매시 지급될 포인트 금액 (5% 지급) == 판매가 * 5% / 100
			$books_list[$book_key]['books_percent'] = ceil((($book_item['pamount'] - $book_item['amount']) / $book_item['pamount']) * 100); // ((원가 - 판매가) / 원가) * 100
			$books_list[$book_key]['payment_book_code'] = 50000 + (int)$book_item['book_id'];
			
			$books_list[$book_key]['books_author'] = $this->m_lecture->getLectureBook($book_item['book_id']);
			$books_list[$book_key]['books_preview'] = $this->m_lecture->getBooksPreView($book_item['book_id']);
		}
		
		$head = array(
			'_css' => 'books'
			,'_js' => 'lecture'
			,'user_info' => $userInfo
			,'user_point' => $user_point
			,'user_coupon_cnt' => count($userCouponData)
		);
		
		$data = array(
			'images' => 'https://gscdn.hackers.co.kr/haksa2080/images'
			,'books_list' => $books_list
			,'best_books_list' => $best_books_list
			,'book_list_cnt' => $book_list_cnt
			,'author_list' => $author_list
		);
		
		$this->load->view('common/header',$head);
		$this->load->view('books/books_list', $data);
        $this->load->view('common/allimpopup');
		$this->load->view('common/footer');
	}
	
	// 교재 더보기
	public function bookStoreListAjax()
	{
		$getPost = (!empty($_POST)) ? $_POST : '' ;
		
		if (!empty($_POST['page_cnt'])) {
			$book_plus_list = $this->m_lecture->getBooksList($getPost);
			
			foreach($book_plus_list as $book_key => $book_item) {
				
				$book_plus_list[$book_key]['book_img'] = (!empty($book_item['book_img'])) ? $book_item['book_img'] : '' ;
				$book_plus_list[$book_key]['books_point'] = number_format(($book_item['pamount'] * 0.05)); // 구매시 지급될 포인트 금액 (5% 지급) == 판매가 * 5% / 100
				$book_plus_list[$book_key]['books_percent'] = ceil((($book_item['pamount'] - $book_item['amount']) / $book_item['pamount']) * 100); // ((원가 - 판매가) / 원가) * 100
				$book_plus_list[$book_key]['payment_book_code'] = 50000 + (int)$book_item['book_id'];
				
				// $book_plus_list[$book_key]['books_author'] = $this->m_lecture->getLectureBook($book_item['book_id']);
				
				$book_plus_list[$book_key]['book_name'] = iconv("CP949","UTF-8", $book_item['book_name']);
				$book_plus_list[$book_key]['author'] = iconv("CP949","UTF-8", $book_item['author']);
				
				$book_plus_list[$book_key]['books_preview'] = $this->m_lecture->getBooksPreView($book_item['book_id']);
			}
			
			if (!empty($book_plus_list)) {
				echo json_encode(array('result' => true, 'msg' => iconv('EUC-KR', 'UTF-8', '리스트 불러오기 성공!!'), 'book_plus_list' => $book_plus_list, 'member_id' => $this->session->userdata('member_id')));
			} else {
				echo json_encode(array('result' => true, 'msg' => iconv('EUC-KR', 'UTF-8', '리스트 불러오기 실패!! 관리자에 문의해주세요.')));
			}
			exit;
		}
	}
}