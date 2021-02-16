var __globalBxslider = {
	bxList:[],
	setup:function(idx, attr){
		if(attr.bxSlider){
			// �̹� �ش� ��ʿ� bxslider �����

		}else{
			// attr�� bxslider ����
			var $sliderLi = $(".bxslider li", attr);

			var wd = $sliderLi.width();

			var isSingleImage = $sliderLi.length === 1;

			var _mode = $(attr).attr("data-mode"); // �����̵� ȿ�� - horizontal,vertical,fade
			_mode = _mode ? _mode : 'horizontal';

			var _maxSlides = $(attr).attr("data-maxSlides"); // �ִ� �������� ����
			_maxSlides = _maxSlides && !isNaN(parseInt(_maxSlides)) ? parseInt(_maxSlides) : 1;

			var _slideMargin = $(attr).attr("data-slideMargin"); // �����̵� ���� margin ��
			_slideMargin = _slideMargin && !isNaN(parseInt(_slideMargin)) ? parseInt(_maxSlides) : 0;

			var _delay = $(attr).attr("data-delay"); // �����̵� ������ ����
			_delay = _delay && !isNaN(parseInt(_delay)) ? parseInt(_delay) : 4000;

			var _speed = $(attr).attr("data-speed"); // �����̵� �ӵ� ����
			_speed = _speed && !isNaN(parseInt(_speed)) ? parseInt(_speed) : 400;

			var _autoBtn = $(attr).attr("data-autoBtn"); // play / stop / puase
			_autoBtn = _autoBtn && _autoBtn=='true' ? true : false;

			var _auto  = $(attr).attr("data-auto"); // �ڵ������̵� ����
			_auto = _auto && _auto=='true' ? true : false;

			var _pagerCustom = '.'+$(attr).attr("data-pagerCustom");  // ������ �ܺη� �������Ҷ�

			var _pager = $(attr).attr("data-pager");  // ������ ���׶� ��ư ���� ����
			_pager = ( _pager && _pager=='true' && !isSingleImage ) ? true : false;

			var _controls = $(attr).attr("data-controls"); //�ڵ� �����̵� ��Ʈ�� ��ư ���� ����
			_controls = _controls && _controls=='true' ? true : false;

			 var _moves = $(attr).attr("data-moves"); //���徿 �����̵�
			_moves = _moves && !isNaN(parseInt(_moves)) ? parseInt(_moves) : 1;

			__globalBxslider.bxList[__globalBxslider.bxList.length] = attr.bxSlider = $('.bxslider',attr).bxSlider({
				 slideWidth: wd
				,slideMargin: _slideMargin
				,minSlides: 1
				,maxSlides: _maxSlides // �ִ� �������� ����
				,speed: _speed
				,pause: _delay
				,mode: _mode // �����̵� ȿ�� - horizontal,vertical,fade
				,autoControls: _autoBtn // play / stop / puase
				,auto: _auto // �ڵ������̵� ����
				,moveSlides:_moves
				,pager: _pager
				,pagerCustom: (_pagerCustom == '.undefined') ? '': _pagerCustom
				,controls: _controls
				,preventDefaultSwipeX: true
				,infiniteLoop: true
				,onSliderLoad:function(){
					$(attr).css('visibility', 'visible');
				} // �ε� �Ϸ�?
				,onSliderResize:function(){ //���θ��� ������ ���ΰ� ���� 20171211������
					var w = $(attr).width(); //���θ�� �� �����ִ°�� li ��ħ ���� �ذ� 20180502 ������
					$(attr).find('.bx-wrapper').css({
						'max-width': w + 'px',
						'margin': '1px auto'
					});
					$(attr).find('.bx-wrapper li').css({
						'min-width': w + 'px'
					});
					__globalBxslider.resize();
				}
			});

			if( _auto && !isSingleImage ){
				$(attr).on('mouseover touchstart',function(){
					this.bxSlider.stopAuto();
				});
				$(attr).on('mouseout touchend',function(){
					this.bxSlider.startAuto();
				});
			}
		}
	},
	resize:function(){
		// ��� resize
		// __globalBxslider.resize();
		for(var i in this.bxList){
			if( typeof this.bxList[i] != 'undefined' ) {
				try{
					this.bxList[i].redrawSlider();
				}catch(e){}
			}
		}
	},
	init:function(selector){
		$(selector).each(this.setup);
	},
	reloadSlider: function(selector) {
		for(var i in this.bxList){
			if( typeof this.bxList[i] != 'undefined' ) {
				try{
					this.bxList[i].reloadSlider();
				}catch(e){}
			}
		}
	}
};

$(function() {
	__globalBxslider.init(".bxslider-default");

	/* ������/�����е� windows focus blur�� event bind�� �������� �ʴ� �̽� ���� ������Ʈ�� �����Ϸ�Ȱ����� ���� 20171211 ������ */
	/*
	var ua = navigator.userAgent;
	if( ua.match(/iPad/i) || ua.match(/iPhone/) ) {
		$(window).on('focus', function(e) {
			__globalBxslider.reloadSlider(".bxslider-default");
		});
	}
	*/

	/* tab ���ο� bxslider�� ������ ��� */
	$('[class*="js-tab"] a[href*="#"]').on('click', function(){__globalBxslider.resize()});
	$('[class*="js-tab-type1"] a[href*="#"]').on('click', function(){__globalBxslider.resize()});
	$('[class*="js-tab-type2"] a[href*="#"]').on('click', function(){__globalBxslider.resize()});
	$('[class*="js-tab-type3"] a[href*="#"]').on('click', function(){__globalBxslider.resize()});

	/*if( _auto &&  !isSingleImage ){
		$(attr).on('mouseover touchstart',function(){
			this.bxSlider.stopAuto()
		});
		$(attr).on('mouseout touchend',function(){
			this.bxSlider.startAuto()
		});
	}*/
});
