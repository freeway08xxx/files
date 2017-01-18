$(function(){

	//凧揚バージョン
	if ($('#kite').hasClass('active')) {
		var p = $('#p');
		var kite_changeImg;
		var kite_motionSpeed = 950;
		var kite_SlideSprite = 90;
		setTimeout(function() {
			setInterval(kite_changeImg, kite_motionSpeed);
			var i = 1;

			function kite_changeImg(){
				p.css('background-position-y', -kite_SlideSprite * i + 'px');
				if (i <= 3) {
					i++;
				} else if (i == 4) {
					p.css('background-position-y', 0 + 'px');
					i = 0;
					i++;
				}
			}
		}, 0);
	}
	
	//獅子舞バージョン
	if ($('#shishimai').hasClass('active')) {
		$(window).load(function(event) {
			var LionAhead = $('#LionAhead');
			var LionBehind = $('#LionBehind');
			var LionBite = $('#bite');
			var MoveInterval = 180;
			var MoveNum = 4;
			var SpriteInterval = 100;
			var HideBg = 100;
			var LionSpeed = 400;
			setTimeout(function() {
				var timer = setInterval(changeImg, LionSpeed);
				var i = 1;

				function changeImg() {
					LionAhead.css('background-position-y', -SpriteInterval * i + 'px');
					if (i <= 3) {
						i++;
					} else if (i <= 4) {
						LionAhead.css('background-position-y', HideBg + 'px');
						LionBehind.css('background-position-y', -SpriteInterval * (i - 4) + 'px');
						i++;
					} else if (i <= 7) {
						LionBehind.css('background-position-y', -SpriteInterval * (i - 4) + 'px');
						i++;
					} else if (i == 8) {
						LionBehind.css('background-position-y', HideBg + 'px');
						LionBite.css('background-position-y', 0 + 'px');
						i++;
					} else if (i <= 9) {
						LionBite.css('background-position-y', -SpriteInterval * (i - 8) + 'px');
						i++;
					} else if (i <= 10) {
						$('#baloon04').show();
						i++;
					} else if (i == 11) {
						setTimeout(function() {
							$('#baloon05').fadeIn();
							LionBite.addClass('ready');
							clearInterval(timer);
						}, 500);
					}
				}
			}, 1000);
			LionBite.on('click', function() {
				if (LionBite.hasClass('ready')) {
					var counter = 1;
					var timer = setInterval(function() {
						BiteAction();
						if (counter >= MoveNum) {
							clearInterval(timer);
						}
						counter++;
					}, MoveInterval * 2);
				}
			});

			function BiteAction() {
				LionBite.removeClass('ready');
				setTimeout(function() {
					LionBite.css('background-position-y', (-100 + (-SpriteInterval)) + 'px');
				}, 0);
				setTimeout(function() {
					LionBite.css('background-position-y', -100 + 'px');
				}, MoveInterval);
				setTimeout(function() {
					LionBite.addClass('ready');
				}, MoveInterval * (MoveNum + 2))
			};
		});
	};
	
	//初詣バージョン
	if ($('#pray').hasClass('active')) {
		$(window).load(function() {
			var p_1 = $('#p_1');
			var p_2 = $('#p_2');
			var p_3 = $('#p_3');
			var pAll = 3;
			for (i = 1; i <= pAll; i++) {
				ImgParapara($('#p_' + i));
			}
			p1Move();
			p2Move();
			p3Move();
			var ResultArr = new Array('GreatBlessing', 'MiddleBlessing', 'Blessing');
			var ResultLength = ResultArr.length;
			var i, k, tmp;
			var God = $('#God');
			var dfd = $.Deferred();
			var baloon01 = $('#baloon01');
			var baloon02 = $('#baloon02');
			var baloon03 = $('#baloon03');
			Random();
			baloon01.one('click', function() {
				baloon01.fadeOut();
				setTimeout(function() {
					baloon01.addClass(ResultArr[1]);
					baloon01.fadeIn();
				}, 1200);
			});

			function Random() {
				for (i = 0; i < ResultLength; i++) {
					k = i;
					while (k == i) k = Math.floor(Math.random() * ResultLength);
					tmp = ResultArr[i];
					ResultArr[i] = ResultArr[k];
					ResultArr[k] = tmp;
				}
			}

			function p1Move() {
				p_1.css({
					'-webkit-transform': 'translate3d(-105px,0,0)',
					'-webkit-transition': '-webkit-transform 18000ms cubic-bezier(0,0,1,1)'
				}).on('webkitTransitionEnd', function() {
					p_1.addClass('stop backFlg');
					setTimeout(function() {
						p_1.removeClass('stop').css({
							'-webkit-transform': 'translate3d(175px,0,0)',
							'-webkit-transition': '-webkit-transform 32000ms cubic-bezier(0,0,1,1)'
						});
					}, 1600);
					setTimeout(function() {
						p_1.removeClass('backFlg');
						p1Move();
					}, 31000);
				});
			}

			function p2Move() {
				p_2.css({
					'-webkit-transform': 'translate3d(-170px,0,0)',
					'-webkit-transition': '-webkit-transform 27000ms cubic-bezier(0,0,1,1)'
				}).on('webkitTransitionEnd', function() {
					p_2.addClass('stop backFlg');
					setTimeout(function() {
						p_2.removeClass('stop').css({
							'-webkit-transform': 'translate3d(100px,0,0)',
							'-webkit-transition': '-webkit-transform 27000ms cubic-bezier(0,0,1,1)'
						});
					}, 2000);
					setTimeout(function() {
						p_2.removeClass('backFlg');
						p2Move();
					}, 27000);
				});
			}

			function p3Move() {
				p_3.css({
					'-webkit-transform': 'translate3d(-257px,0,0)',
					'-webkit-transition': '-webkit-transform 37000ms cubic-bezier(0,0,1,1)'
				}).on('webkitTransitionEnd', function() {
					p_3.addClass('stop backFlg');
					setTimeout(function() {
						p_3.removeClass('stop').css({
							'-webkit-transform': 'translate3d(0px,0,0)',
							'-webkit-transition': '-webkit-transform 33000ms cubic-bezier(0,0,1,1)'
						});
					}, 1800);
					setTimeout(function() {
						p_3.removeClass('backFlg');
						p3Move();
					}, 33000);
				});
			}
			dfd.then(function() {
				setTimeout(function() {
					God.css({
						'-webkit-transform': 'translate3d(50px,0,0)',
						'-webkit-transition': '-webkit-transform 200ms cubic-bezier(0,0,0.3,1)'
					});
				}, 1000);
			}).then(function() {
				setTimeout(function() {
					God.css('background-position-x', 45 + 'px');
				}, 2400);
			}).then(function() {
				setTimeout(function() {
					God.css('background-position-x', 100 + '%');
				}, 3100);
			}).then(function() {
				setTimeout(function() {
					baloon01.fadeIn('slow');
				}, 4000);
			}).then(function() {
				setTimeout(function() {
					baloon02.fadeIn('slow'
				}, 8000);
		scss	}).then(function() {
				setTimeout(function() {
					baloon02.fadeOut('slow');
					baloon03.fadeIn('slow');
				}, 10000);
			}).then(function() {
				setTimeout(function() {
					baloon03.fadeOut('slow');
				}, 12000);
			});
			dfd.resolve();

			function ImgParapara(p_num) {
				var pray_changeImg;
				var pray_motionSpeed = 700;
				var pray_SlideSprite = 62;
				setTimeout(function() {
					setInterval(pray_changeImg, pray_motionSpeed);
					var i = 1;

					function pray_changeImg() {
						p_num.css('background-position-y', -pray_SlideSprite * i + 'px');
						if (i <= 1) {
							i++;
						} else if (i == 2) {
							p_num.css('background-position-y', 0 + 'px');
							i = 0;
							i++;
						}
					}
				}, 0);
			}
		});
	}
});
