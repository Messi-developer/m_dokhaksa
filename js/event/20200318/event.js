$(window).load(function(){
    $('.bxslider').each(function(){
        $(this).bxSlider({
            mode: 'horizontal',
            auto: true,
        })
    });
})

$(document).ready(function(){
	//탭
	$('.tab_box ul li a').on('click',function(){
		var _li = $(this).parent(),
			_idx = _li.index();
		
		_li.addClass('on').siblings().removeClass('on');
		_li.parent().parent().find('.tab_con').eq(_idx).addClass('on').siblings().removeClass('on');
		$('.price').text('0 원');
	});

	//가격단

	$("input[name=chk]:radio").on('change',function(){
		var _this = $(this).val();
		
		if(_this == 372){
			$('.price').text('1,390,000 원');
			$('#checked_lecture').val(_this);
		}
		else if(_this == 371){
			$('.price').text('1,690,000 원');
			$('#checked_lecture').val(_this);
		}
		else if(_this == 381){
			$('.price').text('1,290,000 원');
			$('#checked_lecture').val(_this);
		}
		else if(_this == 380){
			$('.price').text('1,590,000 원');
			$('#checked_lecture').val(_this);
		}
	});

});

function checkPayment() {
	var checked_lecture = $('#checked_lecture').val();
	paymentApply(checked_lecture);
}

//스크롤
function move_scroll(obj){
	var _t = $("#" + obj).offset().top;
	$("html, body").animate({scrollTop:_t + "px"}, 600);
}
