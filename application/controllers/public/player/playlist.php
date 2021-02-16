<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Playlist extends CI_Controller
{
    function __construct(){
        parent::__construct();
    }

    public function xmlload(){
        $target = $this->input->get("target");
        $pos = $this->input->get("pos");
        $lectureName = '';

        //북마크 사용여부 설정 옵션
        //0: 북마크 기능 사용안함
        //1: 북마크 기능 사용
        $bookmark_use = 0;

        //사용자가 지정한 북마크 정보 설정 옵션 (단위:ms)
        //북마크 정보가 다수일 경우 "|" 를 이용하여 구분
        $bookmarkpos = '';

        //영상시작위치
        $startpos = 0;

        //.smi 자막URL
        $subtitle_url = '';

        /*
        urlpath : VOD 서비스 URL
        urltitle : 화면상에 보여지는 이름
        default : 처음 재생 시 보여지는 contents (모두 false인경우 첫번째 영상부터 재생됨)
        */

        $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
        $xml .= "<result>";
        $xml .= "	<contents>";
        $xml .= "		<start>0</start>";
        $xml .= "		<content>";
        $xml .= "			<cid>cid</cid>";
        $xml .= "			<index>0</index>";
        $xml .= "			<path><![CDATA[".$lectureName."]]></path>";
        $xml .= "			<urls>";
        $xml .= "				<url>";
        $xml .= "					<urlpath>http://mvod.aquan.hackers.co.kr/champstudymobile/wmv/withus_300/".$target."</urlpath>";
        $xml .= "					<urltitle>일반화질</urltitle>";
        $xml .= "					<default>true</default>";
        $xml .= "				</url>";
        $xml .= "				<url>";
        $xml .= "					<urlpath>http://mvod.aquan.hackers.co.kr/champstudymobile/wmv/withus/".$target."</urlpath>";
        $xml .= "					<urltitle>고화질</urltitle>";
        $xml .= "					<default>false</default>";
        $xml .= "				</url>";
        $xml .= "			</urls>";
        $xml .= "			<subtitle_url></subtitle_url>";
        $xml .= "			<startpos>".$pos."</startpos>";
        $xml .= "			<bookmarkpos>".$bookmarkpos."</bookmarkpos>";
        $xml .= "			<bookmark_use>".$bookmark_use."</bookmark_use>";
        $xml .= "		</content>";
        $xml .= "	</contents>";
        $xml .= "</result>";

        echo $xml;

    }

}
?>
