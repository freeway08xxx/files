/**
 * PIXTAに関するjs
 *
 * 以下の様な構成となります。
 *
 * pixtaRestApi    : api入り口
 * views           : PIxtaで扱う変数群
 * render          : 描画系の関数
 * factory         : 必要オブジェクトの取り出し処理を外だし
 * setEventHandler : イベントハンドラ
 * その他関数        : regist.js等から呼び出す必要があるためグローバルにて配置
 */
$(window).load(function () {

    var pixtaRestApi = {
        searchImage : function(q){
            var defer = $.Deferred();
            $.ajax({
                type     : 'GET',
                url      : '/my_c3/api/pixta_api/',
                data     : q,
                timeout  : 25000,
                scriptCharset: 'utf-8',
                dataType :'json',
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $('.js-searchPixtaImage').addClass('disabled');
                    render.dispMsg('通信エラーが発生しました。しばらくしてからお試しください。')
                },
                success: function (data) {
                    defer.resolve(data);
                }
            });
            return defer.promise();
        }
    };

    /*
    master変数
     ------------------------------------------ */
    var views = {
        rows          : 140,
        page          : 1,
        refine        : 0,
        type          : 'search',
        search_word   : 'search_word',
        selected_items: [],
        select_max    : 0,
        isLoding      : false,

        tpl : {
            column    : {
                start : '<div class="js-column column">',
                end   : '</div>'
            },
            item      : '<div class="js-item item {%active}" data-item_id="{%id}"> ' +
                            '<span class="item--image" style="background-image:url({%thumbnailImages})"></span>' +
                            '<span class="item--title">{%title}</span>' +
                        '</div>',

            readmore  : '<div class="readmore">' +
                            '<div class="js-loading loading"></div>' +
                            '<a class="js-readmore" data-search_word="{%search_word}" data-page="{%page}" href="#">もっと見る</a>' +
                        '</div>',

            loading   : '<div class="js-loading loading" style="display: block"></div>',

            detail    : '<div class="js-detail detail">' +
                            '<span class="item--image" style=' +"'"+ 'background-image:{%bg_image}'+"'"+'></span>' +
                            '<a class="js-closeDetail close" href="#"></a>' +
                            '<div class="item--info">' +
                                '<span class="item--title">{%title}</span>' +
                                '<a href="#" class="js-select btn-select {%remove}" data-item_id="{%id}">この画像を選択</a>' +
                                '<div class="js-detail--msg detail--msg">この画像をPR TIMES以外（コーポレートサイトやその他の用途）で掲載・利用する際は以下URLからご購入ください。<a class="pixta_url" href="https://pixta.jp/photo/{%id}" target="_blank">https://pixta.jp/photo/{%id}</a></div>' +
                                '<div class="js-err_max err_max" style="display:none;">画像の選択数が上限に達しました。</div>' +
                            '</div>' +
                        '</div>'
        }
    };

    /*
     描画関数
     ------------------------------------------ */
    var render = {

        /**
         * 検索画像の描画
         *
         */
        insertImage : function (data) {
            //console.log(data)
            var $render_block = $('.js-items');
            var item          = views.tpl.column.start;

            $.each(data, function (i, obj) {
                if (_.isNull(obj.title)) obj.title = '';

                //4つごとにwrap 開始タグ
                if( (i % 4) == 0 && i != 0) item +=  views.tpl.column.start;
                item += views.tpl.item.replace(/{%thumbnailImages}/g, obj.thumbnailImages.medium.url)
                                      .replace(/{%title}/g, obj.title)
                                      .replace(/{%id}/g, obj.id);
                //選択済みチェック
                item = (!factory.isMylist() && factory.hasElm(views.selected_items, {id : obj.id})) ? item.replace(/{%active}/g, 'active') : item.replace(/{%active}/g, '');
                //4つごとにwrap 閉じタグ
                if( (i - 1) % 4 == 2) item += views.tpl.column.end;

            });
            $render_block.append(item);
            views.page++;

            //もっと見るボタン表示
            if (data.length == views.rows) {
                var readmore = views.tpl.readmore.replace(/{%page}/g, views.page)
                                                 .replace(/{%search_word}/g, views.search_word);
                $render_block.append(readmore);
            }

            //選択済みを表示
            if (factory.isMylist()) $('.js-item').addClass('selected');
        },

        /**
         * 絞り込み
         *
         */
        setRefine : function () {
            pixtaRestApi.searchImage({type: 'category'}).then(function (data) {
                $('.js-refine option').not(':first').remove();
                var $render_block = $('.js-refine');

                $.each(data.categories.photo, function (i, obj) {
                    var html = '<option value="' + obj.id + '">' + obj.name + '</option>';
                    $render_block.append(html);
                });

            });
        },

        /**
         * 詳細の表示、非表示
         *
         */
        closeDetail : function () {
            $('.js-detail').removeClass('open');
        },

        openDetail : function(){
            $('.js-detail').addClass('open');
        },


        /**
         * 画像DLローディング
         *
         */
        downloadLoading : function(){
            $.each(views.selected_items, function (i, val) {
                $(".toolarea.images #thumb" + (imageid + i + 1) + " .imgfl").css({"background-color":"#fff", "background-image":"url(/common/v4/company/images/imageloading.gif)"});
            });
        },

        /**
         * 削除 画像DLローディング
         *
         */
        removeLoading : function(){
            $.each(views.selected_items, function (i, val) {
                $(".toolarea.images #thumb" + (imageid + i + 1) + " .imgfl").css({"background-color":"#FAFAFA", "background-image":"url(/common/v4.1/images/company/imageicon.png)"});
            });
        },


        /**
         * サムネイルエリアのアニメーション
         * 最新画像を一番上にアニメーション
         */
        animateThumbArea : function(){
            var $thumbswrapper = $(".thumbswrapper");
            $thumbswrapper.animate({scrollTop:$(".toolarea.images #thumb" + parseInt(imageid + 1)).position().top - $thumbswrapper.position().top + $thumbswrapper.scrollTop() + "px"}, 300, "easeOutQuad");
        },

        /**
         * 画像ダウンロード
         *
         */
        downloadImage : function(){
            if(views.selected_items.length == 0) return false;
            $('.js-pixta_close').click();
            render.downloadLoading();
            var q = {
                company_id : $('#releseregistform input[name=company_id]').val(),
                type       : 'download',
                ids        : _.pluck(views.selected_items, 'id').join(',')
            };

            pixtaRestApi.searchImage(q).then(function (data) {
                if(data.status == 200) {
                    render.animateThumbArea();
                    $('.registblk').fadeOut();
                    $('.js-pixta_block').fadeOut(100, 'easeInSine',function(){
                        views.selected_items = [];
                        factory.resetPixta();
                        render.removePixta();
                    });

                    $.each(data.images, function (id, obj) {
                        var image = factory.getFileinfo(obj.path);
                        var file = {
                            _identify : '',
                            error     : '',
                            pixta_id  : id,
                            extension : image['extension'],
                            filename  : image['name'],
                            origin    : image['name'],
                            width     : obj['width'],
                            height    : obj['height'],
                            path      : obj['path']
                        };

                        //@ c.js
                        _msg_success( image['name'] + ' をアップロードしました。' );
                        fileuploaded(0, file, 'image', '.thumbs');
                    });


                }else{
                    render.removeLoading();
                    render.removePixta();
                    render.dispMsg('ダウンロードに失敗しました。しばらくしてからお試しください。');
                }
            });
        },

        /**
         * 画面をリセット
         *
         */
        removePixta : function () {
            $('body').css({'height' : 'auto' ,'overflow' : 'visible'});
            $('.js-column,.readmore,.js-detail').remove();
            $('.js-selected_items').text(views.selected_items.length);
            $('.js-pixta_block input[name=search_word]').val('');
            $('.js-pixta_block [name=refine]').val(0);
            $('.js-items').removeClass('mylist');
            render.controlSearchMsg();
        },

        /**
         * ダウンロードボタン有効無効制御
         *
         */
        controlDlDisable : function () {
            views.selected_items.length > 0 ? $('.js-downloadPixta').removeClass('disabled') : $('.js-downloadPixta').addClass('disabled');
        },

        /**
         * 検索メッセージ表示非表示
         *
         */
        controlSearchMsg : function () {
            var isNoItem  = $('.js-item').size() == 0;
            if(factory.isMylist()){
                isNoItem ? render.dispMsg('選択された画像はありません。') : render.dispMsg('選択された画像一覧');
            }else{
                isNoItem ? render.dispMsg('必要な画像を検索してください。') : $('.js-item_msg').text('').hide();
            }
        },

        /**
         * エラーメッセージ
         *
         */
        dispMsg : function (msg) {
            $('.js-item_msg').text(msg).show()
        },


        setOriginSize :function(){
            $iframeobj = $("#cke_1_contents iframe").contents();
            $iframeobj.find("img").each(function () {
                getOriginSize($(this).attr('src')).then(function (img) {
                    if($(this).attr("data-nwidth")  == 'undefined') $(this).attr("data-nwidth",img.width);
                    if($(this).attr("data-nheight") == 'undefined') $(this).attr("data-nheight",img.height);
                });

            });
        }
    };


    /*
     オブジェクト操作
     ------------------------------------------ */
    var factory = {
        getImageData : function(){
            var word    = $('.js-pixta_block input[name=search_word]').val();
            var refine  = $('.js-pixta_block [name=refine]').val();


            if( views.search_word == word && views.refine == refine && !factory.isMylist()) return false;

            $('.js-column').remove();
            $('.readmore').remove();
            $('.js-items').removeClass('mylist').append(views.tpl.loading);

            views.page        = 1;
            views.refine      = refine;
            views.search_word = word;
            views.type        = (views.refine != '') ? 'refine' : 'search';

            pixtaRestApi.searchImage(views).then(function (data) {
                //console.log(data)

                $('.js-loading').remove();
                if(data.items.length != 0){
                    render.insertImage(data.items);
                    $('.js-item_msg').text('').hide();
                }else{
                    $('.js-item_msg').text('該当画像はありません。キーワード又はカテゴリを変えてお試しください。').show();
                }
            });
        },
        countMax : function(){
            var cnt = 0;
            var max = 20;
            $('.toolarea.images .img_holder').each(function(i, elm){
                if($(this).find('img').size() != 0) cnt++;
            });
            return max - cnt;
        },
        resetPixta : function(){
            views.page           = 1;
            views.refine         = 0;
            views.search_word    = 'search_word';
        },

        //idが一致したオブジェクトを配列から削除
        deleteObj : function(arr,id){
            var res = [];
            $.each(arr, function (i, obj) {
                if(obj['id'] != id) res.push(obj);
            });
            return res;
        },

        //idが一致したらオブジェクトが含まれていればtrueを返す
        hasElm : function(json,elm){
            var bool = false;
            $.each(json, function (i, obj) {
                if(obj.id ==  elm.id ) bool = true;
            });
            return bool;
        },

        /**
         * pathから画像名,拡張子を返す
         * return string
         */
        getFileinfo : function(path,elm){
            var name      = path.split('/').pop();
            var extension = name.split('.');
            var result    = {
                name      : name,
                extension : extension[extension.length-1].toLowerCase()
            };
            return result;
        },

        /**
         * 選択された画面を開いている
         * return boolean
         *
         */
        isMylist : function(){
            return $('.js-items').hasClass('mylist') ? true:false;
        }

    };


    /*
     * イベントハンドラ
     --------------------------------------------------------------*/
    var setEventHandler = function(){

        //PIXTAブロックを表示
        $('.js-showPixtaBlock').on('click', function () {
            render.setRefine();
            $('.js-searchPixtaImage').removeClass('disabled');
            $('.registblk').fadeIn(150, "easeInSine");
            $('.js-pixta_block').css('min-height', $(window).height() ).delay(150).fadeIn(200, "easeInSine");
            $('body').css({'height' : $(window).height() , 'overflow' : 'hidden'});
            $('.js-select_max').text(factory.countMax());
            render.controlDlDisable();
            render.controlSearchMsg();
            return false;
        });

        //閉じる
        $('.js-pixta_close').on('click' , function (evt) {
            $('.js-pixta_block').fadeOut();
            $('.registblk').fadeOut();
            $('body').css({'height' : 'auto' , 'overflow' : 'visible'});
            setRegistResize();
        });

        //ダウンロードボタン
        $('.js-downloadPixta').on('click' , function (evt) {
            render.downloadImage();
        });


        //検索
        $('.js-searchPixtaImage').on('click', function () {
            $('.js-detail').remove();
            factory.getImageData();
            return false;
        });

        //enterで検索
        $('.js-search_word').on('keydown', function (e) {
            var evt = e || window.event;
            if(evt.keyCode == 13) $('.js-searchPixtaImage').click();
        });


        //絞り込み枠 変更
        $('.js-refine').on('change', function () {
            $('.js-detail').remove();
            factory.getImageData();
            return false;
        });


        //もっと見る
        $(document).on('click', '.js-readmore',function () {
            var q = {
                type        : views.type,
                search_word : $(this).data('search_word'),
                page        : $(this).data('page'),
                rows        : views.rows,
                refine      : views.refine,
                type        : views.type
            };

            $(this).hide();
            $('.js-loading').show();

            pixtaRestApi.searchImage(q).then(function (data) {
                $('.readmore').remove();
                if(data != null) render.insertImage(data.items);
            });
            return false;
        });


        //item クリック
        $(document).on({
            "click": function(){
                $('.js-item').not($(this)).removeClass('select');
                $(this).removeClass('hover').toggleClass('select');

                if($(this).hasClass('select') ){
                    //詳細表示
                    $('.js-select_max').text(factory.countMax());
                    $('.js-detail').remove();

                    var remove   = factory.isMylist() ? 'js-remove unselect': '';
                    var id       = $(this).data('item_id');
                    var img      = $(this).find('.item--image').css('background-image');
                    var title    = $(this).find('.item--title').text();
                    var html     = views.tpl.detail.replace(/{%id}/g, id)
                                                   .replace(/{%bg_image}/g, img)
                                                   .replace(/{%title}/g, title)
                                                   .replace(/{%remove}/g, remove);

                    $(this).parent('.js-column').after(html);

                    $(html).ready(function() {
                        if(factory.isMylist()) $('.js-select').text('削除');

                        //最大数を超えたらエラー文表示
                        if(factory.countMax() <= views.selected_items.length && !factory.isMylist()){
                            $('.js-select').addClass('disabled');
                            $('.js-err_max').show();
                            $('.js-detail--msg').hide();
                        }
                        render.openDetail();
                    });

                }else{
                    render.closeDetail();
                }

                return false;
            }
        }, '.js-item');


        //詳細画面 選択クリック
        $(document).on('click', '.js-select',function () {
            if($(this).hasClass('js-unselect')) return false;
            if($(this).hasClass('js-remove')) return false;

            $(this).addClass('js-unselect unselect').text('選択を解除');
            var id    = $(this).data('item_id');
            var img   = $(this).parents('.js-detail').find('.item--image').css('background-image').match(/https?:\/\/[-_.!~*'()a-zA-Z0-9;\/?:@&=+$,%#]+[a-z]/g)
            var title = $(this).parents('.js-detail').find('.item--title').text();

            $('.js-item[data-item_id ="'+ id +'"]').removeClass('select').addClass('active');

            //オブジェクトに追加
            views.selected_items.push({
                id              : id,
                thumbnailImages : {medium : {url : img}},
                title           : title
            });

            //現在選択されている数を反映
            $('.js-selected_items').text(views.selected_items.length);
            render.controlDlDisable();
            return false;
        });


        //詳細画面 選択解除クリック
        $(document).on('click', '.js-unselect',function () {
            $(this).removeClass('js-unselect unselect').text('この画像を選択');
            var id  = $(this).data('item_id');

            $('.js-item[data-item_id ="'+ id +'"]').addClass('select').removeClass('active');

            //配列から削除
            views.selected_items = factory.deleteObj(views.selected_items,id);
            render.controlDlDisable();
            //現在選択されている数を反映
            $('.js-selected_items').text(views.selected_items.length);
            return false;
        });


        //詳細画面 削除クリック -mylist-
        $(document).on('click', '.js-remove',function () {
            var id  = $(this).data('item_id');

            //配列から削除
            views.selected_items = factory.deleteObj(views.selected_items,id);

            //現在選択されている数を反映
            $('.js-selected_items').text(views.selected_items.length);

            //要素を削除
            $('.js-item[data-item_id ="'+ id +'"]').fadeOut(300,function(){
                $(this).remove();
                render.controlDlDisable();
                render.controlSearchMsg();
            });
            $('.js-detail').fadeOut(300,function(){
                $(this).remove();
                $('.js-item').unwrap('.js-column');
                //4つごとにwrap
                do {
                    $('.js-items').children('.js-item:lt(4)').wrapAll('<div class="js-column column"></div>')
                }while($('.js-items').children('.js-item').length);
            });
            return false;
        });


        //詳細画面 閉じる
        $(document).on('click', '.js-closeDetail',function () {
            $('.js-item').removeClass('select');
            render.closeDetail();
            return false;
        });


        //キャプションクリック
        $(document).on('click', '.js-copyright-block',function () {
            $iframeobj = $("#cke_1_contents iframe").contents();
            var _imageid = $(this).attr('id').substr(3);
            $iframeobj.find("img#"+_imageid).mousedown().click();
            return false;
        });


        //選択したリストの一覧を表示
        $(document).on('click', '.js-showMyList',function () {
            $('.js-column,.js-detail,.readmore').remove();
            $('.js-items').addClass('mylist');
            render.insertImage(views.selected_items);
            render.controlSearchMsg();
            return false;
        });

        //pixtaへのリンク
        $(document).on('click', '.js-toPixta',function () {
            var url = $(this).attr('href');
            window.open( url, 'new' );
            return false;
        });


        //直接入力にて画像サイズを変えた場合
        $(document).on('keyup', 'input[name="imagewidth"],input[name="imageheight"]',function () {
            setPixtaCopyright()
            return false;
        });
    };


    /*
     * onload
     --------------------------------------------------------------*/
    setEventHandler();

});



/*
 * pixta グローバル関数
 --------------------------------------------------------------*/
var timerSetPixtaCopyright = false;
function setPixtaCopyright() {
    timerSetPixtaCopyright = setTimeout(function() {
        if (timerSetPixtaCopyright) clearTimeout(timerSetPixtaCopyright);

        $(".registmain .dummy_pixta_copyright div").remove();
        $('.js−pixta_msg').hide();

        $iframeobj = $("#cke_1_contents iframe").contents();
        $iframeobj.find("img").each(function () {
            $(this).css({"opacity": "1"});
            if($(this).attr('src').indexOf('pixta') != -1 ) {
                var id       = $(this).attr("id");
                var pixta_id = _getPixtaId($(this).attr('src'));
                var html = '<div class="js-copyright-block copyright-block" id="ca-' + id + '">' +
                    '<span class="copyright-text"><a class="js-toPixta" target="_blank" href="https://pixta.jp/photo/'+pixta_id+'">photo by pixta.jp</a></span>'
                '</div>';

                $(".registmain .bodytext .dummy_pixta_copyright").append(html);
                $("#ca-" + id).css({
                    "width"            : $(this).width() + "px",
                    "max-height"       : $(this).height() + "px",
                    "left"             : $(this).position().left + 3 + "px",
                    "top"              : $(this).position().top + $(this).height() + 10 + "px",
                    "margin-left"      : $(this).css("margin-left"),
                    "margin-right"     : $(this).css("margin-right"),
                    "position"         : "absolute"
                });

                $('.js−pixta_msg').show();
            }
        });

        function _getPixtaId(url){
            var positionStrPixta    = url.indexOf('pixta_') + 6;
            var pixta_id = url.slice(positionStrPixta , url.indexOf('.',positionStrPixta) ).split('.');
            if(pixta_id[0].indexOf('-') != -1 ) pixta_id = pixta_id[0].split('-')
            return pixta_id[0];
        };

    }, 200);

}

function chkImageSelected(){
    var pid    = "";
    setTimeout(function(){
        pid = $(".registmain .dummyimage > div").attr('id');
        if(typeof pid != 'undefined'){
            pid = pid.slice(3);
            $iframeobj = $("#cke_1_contents iframe").contents();
            $iframeobj.find('#'+pid).css({'opacity': '0'});
        }
    }, 200);
}

/**
 * 画像のオリジナルサイズを返却
 * onloadを待ってレスポンス
 */
function getOriginSize(src) {
    var d = $.Deferred();
    var img = new Image;
    img.src= src;
    img.onload =function(){
        d.resolve(img)
    };
    return d.promise();
};

/**
 * pixta画像のコピーライトを配置
 *
 */
function setPixtaImageCopyright($elm){
    if(typeof $elm != 'undefined'){
        $elm.each(function(){
            if ($(this).attr('src').indexOf('pixta') > -1 ) {
                var width = ($(this).width() == 0) ? 'auto' : $(this).width() - 4 + 'px';
                $(this).after('<div class="image-caption pixta-copyright" style="width: '+ width +';"><a href="javascript:void(0);">photo by pixta.jp</a></div>')
            }
        });
    }

    $iframeobj = $("#cke_1_contents iframe").contents();
    $iframeobj.find("img").each(function () {
        if ($(this).attr('src').indexOf('pixta') > -1 ) {
            $(this).parent('.no-edit').addClass('pixta-image');
        }
    });
}