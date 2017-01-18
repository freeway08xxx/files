jQuery.noConflict();
(function($) {
    $(function() {

        //delay
        $(window).load(function() {
            var i = 1;
            var delaySpeed = 220;
            var fadeSpeed = 400;

            $('.out_top').each(function(i) {
                $(this).delay(i * (delaySpeed)).css({
                    visibility: 'visible',
                    opacity: '0'
                }).animate({
                    opacity: '1'
                }, fadeSpeed);

            });

        });

        //hover
        $('.out_top, #title').hover(function() {
                $(this).fadeTo(100, 0.6);
            },

            function() {
                $(this).fadeTo(0, 1.0);
            });



        //ShakeAnimation
        var ShakeNum = 2;
        var ShakeInterval = 170;
        //振る動き
        var action = $('#action');

        function shake() {
            $(action).removeClass('ready');

            //ShakeIntervalの間隔で.onをtoggle
            setTimeout(function() {
                action.addClass('on');
            }, 0);

            setTimeout(function() {
                action.removeClass('on');
            }, ShakeInterval);

            setTimeout(function() {
                action.addClass('ready');
            }, ShakeInterval * 2)
        }

        //Eventmouseover
        action.mouseover(function() {
            if (action.hasClass('ready')) {
                shake();

                //set timer
                var counter = 1;
                var timer = setInterval(function() {
                    shake();

                    //ShakeNumまで繰り返し
                    if (counter >= ShakeNum) {
                        clearInterval(timer);
                    }
                    counter++;
                }, ShakeInterval * 2);
            }
        });
    });

})(jQuery);