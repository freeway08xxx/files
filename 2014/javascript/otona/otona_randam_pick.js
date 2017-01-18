$(function() {
	$('.slide').css('visibility', 'visible');
	var js_slide1 = $('.js_slide1');
	var copySrc = js_slide1.attr('src');
	var cut = 0;
	var timer = setInterval(function() {
		cut++;
		if (cut == 4) {
			clearInterval(timer)
		}
	}, 2000);

	function responsive() {
		var copyHeight = js_slide1.height();
		$('.js_slide3').css({
			'background-image': 'url(' + copySrc + ')'
		}).height(copyHeight);
		$('.js_slide3 a,.slider1,.bx-viewport').height(copyHeight)
	}
	responsive();
	$(window).on('orientationchange resize', function() {
		setTimeout(function() {
			responsive()
		}, 50)
	});

	function resize() {
		$('.top_list_side li').each(function() {
			var clmH = 0;
			if ($(this).height() > clmH) {
				clmH = $(this).height()
			}
			$('.top_list_side li').css({
				'min-height': clmH
			})
		})
	}
	resize();
	$(window).on('orientationchange resize', function() {
		resize()
	})
});
jQuery.event.add(window, "load", function() {
	$('.bx-controls').insertBefore('.bx-viewport');
	var getImgNum = 11;
	var randamImg = 4;
	var tmp, i, k, j;
	var randamBox = [];
	setTimeout(function() {
		$.ajax({
			url: '/android/otona/gravure.html',
			type: 'GET',
			timeout: 4000,
			dataType: 'html',
		}).done(function(data) {
			for (var i = 1; i <= getImgNum; i++) {
				var obj = {
					img: $('.img:nth-child(' + i + ')' + ' img', $(data)).attr('data-original'),
					linkUrl: $('.img:nth-child(' + i + ') a', $(data)).attr('href')
				};
				randamBox.push({
					img: obj.img,
					linkUrl: obj.linkUrl
				})
			}
		}).always(function() {
			if (randamBox.length === getImgNum) {
				for (i = 0; i < randamBox.length; i++) {
					k = i;
					while (k == i) k = Math.floor(Math.random() * randamBox.length);
					tmp = randamBox[i];
					randamBox[i] = randamBox[k];
					randamBox[k] = tmp
				}
				for (j = 0; j < randamImg; j++) {
					$('.list').append('<li><a href="' + randamBox[j].linkUrl + '">\<img alt="ピックアップ画像" width="70" height="70" src="' + randamBox[j].img + '"></a>\</li>')
				}
			}
		}).error(function(data) {});
		$('.random_area img').error(function() {
			$(this).remove()
		})
	}, 600)
})
