//'use strict'
jQuery.noConflict();
(function($) {
    $(function() {
        var motion1      = $('#motion1');
        var motion2      = $('#motion2');
        var start        = $('#start');
        var cap          = $('#cap');
        var fwk_cv       = $('#fwk_cv');
        var motionSpeed  = 115;
        var WaitMotion   = 650;
        var WaitAlert    = 600;
        var SlideSprite1 = 91;
        var SlideSprite2 = 166;
        var ReccomendNum = 4;
        var arr          = new Array('pickUp');
        var ResultArr    = new Array('hit', 'twobase', 'homerun', 'foul');
        var ResultLength = ResultArr.length;
        var i, k, tmp;

        //ReadyAction
        fwk.init();
        $('#ToOutWrap').insertAfter('#wrapper_landscape');

        //ランダム処理 
        //motion後の訴求をランダムにPickup
        function Random() {
            for (i = 0; i < ReccomendNum; i++) {
                k = i;
                while (k == i)
                    k = Math.floor(Math.random() * ReccomendNum);
                tmp = arr[i];
                arr[i] = arr[k];
                arr[k] = tmp;
            }
            //ランダム class('hit','twobase','homerun','foul')
            for (i = 0; i < ResultLength; i++) {
                k = i;
                while (k == i)
                    k = Math.floor(Math.random() * ResultLength);
                tmp = ResultArr[i];
                ResultArr[i] = ResultArr[k];
                ResultArr[k] = tmp;
            }
            //ランダム class('Pickup')
            for (i = 0; i < ReccomendNum; i++) {
                $('#rec' + i).addClass(arr[0 + i]);
            }
            $('.pickUp .alert').attr('id', 'IdAlert');
            var pickUp = $('.pickUp');
            var Sub = $('.pickUp .sub');
            setTimeout(function() {
                pickUp.addClass(ResultArr[0]);
            }, 400);
            setTimeout(function() {
                if (pickUp.hasClass('hit')) {
                    Sub.html('ナイスバッティング！');
                } else if (pickUp.hasClass('twobase')) {
                    Sub.html('バッチこーーーい！');
                } else if (pickUp.hasClass('foul')) {
                    Sub.html('切り替えていこう！！');
                } else if (pickUp.hasClass('homerun')) {
                    Sub.html('ヘイヘイピッチャービビってる！');
                }
            }, 700);
        }
        Random();

        //SpriteAnimation
        start.on('click', function() {
            $(this).hide();
            cap.hide();
            //クリック数取得
            //_gaq.push(['_trackEvent', 'Links', 'Click', $(this).attr('href')]);
            setTimeout(function() {
                var timer = setInterval(changeImg, motionSpeed)
                i = 0;

                function changeImg() {
                    motion1.css('background-position-y', -SlideSprite1 * i + 'px');
                    if (i <= 7) {
                        i++;
                    } else if (i == 8) {
                        motion1.css('background-position-y', SlideSprite1 + 'px');
                        motion2.css('background-position-y', -SlideSprite2 * (i - 7) + 'px');
                        i++;
                    } else if (i <= 10) {
                        motion2.css('background-position-y', -SlideSprite2 * (i - 7) + 'px');
                        i++;
                    } else if (i == 11) {
                        setTimeout(function() {
                            Alert();
                        }, WaitAlert);
                        clearInterval(timer)
                    }
                };
            }, WaitMotion);
            return false;
        });

        //close
        $('.close').on('click', function() {
            Clear();
        });
        //Show Alert
        function Alert() {
                var pickUp = $('.pickUp');
                var _$land_height = $(document).height();
                $('html').css({
                    '-webkit-tap-highlight-color': 'rgba(0,0,0,0)'
                });
                $('body').css({
                    '-webkit-tap-highlight-color': 'rgba(0,0,0,0)'
                });
                if (_$land_height < $(window).height()) {
                    _$land_height = $(window).height()
                }
                pickUp.css('height', _$land_height).fadeIn('fast').on('touchmove', function(e) {
                    e.preventDefault();
                });
                $('#IdAlert').on('touchmove', function(e) {
                    e.preventDefault();
                });
                //homerunで花火
                if ($('.reccomend').hasClass('homerun')) {
                    fwk_cv.prependTo(pickUp).css('display', 'block');
                    pickUp.css('background', '#000');
                }
            }
            //Clear Alert
        function Clear() {
            $('html').css({
                '-webkit-tap-highlight-color': 'initial'
            });
            $('body').css({
                '-webkit-tap-highlight-color': 'initial'
            });
            $('.pickUp').hide().removeClass('foul hit twobase homerun pickUp').css('background', 'rgba(0, 0, 0, 0.5)');
            $('.alert').attr('id', '');
            start.show();
            cap.show();
            motion1.css('background-position-y', 0 + 'px');
            motion2.css('background-position-y', 162 + 'px');
            Random();
            fwk_cv.hide();
        }
        $('#start').show();
    });
})(jQuery);