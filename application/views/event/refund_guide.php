<div id="event_guide">
    <p class="btn_area">상품안내/환불기준 펼쳐보기 ▼</p>
    <div class="pro_refund_guide">

        <?include 'application/views/event/'.$_GET['evt_code'].'/event_info.html'?>

        <h3>환불</h3>
        <dl>
            <dt>1. 강의 환불 기준</dt>
            <dd>1) 결제 및 교재 수령일로부터 7일 이내 수강한 과목이 없는 경우 : [결제금액 100% ]</dd>
            <dd>2) 결제 및 교재 수령일로부터 7일 이내 1강 이상 수강 : [결제금액 - 학습수수료] 환불</dd>
            <dd>3) 결제 및 교재 수령일로부터 7일 경과 후 : 결제금액 - 학습수수료 - 위약금 ([결제금액-교재비]의 10%) = 환불
                <ul>
                    <li>※ 학습수수료는 실제 수강한 강의의 단과강의 판매 가격을 기준으로 계산합니다. 공제액은 수강한 강좌의 전체강좌 수 대비 수강한 수에 비례하여 산정한 금액으로 계산합니다.</li>
                    <li>※ 쿠폰 등을 통해 할인 혜택을 받으신 경우 실 결제 금액을 기준으로 환불 금액을 산정합니다.</li>
                </ul>
            </dd>

            <dt>2. 교재 환불 기준</dt>
            <dd>1) 강의 환불 기준과 달리 교재 반품의 경우 수령 후 7일 이내 아래 기준에 따라 사용하지 않은 교재에 한하여 환불이 가능합니다. 다만, 강의와 별도로 교재만 따로 환불하는 것은 불가능하며, 강의 환불 시 교재만 따로 반납 받지도 않습니다.</dd>
            <dd>2) 교재는 사용하지 않은 경우에 한해 환불이 가능하기 때문에 아래 사항 중 어느 하나의 경우에 해당하는 교재는 환불이 불가능하여 강의 환불시 교재비(총 금액)가 추가 공제됩니다.
                <ul>
                    <li>-	소비자에게 책임 있는 사유로 교재가 멸실되거나 훼손된 경우. 다만, 교재의 내용을 확인하기 위하여 포장 등을 훼손한 경우는 제외한다.</li>
                    <li>-	소비자의 사용 또는 일부 소비로 교재의 가치가 현저히 감소한 경우</li>
                    <li>-	시간이 지나 다시 판매하기 곤란할 정도로 교재의 가치가 현저히 감소한 경우</li>
                </ul>
            </dd>
            <dd>3) YES24를 통해 교재 배송을 받으신 경우 교재 배송 출발 이후 환불 요청 시 왕복배송비 6,000원을 제외하고 환불이 진행됩니다. 다만, 교재를 사용하여 환불이 불가한 경우 배송비는 공제되지 않으나, 하기 [※ 환불 시 공제되는 교재 금액 안내]에서 공지한 금액이 공제됩니다.
                <ul>
                    <li class="btn_class"><a href="javascript:proRefundview(1);">※ 환불 시 공제되는 교재 금액 안내</a></li>
                </ul>
            </dd>
            <dd>4) 날개물류를 통해 교재 배송을 받으신 경우 교재 배송 출발 이후 환불 요청 시 배송비 3,000원을 결제한 회원에게는 반품 배송비 3,000원을 제외, 배송비 지원 혜택을 받은 회원에게는 왕복배송비 6,000원을 제외한 후 환불이 진행됩니다. 다만, 교재를 사용한 경우에는 배송비 3,000원을 결제한 회원에게는 하기 [※ 환불 시 공제되는 교재 금액 안내]에서 공지한 금액을 제외, 배송비 지원 혜택을 받은 회원에게는 최초 배송비 3,000원과 하기 [※ 환불 시 공제되는 교재 금액 안내]에서 공지한 금액을 제외한 후 환불이 진행됩니다.
                <ul>
                    <li class="btn_class"><a href="javascript:proRefundview(2);">※ 환불 시 공제되는 교재 금액 안내</a></li>
                </ul>
            </dd>

            <dt>3. 환불 금액 지급 기한</dt>
            <dd>1) 신용카드 결제 혹은 PG사(LGU+)를 통해 결제하신 경우 : 환불 신청 시/반품 상품 수령 시 지체없이 카드사 혹은 PG사(LGU+)에 결제 취소 요청하고, 이후 카드사 혹은 PG사(LGU+)에서 결제에 대해 취소 혹은 부분 취소 처리 합니다.</dd>
            <dd>2) 에스크로 결제 혹은 해커스 독학사계좌로 직접 입금하신 경우 : 환불 신청일/반품 상품 수령일로부터 3영업일 이내에 입력하신 계좌로 환불 금액 입금됩니다. 단, 환불 신청자가 계좌 번호를 잘못 기재 혹은 계좌 번호 입력을 늦게 하여 환불 금액 지급이 지체되는 경우에 대해서 해커스 독학사가 책임지지 않습니다.</dd>

            <dt>4. 환불절차</dt>
            <dd>1) 환불을 원하시는 경우 해커스독학사 고객센터 (1599-3081)으로 전화하시면, 환불 문의 및 신청이 가능합니다.
                <ul>
                    <li>-	고객센터 운영시간 :월~금 08:30~19:00</li>
                </ul>
            </dd>

            <dd>2) 전액환불의 경우 환불 문의 후 전액 환불이 가능하다는 검토 내용에 동의하지 않으셔도 자동 환불 처리 됩니다.</dd>
            <dd>3) 부분환불의 경우 검토 드린 환불 금액에 동의하시면 그때 환불 신청이 되고, 환불 처리 됩니다.</dd>
            <dd>4) 환불 문의 시 교재 등의 반품 여부 선택이 가능합니다. 교재 등의 반품을 선택한 경우 환불 금액 검토 완료된 후 교재 등의 반품에 동의하시면 반품 기사님 방문하셔서 교재 등을 수령해갑니다.</dd>

            <dt>5. 기타 환불 정책과 관련한 유의사항</dt>
            <dd>1) 미성년자와 거래에 대한 계약 시, 법정대리인이 그 계약에 동의하지 아니하면 미성년자 본인 또는 법정대리인이 그 계약을 취소시킬 수 있습니다.</dd>
            <dd>2) 상품에 결함이 있거나 상품이 표시광고의 내용과 다르거나 계약내용과 다를 경우 그 상품 등을 공급받은 날부터 3개월 이내, 그 사실을 안 날 또는 알 수 있었던 날부터 30일 이내에 환불 가능합니다.</dd>
            <dd>3) <span class="red_txt">연장기간은 무료 혜택으로 지급되는 기간이므로 연장된 기간 동안에는 환불이 불가합니다.</span></dd>
            <dd>4) 환불 금액은 반품 신청한 교재의 반송 및 미사용 확인 후 결제 수단에 따라 현금 부분환불 또는 카드 부분 취소로 처리됩니다.</dd>
            <dd>5) 주소 미기재 및 불명확한 주소 입력으로 상품 등을 수령하지 못하였을 시에는 해커스 독학사에서 책임지지 않습니다.</dd>
            <dd>6) 혜택으로 쿠폰이나 포인트 등을 받으신 경우 쿠폰은 사용불가 처리, 지급된 포인트는 차감 됩니다.</dd>
            <dd>7) <span class="red_txt">결제 후 수강 대기 상태에서 7일이 경과된 시점부터 수강 중인 상태로 변경 됩니다.</span></dd>
        </dl>

        <div class="proRefundview" id="proRefundview1">
            <div class="proRefundviewBox">
                <h4>[YES24 배송 교재별 공제 금액]</h4>
                <a  href="javascript:proRefundviewClose();" class="prorefund_close">X</a>
                <table class="event_info" cellspacing="0" cellpadding="0">
                    <colgroup>
                        <col width="*">
                        <col width="40%">
                    </colgroup>
                    <tr>
                        <th colspan="2">교양공통    1단계</th>
                    </tr>
                    <tr>
                        <td>국어</td>
                        <td align="right">19,710원</td>
                    </tr>
                    <tr>
                        <td>현대사회와윤리</td>
                        <td align="right">16,110원</td>
                    </tr>
                    <tr>
                        <td>영어</td>
                        <td align="right">19,710원</td>
                    </tr>
                    <tr>
                        <td>국사</td>
                        <td align="right">21,510원</td>
                    </tr>
                    <tr>
                        <td>사회학개론</td>
                        <td align="right">19,710원</td>
                    </tr>
                </table>

                <table class="event_info">
                    <colgroup>
                        <col width="*">
                        <col width="40%">
                    </colgroup>
                    <tr>
                        <th colspan="2">경영학 2단계 1달합격</th>
                    </tr>
                    <tr>
                        <td>경영정보론</td>
                        <td align="right">19,710원</td>
                    </tr>
                    <tr>
                        <td>마케팅원론</td>
                        <td align="right">19,710원</td>
                    </tr>
                    <tr>
                        <td>생산운영관리</td>
                        <td align="right">19,710원</td>
                    </tr>
                    <tr>
                        <td>인적자원관리</td>
                        <td align="right">16,920원</td>
                    </tr>
                    <tr>
                        <td>조직행동론</td>
                        <td align="right">15,930원</td>
                    </tr>
                    <tr>
                        <td>회계원리</td>
                        <td align="right">17,910원</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="proRefundview" id="proRefundview2">
            <div class="proRefundviewBox">
                <h4>[날개물류 배송 교재별 공제 금액]</h4>
                <a  href="javascript:proRefundviewClose();" class="prorefund_close">X</a>
                <ul class="proRefundList">
                    <li>
                        <table class="event_info" cellspacing="0" cellpadding="0">
                            <colgroup>
                                <col width="*">
                                <col width="40%">
                            </colgroup>
                            <tr>
                                <th colspan="2">영어영문학    2단계</th>
                            </tr>
                            <tr>
                                <td>중급영어</td>
                                <td align="right">16,900원</td>
                            </tr>
                            <tr>
                                <td>영문법</td>
                                <td align="right">14,900원</td>
                            </tr>
                            <tr>
                                <td>영어학개론</td>
                                <td align="right">14,900원</td>
                            </tr>
                            <tr>
                                <td>영어음성학</td>
                                <td align="right">14,900원</td>
                            </tr>
                            <tr>
                                <td>19세기영미소설</td>
                                <td align="right">16,900원</td>
                            </tr>
                            <tr>
                                <td>영국문학개관</td>
                                <td align="right">14,900원</td>
                            </tr>
                            <tr>
                                <td>중급영어 모의고사</td>
                                <td rowspan="6" align="right">11,900원</td>
                            </tr>
                            <tr>
                                <td>영문법 모의고사</td>
                            </tr>
                            <tr>
                                <td>영어학개론 모의고사</td>
                            </tr>
                            <tr>
                                <td>영어음성학 모의고사</td>
                            </tr>
                            <tr>
                                <td>19세기영미소설 모의고사</td>
                            </tr>
                            <tr>
                                <td>영국문학개관 모의고사</td>
                            </tr>
                        </table>
                    </li>
                    <li class="list_fr">
                        <table class="event_info" cellspacing="0" cellpadding="0">
                            <colgroup>
                                <col width="*">
                                <col width="40%">
                            </colgroup>
                            <tr>
                                <th colspan="2">경영학    3단계</th>
                            </tr>
                            <tr>
                                <td>소비자행동론</td>
                                <td align="right">17,900원</td>
                            </tr>
                            <tr>
                                <td>경영과학</td>
                                <td align="right">12,900원</td>
                            </tr>
                            <tr>
                                <td>노사관계론</td>
                                <td align="right">17,900원</td>
                            </tr>
                            <tr>
                                <td>재무회계</td>
                                <td align="right">17,900원</td>
                            </tr>
                            <tr>
                                <td>경영전략</td>
                                <td align="right">19,900원</td>
                            </tr>
                            <tr>
                                <td>재무관리론</td>
                                <td align="right">15,900원</td>
                            </tr>
                            <tr>
                                <td>경영과학 모의고사</td>
                                <td rowspan="2" align="right">11,900원</td>
                            </tr>
                        </table>
                    </li>
                </ul>
                <ul class="proRefundList">
                    <li>
                        <table class="event_info" cellspacing="0" cellpadding="0">
                            <colgroup>
                                <col width="*">
                                <col width="40%">
                            </colgroup>
                            <tr>
                                <th colspan="2">영어영문학    3단계</th>
                            </tr>
                            <tr>
                                <td>고급영어</td>
                                <td align="right">18,900원</td>
                            </tr>
                            <tr>
                                <td>고급영문법</td>
                                <td align="right">14,900원</td>
                            </tr>
                            <tr>
                                <td>영어발달사</td>
                                <td align="right">14,900원</td>
                            </tr>
                            <tr>
                                <td>영어통사론</td>
                                <td align="right">14,900원</td>
                            </tr>
                            <tr>
                                <td>20세기영미소설</td>
                                <td align="right">19,800원</td>
                            </tr>
                            <tr>
                                <td>미국문학개관</td>
                                <td align="right">11,900원</td>
                            </tr>
                            <tr>
                                <td>고급영어모의고사</td>
                                <td rowspan="6" align="right">11,900원</td>
                            </tr>
                            <tr>
                                <td>고급영문법 모의고사</td>
                            </tr>
                            <tr>
                                <td>영어발달사 모의고사</td>
                            </tr>
                            <tr>
                                <td>영어통사론 모의고사</td>
                            </tr>
                            <tr>
                                <td>20세기영미소설 모의고사</td>
                            </tr>
                            <tr>
                                <td>미국문학개관 모의고사</td>
                            </tr>
                        </table>
                    </li>
                    <li class="list_fr">
                        <table class="event_info" cellspacing="0" cellpadding="0">
                            <colgroup>
                                <col width="*">
                                <col width="40%">
                            </colgroup>
                            <tr>
                                <th colspan="2">경영학    4단계</th>
                            </tr>
                            <tr>
                                <td>인사조직론/마케팅관리</td>
                                <td rowspan="2" align="right">19,800원</td>
                            </tr>
                            <tr>
                                <td>회계학/재무관리</td>
                            </tr>
                        </table>
                        <table class="event_info" cellspacing="0" cellpadding="0">
                            <colgroup>
                                <col width="*">
                                <col width="40%">
                            </colgroup>
                            <tr>
                                <th colspan="2">간호독학사</th>
                            </tr>
                            <tr>
                                <td>간호과정론</td>
                                <td align="right">17,900원</td>
                            </tr>
                            <tr>
                                <td>간호연구방법론</td>
                                <td align="right">14,900원</td>
                            </tr>
                            <tr>
                                <td>간호윤리와 법</td>
                                <td align="right">15,900원</td>
                            </tr>
                            <tr>
                                <td>간호지도자론</td>
                                <td align="right">20,900원</td>
                            </tr>
                        </table>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>