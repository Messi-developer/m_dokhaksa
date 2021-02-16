<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function get_banner($position,$ui_type="li"){
    $CI =& get_instance();
    $CI->load->model('banner/m_banner');

    //배너 관리자
    $banner_lists = $CI->m_banner->banner_lists($position);
    $period_lists = $CI->m_banner->period_list();
    
    $res_view = "";
    switch ($ui_type){
        case "li" :
            foreach ($banner_lists as $key => $rs){
                $blank = "";
                if ($rs['target'] == "_blank") {
                    $blank = "target='_blank'";
                }

                if ($blank) $blank .= " rel='noreferrer'";

                if ($rs["attached"]) {
                    $rs['attached'] = PC_DOMAIN.'/PEG/banner/'.$rs['attached'];
                }
                
                // 메인배너 기수불러오기
                $period_info = $period_lists[$rs['period_key']];
    
                if(!empty($period_info)){
                    // 기수 구하기
                    if($period_info['del_yn'] == 'Y') {
                        if($_SERVER['REMOTE_ADDR'] == '172.16.0.62') {
                            echo 'aaaaaaaaaaaaaaaaaaaaaaaaa';
                            print_r($period_info);
                        }
                        // 시작 날짜로 경과 일수 구하기
                        $prd_days = floor(((strtotime(date('YmdHm')) - strtotime($period_info['evt_sdate'])) / 3600) / 24);
                        $r_day = intval((strtotime(date('YmdHm'))-strtotime($period_info['evt_sdate'])) / 86400);
        
                        // 기수 기간으로 기수 계산
                        $prd_cnt = ($period_info['evtp_term']) ? ceil($prd_days / $period_info['evtp_term']) : 0;
        
                        if($prd_cnt == 0){
                            $prd_cnt = 1;
                        }
                        $evt_eyoil = explode("|", $period_info['evt_eyoil']);
                        $y = date('w') + 1;
                        for($i=0; $i<$r_day; $i++) {
                            if($y != 'undefined' && in_array($y, $evt_eyoil)) $prd_cnt ++;
                            $y++;
                            if($y > 6) $y = 0;
                        }
        
                        //마감까지 남은 일수
                        if(($evt_eyoil[0]-date('w')) >= 0) {
                            $prd_day = ($evt_eyoil[0]-date('w'));
                        } else if(($evt_eyoil[1]-date('w')) >= 0) {
                            $prd_day = ($evt_eyoil[1]-date('w'));
                        } else {
                            $prd_day = 7 + ($evt_eyoil[0]-date('w'));
                        }
                        // 배너와 페이지 기수 차이로 인한 임시 조치
                        // http://hac.educamp.org/linker.php?menuno=1299&board_id=hacjob_error&board_no=32161
//                        $prd_cnt ++;
        
                    } else if($period_info['del_yn'] == 'W') {
        
                        // 시작 날짜로 경과 일수 구하기
                        $prd_days = floor(((strtotime(date('YmdHm')) - strtotime($period_info['evt_sdate'])) / 3600) / 24);
                        // 기수 기간으로 기수 계산
                        $prd_cnt = ceil($prd_days / $period_info['evtp_term']);
                        if($prd_cnt==0){
                            $prd_cnt =1;
                        }
                        //마감까지 남은 일수
                        $prd_day = $period_info['evtp_term'] - ($prd_days % $period_info['evtp_term']);
        
                    }
    
                    // 마감문구
                    $prd_end_mark = "";
    
                    if($prd_day == 1){
                        $prd_end_mark = "내일 마감";
                    }else if($prd_day == 2){
                        $prd_end_mark = "마감 2일전";
                    }else if($prd_day == 3){
                        $prd_end_mark = "마감 3일전";
                    }else if(($prd_day > 3 && $prd_day <= 7)){
                        $prd_end_mark = "마감임박";
                    }else if ($prd_day >= 8){
                        $prd_end_mark = "";
                    }else {
                        $prd_end_mark = "오늘 마감";
                    }
                    // 배너리스트 or 마감이슈
                    $res_view .= "
                            <li>
                                <a href='".($rs['address'])."' $blank>
                                    <span class='mainEndDate'><strong>$prd_cnt</strong><strong>기</strong><strong style='margin-left:6px;'>$prd_end_mark</strong></span>
                                    <img src='".$rs['attached']."' alt='".$rs['banner_text']."' />
                                </a>
                            </li>
                            ";
                }else{
                    // 배너리스트 or 마감이슈
                    $res_view .= "
                            <li>
                                <a href='".($rs['address'])."' $blank>
                                    <img src='".$rs['attached']."' alt='".$rs['banner_alt']."' />
                                </a>
                            </li>
                            ";
                }
            }
            break;
        case "none" :
            foreach ($banner_lists as $banner) {
                if ($banner['VIEW'] != "yes") continue;
                switch ($banner['type']) {
                    case 'text':
                        $res_view .= $banner['content'];
                        break;
                    case 'media' :
                        $res_view .= "<a href='{$banner['address']}' target='{$banner['target']}'>";
                        $res_view .= "<img src= ".PC_DOMAIN."/PEG/banner/{$banner['attached']} />";
                        $res_view .= "</a>";
                        break;
                }
            }
            break;
        /*case "dd" :
            foreach ($banner_lists as $key => $rs){
                $blank = "";
                if($rs['a_blank'] == "Y") $blank = "target='_blank'";
                if(!preg_match("/http:/i",$rs["img_src"])) $rs['img_src'] = PC_DOMAIN.$rs['img_src'];
                $res_view .= "<dd><a href='".domainChange($rs['link'])."' $blank><img src='".$rs['img_src']."' alt='".$rs['banner_name']."' /></a></dd>";
            }
            break;*/
    }

    return $res_view;
}

// 도메인 변경으로 인한 도메인 변경 로직
/*function domainChange($url){
    $url = preg_replace('/^https?:\/\//','',$url);
    $url_arr = explode('/',$url);

    $url_domain = array_shift($url_arr);

    $url_param = implode('/',$url_arr);
    $patterns_arr = array('/^www.(pass|hackers)+.com/','/^m.www.(pass|hackers)+.com/','/^gosi.(pass|hackers)+.com/','/^egosi.(pass|hackers)+.com/','/^m.gosi.(pass|hackers)+.com/','/^land.(pass|hackers)+.com/','/^eland.(pass|hackers)+.com/','/^m.land.(pass|hackers)+.com/','/^police.(pass|hackers)+.com/','/^epolice.(pass|hackers)+.com/','/^m.police.(pass|hackers)+.com/','/^fn.(pass|hackers)+.com/','/^m.fn.(pass|hackers)+.com/','/^teacher.(pass|hackers)+.com/','/^m.teacher.(pass|hackers)+.com/');
    $replacement_arr = array(PASS_HACKERS,MPASS_HACKERS,GOSI_HACKERS,EGOSI_HACKERS,MGOSI_HACKERS,LAND_HACKERS,ELAND_HACKERS,MLAND_HACKERS,POLICE_HACKERS,EPOLICE_HACKERS,MPOLICE_HACKERS,FN_HACKERS,MFN_HACKERS,TEACHER_HACKERS,MTEACHER_HACKERS);
    $modified_url = preg_replace($patterns_arr,$replacement_arr,$url_domain).'/'.$url_param;
    return $modified_url;
}*/

?>
