<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');


/* End of file constants.php */
/* Location: ./application/config/constants.php */


/* 전역변수 추가 */
if (mb_substr($_SERVER['HTTP_HOST'], 0, 12) == "ml.haksa2080" || mb_substr($_SERVER['HTTP_HOST'], 0, 11) == "mlhaksa2080") { // 로컬
    define('LMHAKSA', '1');
} else {
    define('LMHAKSA', '0');
}

if (mb_substr($_SERVER['HTTP_HOST'], 0, 12) == "mt.haksa2080" || mb_substr($_SERVER['HTTP_HOST'], 0, 11) == "mthaksa2080") { // 개발
    define('TMHAKSA', '1');
} else {
    define('TMHAKSA', '0');
}

if (mb_substr($_SERVER['HTTP_HOST'], 0, 12) == "mq.haksa2080" || mb_substr($_SERVER['HTTP_HOST'], 0, 11) == "mqhaksa2080") { // QA
    define('QMHAKSA', '1');
} else {
    define('QMHAKSA', '0');
}

if (mb_substr($_SERVER['HTTP_HOST'], 0, 12) == "mr.haksa2080" || mb_substr($_SERVER['HTTP_HOST'], 0, 11) == "mrhaksa2080") { // 운영
    define('MHAKSA', '1');
} else {
    define('MHAKSA', '0');
}


if (mb_substr($_SERVER['HTTP_HOST'], 0, 12) == "ml.haksa2080" || mb_substr($_SERVER['HTTP_HOST'], 0, 11) == "mlhaksa2080") { // 로컬
    define('SITE_DOMAIN', 'http://ml.haksa2080.com');
    define('PC_DOMAIN', 'http://l.haksa2080.com');
    define('LOGIN_PAGE', 'http://ml.haksa2080.com/login/main');
    define('IS_LOCAL', true);
    define('ENV_DOMAIN', 'l');
} else if (mb_substr($_SERVER['HTTP_HOST'], 0, 12) == "mt.haksa2080" || mb_substr($_SERVER['HTTP_HOST'], 0, 11) == "mthaksa2080") { // 개발
    define('SITE_DOMAIN', 'https://mt.haksa2080.com');
    define('PC_DOMAIN', 'https://t.haksa2080.com/');
    define('LOGIN_PAGE', 'https://mt.haksa2080.com/login/main');
    define('IS_DEV', true);
    define('ENV_DOMAIN', 't');
} else if (mb_substr($_SERVER['HTTP_HOST'], 0, 12) == "mq.haksa2080" || mb_substr($_SERVER['HTTP_HOST'], 0, 11) == "mqhaksa2080") { // QA
    define('SITE_DOMAIN', 'https://mq.haksa2080.com');
    // define('PC_DOMAIN', '//qpublic.hackers.com');
    define('LOGIN_PAGE', 'https://mq.haksa2080.com/login/main');
    define('IS_QA', true);
    define('ENV_DOMAIN', 'q');
} else { // 운영
    define('SITE_DOMAIN', 'https://mr.haksa2080.com');
    define('PC_DOMAIN', 'https://www.haksa2080.com/');
    define('LOGIN_PAGE', 'https://mr.haksa2080.com/login/main');
    define('IS_REAL', true);
    define('ENV_DOMAIN', '');
}

//경로 설정
define('SERVER_DOMAIN',$_SERVER["HTTP_HOST"]);

//SKIN, IMAGE, CSS
define('JS_PATH',  SITE_DOMAIN.'/js/');
define('CSS_PATH', SITE_DOMAIN.'/css/');

//게시판 관리자
define('COMMUNITY_ADM_ID', "admin");

//테스트결제
define(
	'PAYADMIN', "admin|admin2|test1515|test342"
);

//kkalrong		윤정원 프로 요청(2019/05/30, http://hac.educamp.org/linker.php?menuno=5432&board_id=hac_180214102811&board_no=1627)
//yerong		윤정원 프로 요청(2019/05/30, http://hac.educamp.org/linker.php?menuno=5432&board_id=hac_180214102811&board_no=1627)
//edmsqlse		기은빈 프로 요청(2019/05/31)
//
