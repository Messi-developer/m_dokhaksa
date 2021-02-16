var autoMap = {
	/* global variable */
	_gv:function(){
		_this._ori = {};
		_this._setW = {};
		_this._tImg = {};
	},
	init:function(){
		_this = this;
		_this._gv();

		$("map").each(function(){
			var _thisM = $(this);
			if(_thisM.attr("map-autoresize") != undefined){
				if(_thisM.attr("map-size") != undefined){
					_this._setW[_thisM.attr("name")] = _thisM.attr("map-size");
				}else{
					_this._setW[_thisM.attr("name")] = 640;
				}
				_this._ori[_thisM.attr("name")] = {};

				_this._tImg[_thisM.attr("name")] = $("img[usemap=#"+_thisM.attr("name")+"]");

				$(this).find("area").each(function() {
					_this._ori[_thisM.attr("name")][$(this).index()]= ($(this).attr("coords")).split(',');
				});
			}
		});

		_this.printMap();
	},
	printMap:function(){
		$("map").each(function(){
			var _thisM = $(this);
			if(_thisM.attr("map-autoresize") != undefined){
				var _thisM = $(this);
				_thisM.find("area").each(function() {
					$(this).attr("coords", _this.creatCoords(_this._ori[_thisM.attr("name")][$(this).index()], _this._setW[_thisM.attr("name")], _this._tImg[_thisM.attr("name")]));
				});
			}
		})
	},
	creatCoords:function(_obj, _setW, _img){
		var _val = _obj;
		var _coords = ""
		var _w = parseInt(_img.width());
		for(i=0; i<_val.length; i++){
			if(i==0){
				_coords += parseInt(_val[i] * _w / _setW);
			}else{
				_coords += "," + parseInt(_val[i] * _w / _setW)
			}
		}
		return _coords;
	}
}

$(function(){
	setTimeout(function(){autoMap.init();},500);

	$(window).resize(function(){
		autoMap.printMap();
	})
});