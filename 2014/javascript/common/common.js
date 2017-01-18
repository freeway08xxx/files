(function($){

	/*
	表示位置設定 画面の中央に表示
	----------------------------------------------------------------------*/
	$.fn.adjustCenter = function () {
		var that = this,
		w = that.outerWidth()/2,
		t = that.outerHeight()/2,
		s = (document.documentElement.scrollTop > 0) ? document.documentElement.scrollTop: document.body.scrollTop,
		dH = $(document).height(),
		cH = document.documentElement.clientHeight;

		that.css({"top":s+(cH/2)+"px","margin-top":-t+"px", "margin-left":-w+"px"});
		that.parent().css({"min-height":dH,"max-height":dH});
	}

	/*
	ダイアログ表示
	----------------------------------------------------------------------*/
	$.fn.dialog = function (modalClass) {

		$(this).on('click',function(event) {

			event.preventDefault();
			document.addEventListener("touchmove", touchHandler, false);
			$('body').css("position","relative");
			$('a').addClass('no_highlight');
			modalClass.css("display", "block");
			modalClass.find('.js-t-modal__content').adjustCenter();

		});

		modalClass.find('.js-t-cancell,.js-t-modal__overlay').off('click').on('click',function(event) {
			modalClass.css("display", "none");
			$('body').css("position","");
			$('a').removeClass('no_highlight');
			document.removeEventListener("touchmove", touchHandler);
		});

		var touchHandler = function (event) {
			event.preventDefault();
		}

	};

	/*
	検索xボタン
	----------------------------------------------------------------------*/
	$.fn.inputOff = function () {

		var $input = this.find('input'),
		$clear = this.find('.js-search__clear');	

        var timer = setInterval(function () {
            switching();
        }, 500);

       $clear.on('click', function(){
			var taht = $(this);
            taht.parent().find('input').val('');
            taht.css('display','none');
    	});

       	var switching =  function (input) {
            ($input.val() == '') ? $clear.css('display','none') : $clear.css('display','block');
        }
	};

	/*
	ドロップダウンメニュー開閉
	----------------------------------------------------------------------*/
	$('.js-t-dropdown').on("click",function(){
		var that = $(this);
		that.next().toggle();
		that.toggleClass("t-arrow_up t-arrow_down");
	});

	/*
	アコーディオン開閉
	----------------------------------------------------------------------*/
	$('.js-t-accordion').on('click',function(){
		var that = $(this);
		that.next().slideToggle();
		that.toggleClass("t-arrow_down t-arrow_up");
	});

	/*
	トップへ戻る
	----------------------------------------------------------------------*/
	$('.js-t-totop').on('click',function(){
		window.scrollTo(0,0);
	});

	/*
	タブ
	----------------------------------------------------------------------*/
	var $tab =  $('.js-t-tab__navs li'),
	$tabItems =  $(".js-t-tab__items li"),
	activeClass = "t-tab__menu--active";
		
	$tab.on('click',function(){

		$tab.removeClass(activeClass);
		$(this).addClass(activeClass);
 		$tabItems.css("display","none").eq($tab.index($(this))).css("display","block");
 		
	});


})(jQuery);






