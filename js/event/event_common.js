$(document).ready(function () {
	$(".js-tab-type2").each(function () {
		var tabBtn = $(this).children("li.tab").children("a");

		// 탭버튼을 클릭했을때
		tabBtn.click(function () {
			var idx = $(this).parent().index();
			// 이미 on 상태면 pass
			if ($(this).hasClass("on")) return;
			tabBtn.removeClass("on");
			$(this).addClass("on");

			tabBtn.each(function () {
				var src;
				var img = $(this).children("img");
				if ($(this).hasClass("on")) {
					src = img.attr("src").replace("-off.png", "-on.png");
				} else {
					src = img.attr("src").replace("-on.png", "-off.png");

				}
				img.attr("src", src);
			});
			return false;
		});
	});
	
	$(".js-tab-type2 li.tab a").click(function () {
		var tab_idx = $(this).parent().index();
		var $tab = $(this).parent().parent();
		if ($tab.hasClass(".arrow_type") == 0) { //true,0 //false,1
			$("li", $tab).removeClass("on off");
			$("li", $tab).eq(tab_idx).addClass("on");
			$("li", $tab).eq(tab_idx - 1).addClass("off");
		}

		else {
			$("li", $tab).removeClass("on");
			$("li", $tab).eq(tab_idx).addClass("on");
		}
		$tab.parent().find(".js-tab-type2-con").hide();
		$tab.parent().find(".js-tab-type2-con").eq(tab_idx).show();
		return false;
	});
});

 
function call_tel() {
	window.open('tel:1599-3081');
}
//move_scroll
function move_scroll(obj){
	var _t = $("#"+obj).offset().top;
	$("html,body").animate({
		scrollTop:_t+"px"
	},800);//속도
}
// 레이어 열기
function e_layer_open(el){
	$(document).ready(function(){
		$("#"+el).show();
		autoMap.printMap();
	});
}

// 레이어 닫기
function e_layer_close(el){
	$(document).ready(function(){
		$("#"+el).hide();
	});
}