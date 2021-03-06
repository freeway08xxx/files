/**
 * BxSlider v4.1.2 - Fully loaded, responsive content slider
 * http://bxslider.com
 *
 * Copyright 2014, Steven Wanderski - http://stevenwanderski.com - http://bxcreative.com
 * Written while drinking Belgian ales and listening to jazz
 *
 * Released under the MIT license - http://opensource.org/licenses/MIT
 */

;
(function($) {
    var plugin = {};
    var defaults = {
        slideSelector: '',
        infiniteLoop: false,
        speed: 350,
        slideMargin: 0,
        startSlide: 0,
        wrapperClass: 't-bx__wrapper',
        touchEnabled: true,
        swipeThreshold: 20,
        preventDefaultSwipeX: true,
        preventDefaultSwipeY: true,
        pager: false,
        pagerClick: true,
        pagerLarge: false,
        controls: false,
        auto: false,
        pause: 4500,
        appearArrowNum: -1,
        moveSlides: 0,
        slideWidth: 0,
        centering: false,
        loadAnimation: false,
        showBothImg: false,
        onSliderLoad: function() {},
        onSlideBefore: function() {},
        onSlideAfter: function() {},
        onSliderResize: function() {}
    };
    $.fn.bxSlider = function(options) {
        if (this.length == 0) return this;
        if (this.length > 1) {
            this.each(function() {
                $(this).bxSlider(options)

            });
            return this
        }
        var slider = {};
        var el = this;

        plugin.el = this;
        var windowWidth = $(window).width();
        var windowHeight = $(window).height();
        var init = function() {
            slider.settings = $.extend({}, defaults, options);
            slider.settings.slideWidth = parseInt(slider.settings.slideWidth);
            slider.children = el.children(slider.settings.slideSelector);

            if (slider.settings.loadAnimation) {
                var lastNum = el.find('> div').length - 1;
                slider.active = {
                    index: lastNum
                };
            } else {
                slider.active = {
                    index: slider.settings.startSlide
                };
            }
            slider.carousel = 1 > 1;
            if (slider.carousel) {
                slider.settings.preloadImages = 'all'
            };
            slider.minThreshold = slider.settings.slideWidth;
            slider.maxThreshold = slider.settings.slideWidth;
            slider.working = false;
            slider.controls = {};
            slider.interval = null;
            slider.usingCSS = (function() {
                var a = document.createElement('div');
                var b = ['WebkitPerspective', 'MozPerspective', 'OPerspective', 'msPerspective'];
                for (var i in b) {
                    if (a.style[b[i]] !== undefined) {
                        slider.cssPrefix = b[i].replace('Perspective', '').toLowerCase();
                        slider.animProp = '-' + slider.cssPrefix + '-transform';
                        return true
                    }
                };
                return false
            }());
            setup();
        };
        setup = function() {
            el.wrap('<div style="max-width:100%;" class="' + slider.settings.wrapperClass + '"><div class="t-bx__viewport"></div></div>');
            slider.viewport = el.parent();
            slider.viewport.prepend(slider.loader);
            el.css({
                width: (slider.children.length * 100 + 215) + '%',
                position: 'relative'
            });
            var a = getNumberSlidesShowing();
            slider.viewport.css({
                width: '100%',
                overflow: 'hidden',
                position: 'relative'
            });


            slider.children.css({
                'float': 'left',
                position: 'relative'
            });
            slider.children.css('width', getSlideWidth());
            if (slider.settings.slideMargin > 0) {
                slider.children.css('marginRight', slider.settings.slideMargin)
            };
            if (slider.settings.slideMargin > 0) {
                slider.children.css('marginBottom', slider.settings.slideMargin)
            };


            slider.controls.el = $('<div class="t−bx__controls" />');
            slider.active.last = slider.settings.startSlide == getPagerQty() - 1;
            var b = slider.children.eq(slider.settings.startSlide);
            if (slider.settings.preloadImages == "all") {
                b = slider.children
            };

            if (slider.settings.pager) {
                appendPager()
            };
            if (slider.settings.pager) {
                slider.viewport.after(slider.controls.el)
            };

            loadElements(b, start)
        };

        var loadElements = function(a, b) {
            var c = a.find('img').length;
            if (c == 0) {
                b();
                return
            }
            var d = 0;
            a.find('img').each(function() {
                $(this).one('load', function() {
                    if (++d == c) b()
                }).each(function() {
                    if (this.complete) $(this).load()
                })
            })
        };
        var start = function() {
            if (slider.settings.infiniteLoop) {

                var a = 1;
                var aPlus = 2
                var b = slider.children.slice(0, a).clone().addClass('t-bx__clone');
                var bPlus = slider.children.slice(1, aPlus).clone().addClass('t-bx__clone');
                var c = slider.children.slice(-a).clone().addClass('t-bx__clone');

                el.append(b).prepend(c);
                el.append(bPlus)

            }
            setSlidePosition();
            slider.viewport.height(getViewportHeight());
            el.redrawSlider();
            slider.settings.onSliderLoad(slider.active.index);
            slider.initialized = true;



            if (slider.settings.auto && (getPagerQty() > 1)) {
                initAuto()
            };
            if (slider.settings.pager) {
                updatePagerActive(slider.settings.startSlide)
            };
            if (slider.settings.controls) {
                updateDirectionControls()
            };
            if (slider.settings.touchEnabled) {
                initTouch()
            }
            if (slider.settings.showBothImg) {

                slider.settings.swipeThreshold = 5;

                el.find('> div').css('text-align', 'center')
                var biginNum = 0;
                if ((el.find('> div')).hasClass('t-bx__clone')) {
                    biginNum = biginNum + 1;
                }

                el.find('> div').eq(biginNum).addClass('current')

                //一つの時	
                var ImgMargin = 10;
                var imgLength = el.find('img').length;

                if (imgLength <= 3) {
                    el.find('.t-bx__clone').remove();
                };

                el.find('.current').prev('div').addClass('js-t-prevCurrent');
                el.find('.current').next('div').addClass('js-t-nextCurrent');
                el.find('.js-t-nextCurrent').css({
                    'margin-left': '-20px',
                    'text-align': 'left'
                });

                resize();
                $(window).resize(function() {
                    resize();
                });

                el.find('.t-bx__pager-link').on('click', function() {
                    pagerClick($(this));
                });
                el.find('img').css('max-width', '300px')

                var imgCurrentW = el.find('.current img').width();
                if (imgCurrentW >= 270) {
                    $('.js-t-nextCurrent').css('visibility', 'hidden')
                }
                if (imgCurrentW >= 270) {
                    $('.js-t-nextCurrent').css('visibility', 'hidden')
                }
            };


            if (slider.settings.centering) {

                var valMarginRight = slider.settings.slideWidth / 2;
                el.wrap('<div class="bx" style="width:' + slider.settings.slideWidth + 'px;position:absolute;left:50%;margin-left:-' + valMarginRight + 'px;overflow:hidden;"></div>');
            }
            if (slider.settings.loadAnimation) {



                var onetimeFlg = true;
                var keepVal = slider.settings.speed;
                slider.settings.speed = 1350;
                setTimeout(function() {
                    el.goToSlide(0);
                }, 700);

                if (onetimeFlg) {
                    slider.settings.onSlideAfter = function() {
                        slider.settings.speed = keepVal;
                        onetimeFlg = false;

                    };
                }
            };

            if (slider.settings.appearArrowNum >= 0) {

                var elm = el.attr('class').split(' ');
                var innnerNum = $('.' + elm[0] + ' img').length - 1;

                if (innnerNum >= slider.settings.appearArrowNum) {
                    el.before('<div class="t-bx__arrow" style="position:absolute;z-index:1;"><p>⇒</p></div>');
                }


            }

        };


        var getViewportHeight = function() {
            var height = 0;
            var children = $();
            if (!slider.carousel) {
                children = slider.children.eq(slider.active.index)
            } else {
                var currentIndex = slider.settings.moveSlides == 1 ? slider.active.index : slider.active.index * getMoveBy();
                children = slider.children.eq(currentIndex);
                for (i = 1; i <= 1 - 1; i++) {
                    if (currentIndex + i >= slider.children.length) {
                        children = children.add(slider.children.eq(i - 1))
                    } else {
                        children = children.add(slider.children.eq(currentIndex + i))
                    }
                }
            }
            height = Math.max.apply(Math, children.map(function() {
                return $(this).outerHeight(false)
            }).get());
            if (slider.viewport.css('box-sizing') == 'border-box') {
                height += parseFloat(slider.viewport.css('padding-top')) + parseFloat(slider.viewport.css('padding-bottom')) + parseFloat(slider.viewport.css('border-top-width')) + parseFloat(slider.viewport.css('border-bottom-width'))
            } else if (slider.viewport.css('box-sizing') == 'padding-box') {
                height += parseFloat(slider.viewport.css('padding-top')) + parseFloat(slider.viewport.css('padding-bottom'))
            }
            return height
        };
        var getViewportMaxWidth = function() {
            var width = '100%';
            if (slider.settings.slideWidth > 0) {
                width = slider.settings.slideWidth

            }
            return width

        };
        var getSlideWidth = function() {
            var newElWidth = slider.settings.slideWidth;
            var wrapWidth = slider.viewport.width();
            if (slider.settings.slideWidth == 0 || (slider.settings.slideWidth > wrapWidth && !slider.carousel)) {} else if (1 > 1) {
                if (wrapWidth > slider.maxThreshold) {} else if (wrapWidth < slider.minThreshold) {
                    newElWidth = wrapWidth
                }
            }
            return newElWidth
        };
        var getNumberSlidesShowing = function() {
            var slidesShowing = 1;
            if (slider.settings.slideWidth > 0) {
                if (slider.viewport.width() < slider.minThreshold) {
                    slidesShowing = 1
                } else if (slider.viewport.width() > slider.maxThreshold) {
                    slidesShowing = 1
                } else {
                    var childWidth = slider.children.first().width() + slider.settings.slideMargin;
                    slidesShowing = Math.floor((slider.viewport.width() + slider.settings.slideMargin) / childWidth)
                }
            }
            return slidesShowing
        };
        var getPagerQty = function() {
            var pagerQty = 0;
            if (slider.settings.moveSlides > 0) {
                if (slider.settings.infiniteLoop) {
                    pagerQty = Math.ceil(slider.children.length / getMoveBy())
                } else {
                    var breakPoint = 0;
                    var counter = 0;
                    while (breakPoint < slider.children.length) {
                        ++pagerQty;
                        breakPoint = counter + getNumberSlidesShowing();
                        counter += slider.settings.moveSlides <= getNumberSlidesShowing() ? slider.settings.moveSlides : getNumberSlidesShowing()
                    }
                }
            } else {
                pagerQty = Math.ceil(slider.children.length / getNumberSlidesShowing())
            };
            return pagerQty
        };
        var getMoveBy = function() {
            if (slider.settings.moveSlides > 0 && slider.settings.moveSlides <= getNumberSlidesShowing()) {
                return slider.settings.moveSlides
            }
            return getNumberSlidesShowing()
        };
        var setSlidePosition = function() {
            if (slider.children.length > 1 && slider.active.last && !slider.settings.infiniteLoop) {

                var lastChild = slider.children.last();
                var position = lastChild.position();
                setPositionProperty(-(position.left - (slider.viewport.width() - lastChild.outerWidth())), 'reset', 0)

            } else {
                var position = slider.children.eq(slider.active.index * getMoveBy()).position();
                if (slider.active.index == getPagerQty() - 1) slider.active.last = true;
                if (position != undefined) {

                    setPositionProperty(-position.left, 'reset', 0)

                }
            }
        };
        var setPositionProperty = function(value, type, duration, params) {
            if (slider.usingCSS) {
                var propValue = 'translate3d(' + value + 'px, 0, 0)';
                el.css('-' + slider.cssPrefix + '-transition-duration', duration / 1000 + 's');
                if (type == 'slide') {
                    el.css(slider.animProp, propValue);

                    setTimeout(function() {
                        updateAfterSlideTransition();
                    }, 300);



                    el.bind('transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd', function() {

                        el.unbind('transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd');

                    })
                } else if (type == 'reset') {
                    el.css(slider.animProp, propValue)
                }
            }
        };
        var populatePager = function() {
            var pagerHtml = '';
            var pagerQty = getPagerQty();


            if (slider.settings.pagerLarge) {
                slider.pagerEl.addClass('t-bx__large-pager');
            } else {
                slider.pagerEl.addClass('t-bx__default-pager');
            }


            for (var i = 0; i < pagerQty; i++) {
                var linkContent = '';
                linkContent = i + 1;
                if (slider.settings.pagerClick) {
                    pagerHtml += '<div class="t-bx__pager-item"><a href="" data-slide-index="' + i + '" class="t-bx__pager-link">' + linkContent + '</a></div>'
                } else {

                    pagerHtml += '<div class="t-bx__pager-item"><a style="-webkit-tap-highlight-color:rgba(0,0,0,0);" href="" data-slide-index="' + i + '" class="t-bx__pager-link">' + linkContent + '</a></div>'
                }
            };

            slider.pagerEl.html(pagerHtml)
        };
        var appendPager = function() {

            slider.pagerEl = $('<div class="t-bx__pager" />');
            slider.controls.el.addClass('bx-has-pager').append(slider.pagerEl);
            populatePager();

            slider.pagerEl.on('click', 'a', clickPagerBind)
        };
        var clickStartBind = function(e) {
            el.startAuto();
            e.preventDefault()
        };
        var clickStopBind = function(e) {
            e.preventDefault()
        };
        var clickPagerBind = function(e) {

            if (slider.settings.pagerClick) {
                var pagerLink = $(e.currentTarget);
                var pagerLink = $(e.currentTarget);
                if (pagerLink.attr('data-slide-index') !== undefined) {
                    var pagerIndex = parseInt(pagerLink.attr('data-slide-index'));
                    if (pagerIndex <= slider.active.index) {
                        el.goToSlide(pagerIndex, 'prev');
                        e.preventDefault();
                    } else if (pagerIndex != slider.active.index) {
                        el.goToSlide(pagerIndex, 'next');
                        e.preventDefault();
                    }
                }
            } else {

                e.preventDefault();
            };
        };
        var updatePagerActive = function(slideIndex) {
            var len = slider.children.length;

            slider.pagerEl.find('a').removeClass('active');
            slider.pagerEl.each(function(i, el) {
                $(el).find('a').eq(slideIndex).addClass('active')
            })
        };
        var updateAfterSlideTransition = function() {
            if (slider.settings.infiniteLoop) {
                var position = '';

                if (slider.active.index == 0) {

                    position = slider.children.eq(0).position()

                } else if (slider.active.index == getPagerQty() - 1 && slider.carousel) {
                    position = slider.children.eq((getPagerQty() - 1) * getMoveBy()).position()
                } else if (slider.active.index == slider.children.length - 1) {
                    position = slider.children.eq(slider.children.length - 1).position()
                }
                if (position) {

                    setPositionProperty(-position.left, 'reset', 0)

                }
            }
            slider.working = false;
            slider.settings.onSlideAfter(slider.children.eq(slider.active.index), slider.oldIndex, slider.active.index);
            if (slider.settings.showBothImg) {
                resize()
            };
        };
        var updateAutoControls = function(state) {
            slider.controls.autoEl.find('a').removeClass('active');
            slider.controls.autoEl.find('a:not(.bx-' + state + ')').addClass('active')
        };
        var initAuto = function() {
            el.startAuto()
        };
        var initTouch = function() {
            slider.touch = {
                start: {
                    x: 0,
                    y: 0
                },
                end: {
                    x: 0,
                    y: 0
                }
            };
            slider.viewport.bind('touchstart', onTouchStart)
        };
        var onTouchStart = function(e) {

            el.stopAuto();
            if (slider.working) {

                e.preventDefault()
            } else {
                slider.touch.originalPos = el.position();
                var orig = e.originalEvent;
                slider.touch.start.x = orig.changedTouches[0].pageX;
                slider.touch.start.y = orig.changedTouches[0].pageY;
                slider.viewport.bind('touchmove', onTouchMove);
                slider.viewport.bind('touchend', onTouchEnd)
            }
        };
        var onTouchMove = function(e) {
            if (slider.settings.showBothImg) {
                el.find('.js-t-nextCurrent, .js-t-prevCurrent').css({
                    'text-align': 'center',
                    'margin': '0 auto'
                });
                el.find('> div').removeClass('current js-t-nextCurrent js-t-prevCurrent');
                el.find('.js-clone-next,.js-clone-prev').remove();
            }

            var orig = e.originalEvent;
            var xMovement = Math.abs(orig.changedTouches[0].pageX - slider.touch.start.x);
            var yMovement = Math.abs(orig.changedTouches[0].pageY - slider.touch.start.y);
            if ((xMovement * 3) > yMovement && slider.settings.preventDefaultSwipeX) {
                e.preventDefault()
            } else if ((yMovement * 3) > xMovement && slider.settings.preventDefaultSwipeY) {
                e.preventDefault()
            }
            var value = 0;

            var change = orig.changedTouches[0].pageX - slider.touch.start.x;
            value = slider.touch.originalPos.left + change

            setPositionProperty(value, 'reset', 0)

        };
        var onTouchEnd = function(e) {

            slider.viewport.unbind('touchmove', onTouchMove);
            var orig = e.originalEvent;
            var value = 0;
            slider.touch.end.x = orig.changedTouches[0].pageX;
            slider.touch.end.y = orig.changedTouches[0].pageY;

            var distance = 0;

            distance = slider.touch.end.x - slider.touch.start.x;
            value = slider.touch.originalPos.left
            if (!slider.settings.infiniteLoop && ((slider.active.index == 0 && distance > 0) || (slider.active.last && distance < 0))) {
                setPositionProperty(value, 'reset', 200)
            } else {
                if (Math.abs(distance) >= slider.settings.swipeThreshold) {
                    distance < 0 ? el.goToNextSlide() : el.goToPrevSlide();

                } else {
                    setPositionProperty(value, 'reset', 200)
                }
            }

            slider.viewport.unbind('touchend', onTouchEnd);
            if (slider.settings.auto) {
                initAuto();
            }
        };

        el.goToSlide = function(slideIndex, direction) {
            if (slider.working || slider.active.index == slideIndex) return;
            slider.working = true;
            slider.oldIndex = slider.active.index;
            if (slideIndex < 0) {
                slider.active.index = getPagerQty() - 1
            } else if (slideIndex >= getPagerQty()) {
                slider.active.index = 0
            } else {
                slider.active.index = slideIndex
            }
            slider.settings.onSlideBefore(slider.children.eq(slider.active.index), slider.oldIndex, slider.active.index);
            if (slider.settings.showBothImg) {
                $(el).prevAll('.js-clone-prev,.js-clone-next').remove();
                $(el).parent('.t-bx__viewport').prev('.t-bx__pager-link').on('click', function() {
                    pagerClick($(this));
                });

            };

            slider.active.last = slider.active.index >= getPagerQty() - 1;
            if (slider.settings.pager) {
                updatePagerActive(slider.active.index)
            };
            var moveBy = 0;
            var position = {
                left: 0,
                top: 0
            };
            if (!slider.settings.infiniteLoop && slider.carousel && slider.active.last) {

                var lastChild = slider.children.eq(slider.children.length - 1);
                position = lastChild.position();
                moveBy = slider.viewport.width() - lastChild.outerWidth()

            } else if (slider.carousel && slider.active.last && direction == 'prev') {

                var eq = slider.settings.moveSlides == 1 ? 1 - getMoveBy() : ((getPagerQty() - 1) * getMoveBy()) - (slider.children.length - 1);
                var lastChild = el.children('.t-bx__clone').eq(eq);
                position = lastChild.position()

            } else if (direction == 'next' && slider.active.index == 0) {
                position = el.find('> .t-bx__clone').eq(1).position();
                slider.active.last = false

            } else if (slideIndex >= 0) {
                var requestEl = slideIndex * getMoveBy();
                position = slider.children.eq(requestEl).position()

            }

            if ("undefined" !== typeof(position)) {
                var value = -(position.left - moveBy);
                setPositionProperty(value, 'slide', slider.settings.speed)
            }
            if (slider.settings.showBothImg) {

                var imgWrap = el.find('> div');
                var elClass = el.attr('class').split(' ')
                elClass = ('.' + elClass[0])

                imgWrap.css({
                        'visibility': 'visible',
                        'margin': '0'
                    })
                    .removeClass('current js-t-nextCurrent js-t-prevCurrent');


                if (slider.settings.infiniteLoop) {
                    imgWrap.eq(slider.active.index + 1).addClass('current');
                } else {
                    imgWrap.eq(slider.active.index).addClass('current');
                }

                var current = el.find('.current');
                var windowW = $(window).width();

                current.css({
                    'text-align': 'center',
                    'width': windowW,
                    'margin': 0,
                    'z-index': 51
                });

                $(current).next('div').addClass('js-t-nextCurrent')
                $(current).prev('div').addClass('js-t-prevCurrent')
                el.find('.js-t-prevCurrent').clone(true).insertBefore(el).attr('class', 'js-clone-prev').removeClass('.js-t-prevCurrent').hide();
                el.find('.js-t-nextCurrent').clone(true).insertBefore(el).attr('class', 'js-clone-next').removeClass('.js-t-nextCurrent').hide();

                var imgCurrentW = el.find('.current img').width();
                var imgPrevW = el.find('.js-t-prevCurrent img').width();
                var imgNextW = el.find('.js-t-nextCurrent img').width();
                var leftPx = -(imgPrevW - 20);
                var rightPx = -(imgNextW - 20);
                var clonePrev = el.prevAll('.js-clone-prev');
                var cloneNext = el.prevAll('.js-clone-next');
                var prevCurrent = el.find('.js-t-prevCurrent');
                var nextCurrent = el.find('.js-t-nextCurrent');

                clonePrev.css({
                    'position': 'absolute',
                    'left': leftPx,
                    'top': 0,
                    'width': 'auto',
                    'display': 'none',
                    'z-index': 0
                });

                cloneNext.css({
                    'position': 'absolute',
                    'right': rightPx,
                    'top': 0,
                    'width': 'auto',
                    'display': 'none',
                    'z-index': 0

                });

                if (direction == 'next') {

                    clonePrev.css('display', 'block');

                    nextCurrent.css({
                        'margin-left': '-20px',
                        'text-align': 'left'
                    });
                } else if (direction == 'prev') {

                    cloneNext.css('display', 'block');

                    windowW = windowW + 20
                    prevCurrent.css({
                        'margin-right': '-20px',
                        'text-align': 'right',
                        'width': windowW + 'px'
                    });
                }

                if (imgCurrentW >= 280) {
                    el.prevAll('.js-clone-prev,.js-clone-next').css('visibility', 'hidden')
                    el.find('.js-t-nextCurrent,.js-t-prevCurrent').css('visibility', 'hidden')
                }

            }

        };
        el.goToNextSlide = function() {
            if (!slider.settings.infiniteLoop && slider.active.last) {
                return
            };
            var pagerIndex = parseInt(slider.active.index) + 1;
            el.goToSlide(pagerIndex, 'next')
        };
        el.goToPrevSlide = function() {
            if (!slider.settings.infiniteLoop && slider.active.index == 0) {
                return
            };
            var pagerIndex = parseInt(slider.active.index) - 1;
            el.goToSlide(pagerIndex, 'prev')
        };
        el.startAuto = function(preventControlUpdate) {
            if (slider.interval) {
                return
            };
            slider.interval = setInterval(function() {
                el.goToNextSlide()
            }, slider.settings.pause);
            if (slider.settings.autoControls && preventControlUpdate != true) {
                updateAutoControls('stop')
            };
        };
        el.stopAuto = function(preventControlUpdate) {
            // if no interval exists, disregard call
            if (!slider.interval) return;
            // clear the interval
            clearInterval(slider.interval);
            slider.interval = null;
            // if auto controls are displayed and preventControlUpdate is not true
            if (slider.settings.autoControls && preventControlUpdate != true) updateAutoControls('start');
        };
        el.getCurrentSlide = function() {
            return slider.active.index
        };
        el.getCurrentSlideElement = function() {
            return slider.children.eq(slider.active.index)
        };
        el.getSlideCount = function() {
            return slider.children.length
        };
        el.redrawSlider = function() {
            slider.children.add(el.find('.t-bx__clone')).width(getSlideWidth());
            slider.viewport.css('height', getViewportHeight());

            if (slider.active.last) {
                slider.active.index = getPagerQty() - 1
            };
            if (slider.active.index >= getPagerQty()) {
                slider.active.last = true
            };
            if (slider.settings.pager) {
                updatePagerActive(slider.active.index)
            }
        };

        function resize() {

            var alignNext = el.find('.js-t-prevCurrent').css('margin-right');
            if (alignNext == '-20px') {
                var diff = -20
            } else {
                diff = 0;
            }

            var windowW = $(window).width();
            var currentNum = el.find('> div').index(el.find('.current'));
            var centerNum = (windowW * currentNum) + diff;
            el.css({
                'transform': 'translate3d(' + -centerNum + 'px, 0, 0)',
                '-webkit-transition': '-webkit-transform 0ms'
            });
            el.find('> div').css({
                'width': windowW,
            });
            el.find('> div').not('.js-t-nextCurrent,.js-t-prevCurrent').css({
                'margin': 0,
            });
        }

        function pagerClick(num) {
            var imgWrap = el.find('> div')
            var thisNum = $(num).data('slide-index');
            imgWrap.removeClass('current js-t-nextCurrent js-t-prevCurrent');
            imgWrap.eq(thisNum).addClass('current');
        }

        init();
        return this
    }
})(jQuery);