<?
$yoil = array("일","월","화","수","목","금","토");
$yoil_today = date('w', strtotime(date("Y-m-d")));
$yoil_end = 0;
$yoil_diff = 0;

switch ($endDay){
    case '일': $yoil_end = 0; break;
    case '월': $yoil_end = 1; break;
    case '화': $yoil_end = 2; break;
    case '수': $yoil_end = 3; break;
    case '목': $yoil_end = 4; break;
    case '금': $yoil_end = 5; break;
    case '토': $yoil_end = 6; break;
}

$yoil_diff = $yoil_end - $yoil_today;

if($yoil_diff < 0){
    $yoil_diff = 7 + $yoil_diff;
}

$endDate = date("Y-m-d", strtotime('+'.$yoil_diff.' days')).' 23:59:59';
$diff_sec = strtotime($endDate) - strtotime(date("Y-m-d H:i:s"));
?>

<div class="timer_box">
    <div class="timer_in">
        <div class="finish_mark">
            <span data-component="timer" data-format="label">
                <span class="txt">
                    <?if($yoil_diff == 0){?>
                        오늘<strong>마감</strong>
                    <?}else{?>
                        마감<strong><?=$yoil_diff?>일전</strong>
                    <?}?>
                </span>
            </span>
        </div>
        <div class="time_area">
            <!-- [D] 마감 타이머 -->
            <div id="timer_text"><span id="s_text">84기</span> 이벤트 마감까지 남은 시간</div>
            <div class="bx_time timer">
                <span id="dday" data-component="timer" data-format="dd">00</span>일
                <span id="dday_hour" data-component="timer"
                      data-format="hh">00</span> :
                <span id="dday_min" data-component="timer"
                      data-format="mm">00</span> :
                <span id="dday_sec" data-component="timer"
                      data-format="ss">00</span>
            </div>
        </div>
    </div>
</div>

<script>
    var total_sec = <?=$diff_sec?>;

    Day_counter()
    setInterval(Day_counter, 1000);

    function Day_counter() {
        var Remain_days = Math.floor(total_sec / 86400);
        var Remain_tot_sec = total_sec - 86400 * Remain_days;
        var Remain_hour = Math.floor(Remain_tot_sec / 3600);
        var tmp = Remain_tot_sec - Remain_hour * 3600;
        var Remain_minute = Math.floor(tmp / 60);
        var Remain_sec = Math.floor(tmp % 60);

        // 날짜
        if (Remain_days <= 0) Remain_days = '00';
        else if (Remain_days < 10) Remain_days = '0' + Remain_days;
        else Remain_days = Remain_days;

        // 시간
        if (Remain_hour <= 0) Remain_hour = '00';
        else if (Remain_hour < 10) Remain_hour = '0' + Remain_hour;
        else Remain_hour = Remain_hour;

        // 분
        if (Remain_minute < 10) Remain_minute = '0' + Remain_minute;

        // 초
        if (Remain_sec < 10) Remain_sec = '0' + Remain_sec;

        $("#dday").text(Remain_days);
        $("#dday_hour").text(Remain_hour);
        $("#dday_min").text(Remain_minute);
        $("#dday_sec").text(Remain_sec);

        total_sec--;
    }
</script>